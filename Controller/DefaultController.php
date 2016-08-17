<?php

namespace MWSimple\Bundle\AdminCrudBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Exporter\Source\DoctrineORMQuerySourceIterator;
use Exporter\Handler;

/**
 * MWSimpleAdminCrudBundle Default controller.
 * @author Gonzalo Alonso <gonkpo@gmail.com>
 */
class DefaultController extends Controller
{
    /* Configuration file. */
    protected $config = array();
    protected $configArray = array();
    /* Entity. */
    protected $entity;
    /* Entity Manager. */
    protected $em;

    /**
     * Index
     */
    public function indexAction(Request $request)
    {
        $this->getConfig();
        list($filterForm, $queryBuilder) = $this->filter($request);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->get('page', 1),
            ($this->container->hasParameter('knp_paginator.page_range')) ? $this->container->getParameter('knp_paginator.page_range'):10
        );
        //remove the form to return to the view
        unset($this->configArray['filterType']);

        return $this->render($this->configArray['view_index'], array(
            'config'     => $this->configArray,
            'entities'   => $pagination,
            'filterForm' => $filterForm->createView(),
        ));
    }

    /**
     * Create query.
     * @param string $repository
     * @return Doctrine\ORM\QueryBuilder $queryBuilder
     */
    protected function createQuery($repository)
    {
        $this->em = $this->getDoctrine()->getManager();
        $queryBuilder = $this->em->getRepository($repository)
            ->createQueryBuilder('a')
            ->orderBy('a.id', 'DESC')
        ;

        return $queryBuilder;
    }

    /**
     * Export Csv.
     */
    public function exportCsvAction($format)
    {
        $this->getConfig();
        $query  = $this->createQuery($this->configArray['repository'])->getQuery();
        $campos = $this->getCampos();
        $content_type = $this->getContentType($format);
        // Location to Export this to
        $export_to = 'php://output';
        // Data to export
        $exporter_source = new DoctrineORMQuerySourceIterator($query, $campos, "Y-m-d H:i:s");
        // Get an Instance of the Writer
        $exporter_writer = '\Exporter\Writer\\' . ucfirst($format) . 'Writer';
        $exporter_writer = new $exporter_writer($export_to);
        // Generate response
        $response = new Response();
        // Set headers
        $response->headers->set('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
        $response->headers->set('Content-type', $content_type);
        $response->headers->set('Expires', 0);
        $response->headers->set('Pragma', 'public');
        // Send headers before outputting anything
        $response->sendHeaders();
        // Export to the format
        Handler::create($exporter_source, $exporter_writer)->export();

        return $response;
    }

    protected function getCampos()
    {
        $campos = array();
        foreach ($this->configArray['fieldsindex'] as $key => $value) {
            //if is defined and true
            if (!empty($value['export']) && $value['export']) {
                $campos[] = $value['name'];
            }
        }

        return $campos;
    }

    protected function getContentType($format)
    {
        // Pick a format to export to
        // Set Content-Type
        switch ($format) {
            case 'xls':
                $content_type = 'application/vnd.ms-excel';
                break;
            case 'json':
                $content_type = 'application/json';
                break;
            case 'csv':
                $content_type = 'text/csv';
                break;
            default:
                $content_type = 'text/csv';
                break;
        }

        return $content_type;
    }

    /**
     * Process filter request.
     * @return array
     */
    protected function filter(Request $request)
    {
        $session = $request->getSession();
        $filterForm = $this->createFilterForm();
        $queryBuilder = $this->createQuery($this->configArray['repository']);
        // Bind values from the request
        $filterForm->handleRequest($request);
        // Reset filter
        if ($filterForm->get('reset')->isClicked()) {
            $session->remove($this->configArray['sessionFilter']);
            $filterForm = $this->createFilterForm();
        }

        // Filter action
        if ($filterForm->get('filter')->isClicked()) {
            if ($filterForm->isValid()) {
                // Build the query from the given form object
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
                // Save filter to session
                $filterData = $request->get($filterForm->getName());
                $session->set($this->configArray['sessionFilter'], $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has($this->configArray['sessionFilter'])) {
                $filterData = $session->get($this->configArray['sessionFilter']);
                $filterForm->submit($filterData);
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
            }
        }

        return array($filterForm, $queryBuilder);
    }

    /**
     * Create filter form.
     * @param $filterData
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createFilterForm($filterData = null)
    {
        $form = $this->createForm($this->configArray['filterType'], $filterData, array(
            'action' => $this->generateUrl($this->configArray['index']),
            'method' => 'GET',
        ));

        $form
            ->add('filter', SubmitType::class, array(
                'translation_domain' => 'MWSimpleAdminCrudBundle',
                'label'              => 'views.index.filter',
                'attr'               => array(
                    'class' => 'form-control btn-success',
                    'col'   => 'col-lg-2',
                ),
            ))
            ->add('reset', SubmitType::class, array(
                'translation_domain' => 'MWSimpleAdminCrudBundle',
                'label'              => 'views.index.reset',
                'attr'               => array(
                    'class' => 'form-control reset_submit_filters btn-danger',
                    'col'   => 'col-lg-2',
                ),
            ))
        ;

        return $form;
    }

    /**
     * Create
     */
    public function createAction(Request $request)
    {
        $this->getConfig();
        $this->createNewEntity();
        $form = $this->createCreateForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->em = $this->getDoctrine()->getManager();
            $this->persistEntity();
            $this->em->flush();
            //Crear ACL
            $this->useACL('create');
            //Set session mensaje
            $this->get('session')->getFlashBag()->add('success', 'flash.create.success');

            if ($this->configArray['saveAndAdd']) {
                $nextAction = $form->get('saveAndAdd')->isClicked()
                ? $this->generateUrl($this->configArray['new'])
                : $this->generateUrl($this->configArray['show'], array('id' => $this->entity->getId()));
            } else {
                $nextAction = $this->generateUrl($this->configArray['show'], array('id' => $this->entity->getId()));
            }

            return $this->redirect($nextAction);
        }

        $this->get('session')->getFlashBag()->add('danger', 'flash.create.error');

        // remove the form to return to the view
        unset($this->configArray['newType']);

        return $this->render($this->configArray['view_new'], array(
            'config' => $this->configArray,
            'entity' => $this->entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a entity.
    * @return \Symfony\Component\Form\Form The form
    */
    protected function createCreateForm()
    {
        $form = $this->createForm($this->configArray['newType'], $this->entity, array(
            'action' => $this->generateUrl($this->configArray['create']),
            'method' => 'POST',
        ));

        $form
            ->add('save', SubmitType::class, array(
                'translation_domain' => 'MWSimpleAdminCrudBundle',
                'label'              => 'views.new.save',
                'attr'               => array(
                    'class' => 'form-control btn-success',
                    'col'   => 'col-lg-2',
                )
            ))
        ;

        if ($this->configArray['saveAndAdd']) {
            $form
                ->add('saveAndAdd', SubmitType::class, array(
                    'translation_domain' => 'MWSimpleAdminCrudBundle',
                    'label'              => 'views.new.saveAndAdd',
                    'attr'               => array(
                        'class' => 'form-control btn-primary',
                        'col'   => 'col-lg-3',
                    )
                ))
            ;
        }

        return $form;
    }

    /**
     * New
     */
    public function newAction()
    {
        $this->getConfig();
        $this->createNewEntity();
        $form = $this->createCreateForm();

        // remove the form to return to the view
        unset($this->configArray['newType']);

        return $this->render($this->configArray['view_new'], array(
            'config' => $this->configArray,
            'entity' => $this->entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Query Entity
     * @param $id
     */
    protected function queryEntity($id)
    {
        $this->em = $this->getDoctrine()->getManager();

        $this->entity = $this->em->getRepository($this->configArray['repository'])->find($id);
    }

    /**
     * Show
     * @param $id
     */
    public function showAction($id)
    {
        $this->getConfig();

        $this->queryEntity($id);

        if (!$this->entity) {
            throw $this->createNotFoundException('Unable to find '.$this->configArray['entityName'].' entity.');
        }
        $this->useACL('show');
        $deleteForm = $this->createDeleteForm($id);

        return $this->render($this->configArray['view_show'], array(
            'config'      => $this->configArray,
            'entity'      => $this->entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edit
     * @param $id
     */
    public function editAction($id)
    {
        $this->getConfig();

        $this->queryEntity($id);

        if (!$this->entity) {
            throw $this->createNotFoundException('Unable to find '.$this->configArray['entityName'].' entity.');
        }
        $this->useACL('edit');
        $form = $this->createEditForm();
        $deleteForm = $this->createDeleteForm($id);

        // remove the form to return to the view
        unset($this->configArray['editType']);

        return $this->render($this->configArray['view_edit'], array(
            'config'      => $this->configArray,
            'entity'      => $this->entity,
            'form'        => $form->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a entity.
    * @return \Symfony\Component\Form\Form The form
    */
    protected function createEditForm()
    {
        $form = $this->createForm($this->configArray['editType'], $this->entity, array(
            'action' => $this->generateUrl($this->configArray['update'], array('id' => $this->entity->getId())),
            'method' => 'PUT',
        ));

        $form
            ->add('save', SubmitType::class, array(
                'translation_domain' => 'MWSimpleAdminCrudBundle',
                'label'              => 'views.new.save',
                'attr'               => array(
                    'class' => 'form-control btn-success',
                    'col'   => 'col-lg-2',
                )
            ))
        ;

        if ($this->configArray['saveAndAdd']) {
            $form
                ->add('saveAndAdd', SubmitType::class, array(
                    'translation_domain' => 'MWSimpleAdminCrudBundle',
                    'label'              => 'views.new.saveAndAdd',
                    'attr'               => array(
                        'class' => 'form-control btn-primary',
                        'col'   => 'col-lg-3',
                    )
                ))
            ;
        }

        return $form;
    }

    /**
     * Update
     * @param $id
     */
    public function updateAction(Request $request, $id)
    {
        $this->getConfig();

        $this->queryEntity($id);

        if (!$this->entity) {
            throw $this->createNotFoundException('Unable to find '.$this->configArray['entityName'].' entity.');
        }
        $this->useACL('update');
        $deleteForm = $this->createDeleteForm($id);
        $form = $this->createEditForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.update.success');

            if ($this->configArray['saveAndAdd']) {
                $nextAction = $form->get('saveAndAdd')->isClicked()
                    ? $this->generateUrl($this->configArray['new'])
                    : $this->generateUrl($this->configArray['show'], array('id' => $id));
            } else {
                $nextAction = $this->generateUrl($this->configArray['show'], array('id' => $id));
            }
            return $this->redirect($nextAction);
        }

        $this->get('session')->getFlashBag()->add('danger', 'flash.update.error');

        // remove the form to return to the view
        unset($this->configArray['editType']);

        return $this->render($this->configArray['view_edit'], array(
            'config'      => $this->configArray,
            'entity'      => $this->entity,
            'form'        => $form->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Delete
     * @param $id
     */
    public function deleteAction(Request $request, $id)
    {
        $this->getConfig();
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository($this->configArray['repository'])->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find '.$this->configArray['entityName'].' entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.delete.success');
        }

        return $this->redirectToRoute($this->configArray['index']);
    }

    /**
     * Creates a form to delete a Post entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createDeleteForm($id)
    {
        $mensaje = $this->get('translator')->trans('views.recordactions.confirm', array(), 'MWSimpleAdminCrudBundle');
        $onclick = 'return confirm("'.$mensaje.'");';
        return $this->createFormBuilder()
            ->setAction($this->generateUrl($this->configArray['delete'], array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, array(
                'translation_domain' => 'MWSimpleAdminCrudBundle',
                'label'              => 'views.recordactions.delete',
                'attr'               => array(
                    'class'   => 'form-control btn-danger',
                    'onclick' => $onclick,
                )
            ))
            ->getForm()
        ;
    }

    protected function getConfig()
    {
        $configs = Yaml::parse(file_get_contents($this->get('kernel')->getRootDir().'/../src/'.$this->config['yml']));
        foreach ($configs as $key => $value) {
            $this->configArray[$key] = $value;
        }
        foreach ($this->config as $key => $value) {
            if ($key != 'yml') {
                $this->configArray[$key] = $value;
            }
        }
        //Set opcion saveAndAdd en la configuracion
        $this->setSaveAndAdd();
    }

    public function getAutocompleteFormsMwsAction(Request $request, $options, $qb = null)
    {
        $term = $request->query->get('q', null);

        if (is_null($qb)) {
            $em = $this->getDoctrine()->getManager();

            $qb = $em->getRepository($options['repository'])->createQueryBuilder('a');
            $qb
                ->where("a.".$options['field']." LIKE :term")
                ->orderBy("a.".$options['field'], "ASC")
            ;
        }
        $qb->setParameter("term", "%" . $term . "%");
        $entities = $qb->getQuery()->getResult();

        $array = array();

        foreach ($entities as $entity) {
            $array[] = array(
                'id'   => $entity->getId(),
                'text' => $entity->__toString(),
            );
        }

        $response = new JsonResponse();
        $response->setData($array);

        return $response;
    }

    protected function useACL($action)
    {
        $aclConf = $this->container->hasParameter('mw_simple_admin_crud.acl') ?
            $this->container->getParameter('mw_simple_admin_crud.acl') : null;

        if ($aclConf['use']) {
            if ($this->isInstanceOf($this->entity, $aclConf['entities'])) {
                $aclManager = $this->container->get('mws_acl_manager');
                switch ($action) {
                    case 'create':
                        $aclManager->createACL($this->entity);
                        break;
                    case 'show':
                        $aclManager->controlACL($this->entity, 'VIEW', $aclConf['exclude_role']);
                        break;
                    case 'edit':
                        $aclManager->controlACL($this->entity, 'EDIT', $aclConf['exclude_role']);
                        break;
                    case 'update':
                        $aclManager->controlACL($this->entity, 'EDIT', $aclConf['exclude_role']);
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
    }

    protected function isInstanceOf($object, Array $classnames)
    {
        foreach ($classnames as $classname) {
            if ($object instanceof $classname) {
                return true;
            }
        }
        return false;
    }

    //FUNCIONES VARIAS
    protected function setSaveAndAdd()
    {
        if (!array_key_exists('saveAndAdd', $this->configArray)) {
            $this->configArray['saveAndAdd'] = true;
        } elseif ($this->configArray['saveAndAdd'] !== false) {
            $this->configArray['saveAndAdd'] = true;
        }
    }

    /**
     * @return object
     */
    protected function createNewEntity()
    {
        $this->entity = new $this->configArray['entity']();
    }

    /**
     * @return object
     */
    protected function persistEntity()
    {
        $this->em->persist($this->entity);
    }
}
