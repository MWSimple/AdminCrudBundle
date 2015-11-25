<?php

namespace MWSimple\Bundle\AdminCrudBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Exporter\Source\DoctrineORMQuerySourceIterator;
use Exporter\Handler;

/**
 * MWSimpleAdminCrudBundle Default controller.
 * @author Gonzalo Alonso <gonkpo@gmail.com>
 */
class DefaultController extends Controller
{
    /**
     * Configuration file.
     */
    protected $config = array();

	/**
     * Index
     */
    public function indexAction()
    {
        $config = $this->getConfig();
        list($filterForm, $queryBuilder) = $this->filter($config);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $queryBuilder,
            $this->get('request')->query->get('page', 1),
            ($this->container->hasParameter('knp_paginator.page_range')) ? $this->container->getParameter('knp_paginator.page_range'):10
        );
        //remove the form to return to the view
        unset($config['filterType']);

        return array(
        	'config'     => $config,
            'entities'   => $pagination,
            'filterForm' => $filterForm->createView(),
        );
    }

    /**
     * Create query.
     * @param string $repository
     * @return Doctrine\ORM\QueryBuilder $queryBuilder
     */
    protected function createQuery($repository)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository($repository)
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
        $config = $this->getConfig();
        $queryBuilder = $this->createQuery($config['repository']);
        $campos = array();
        foreach ($config['fieldsindex'] as $key => $value) {
            //if is defined and true
            if (!empty($value['export']) && $value['export']) {
                $campos[] = $value['name'];
            }
        }
        $query = $queryBuilder->getQuery();
        // Pick a format to export to
        //$format = 'csv';
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
        //$response->headers->set('Content-length', filesize($filename));
        $response->headers->set('Pragma', 'public');
        // Send headers before outputting anything
        $response->sendHeaders();
        // Export to the format
        Handler::create($exporter_source, $exporter_writer)->export();

        return $response;
    }

    /**
     * Process filter request.
     * @param array $config
     * @return array
     */
    protected function filter($config)
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $filterForm = $this->createFilterForm($config);
        $queryBuilder = $this->createQuery($config['repository']);
        // Bind values from the request
        $filterForm->handleRequest($request);
        // Reset filter
        if ($filterForm->get('reset')->isClicked()) {
            $session->remove($config['sessionFilter']);
            $filterForm = $this->createFilterForm($config);
        }

        // Filter action
        if ($filterForm->get('filter')->isClicked()) {
            if ($filterForm->isValid()) {
                // Build the query from the given form object
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
                // Save filter to session
                $filterData = $request->get($filterForm->getName());
                $session->set($config['sessionFilter'], $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has($config['sessionFilter'])) {
                $filterData = $session->get($config['sessionFilter']);
                $filterForm->submit($filterData);                
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
            }
        }

        return array($filterForm, $queryBuilder);
    }

    /**
     * Create filter form.
     * @param array $config
     * @param $filterData
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createFilterForm($config, $filterData = null)
    {
        $form = $this->createForm($config['filterType'], $filterData, array(
            'action' => $this->generateUrl($config['index']),
            'method' => 'GET',
        ));

        $form
            ->add('filter', 'submit', array(
                'translation_domain' => 'MWSimpleAdminCrudBundle',
                'label'              => 'views.index.filter',
                'attr'               => array(
                    'class' => 'form-control btn-success',
                    'col'   => 'col-lg-2',
                ),
            ))
            ->add('reset', 'submit', array(
                'translation_domain' => 'MWSimpleAdminCrudBundle',
                'label'              => 'views.index.reset',
                'attr'               => array(
                    'class' => 'form-control reset_submit_filters btn-danger',
                    'col'   => 'col-lg-2 col-lg-offset-1',
                ),
            ))
        ;
        
        return $form;
    }

    /**
     * Create
     */
    public function createAction()
    {
        $config = $this->getConfig();
    	$request = $this->getRequest();
        $entity = new $config['entity']();
        $form   = $this->createCreateForm($config, $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->useACL($entity, 'create');

            $this->get('session')->getFlashBag()->add('success', 'flash.create.success');

            $nextAction = $form->get('saveAndAdd')->isClicked()
                ? $this->generateUrl($config['new'])
                : $this->generateUrl($config['show'], array('id' => $entity->getId()));
            return $this->redirect($nextAction);

        }
        $this->get('session')->getFlashBag()->add('danger', 'flash.create.error');

        // remove the form to return to the view
        unset($config['newType']);

        return array(
            'config'     => $config,
            'entity'     => $entity,
            'form'       => $form->createView(),
        );
    }

    /**
    * Creates a form to create a entity.
    * @param array $config
    * @param $entity The entity
    * @return \Symfony\Component\Form\Form The form
    */
    protected function createCreateForm($config, $entity)
    {
        $form = $this->createForm($config['newType'], $entity, array(
            'action' => $this->generateUrl($config['create']),
            'method' => 'POST',
        ));

        $form
            ->add('save', 'submit', array(
                'translation_domain' => 'MWSimpleAdminCrudBundle',
                'label'              => 'views.new.save',
                'attr'               => array(
                    'class' => 'form-control btn-success',
                    'col'   => 'col-lg-2',
                )
            ))
            ->add('saveAndAdd', 'submit', array(
                'translation_domain' => 'MWSimpleAdminCrudBundle',
                'label'              => 'views.new.saveAndAdd',
                'attr'               => array(
                    'class' => 'form-control btn-primary',
                    'col'   => 'col-lg-3',
                )
            ))
        ;

        return $form;
    }

    /**
     * New
     */
    public function newAction()
    {
        $config = $this->getConfig();
        $entity = new $config['entity']();
        $form   = $this->createCreateForm($config, $entity);

        // remove the form to return to the view
        unset($config['newType']);

        return array(
            'config'     => $config,
            'entity'     => $entity,
            'form'       => $form->createView(),
        );
    }

    /**
     * Show
     * @param $id
     */
    public function showAction($id)
    {
        $config = $this->getConfig();
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository($config['repository'])->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find '.$config['entityName'].' entity.');
        }
        $this->useACL($entity, 'show');
        $deleteForm = $this->createDeleteForm($config, $id);

        return array(
            'config'      => $config,
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edit
     * @param $id
     */
    public function editAction($id)
    {
        $config = $this->getConfig();
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository($config['repository'])->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find '.$config['entityName'].' entity.');
        }
        $this->useACL($entity, 'edit');
        $editForm = $this->createEditForm($config, $entity);
        $deleteForm = $this->createDeleteForm($config, $id);

        // remove the form to return to the view
        unset($config['editType']);

        return array(
            'config'      => $config,
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a entity.
    * @param array $config
    * @param $entity The entity
    * @return \Symfony\Component\Form\Form The form
    */
    protected function createEditForm($config, $entity)
    {
        $form = $this->createForm($config['editType'], $entity, array(
            'action' => $this->generateUrl($config['update'], array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form
            ->add(
                'save', 'submit', array(
                'translation_domain' => 'MWSimpleAdminCrudBundle',
                'label'              => 'views.new.save',
                'attr'               => array(
                    'class' => 'form-control btn-success',
                    'col'   => 'col-lg-2',
                )
                )
            )
            ->add(
                'saveAndAdd', 'submit', array(
                'translation_domain' => 'MWSimpleAdminCrudBundle',
                'label'              => 'views.new.saveAndAdd',
                'attr'               => array(
                    'class' => 'form-control btn-primary',
                    'col'   => 'col-lg-3',
                )
                )
            )
        ;

        return $form;
    }

    /**
     * Update
     * @param $id
     */
    public function updateAction($id)
    {
        $config = $this->getConfig();
    	$request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository($config['repository'])->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find '.$config['entityName'].' entity.');
        }
        $this->useACL($entity, 'update');
        $deleteForm = $this->createDeleteForm($config, $id);
        $editForm = $this->createEditForm($config, $entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.update.success');

            $nextAction = $editForm->get('saveAndAdd')->isClicked()
                        ? $this->generateUrl($config['new'])
                        : $this->generateUrl($config['show'], array('id' => $id));
            return $this->redirect($nextAction);
        }

        $this->get('session')->getFlashBag()->add('danger', 'flash.update.error');

        // remove the form to return to the view
        unset($config['editType']);

        return array(
            'config'      => $config,
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Delete
     * @param $id
     */
    public function deleteAction($id)
    {
        $config = $this->getConfig();
    	$request = $this->getRequest();
        $form = $this->createDeleteForm($config, $id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository($config['repository'])->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find '.$config['entityName'].' entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.delete.success');
        }

        return $this->redirect($this->generateUrl($config['index']));
    }

    /**
     * Creates a form to delete a Post entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createDeleteForm($config, $id)
    {
        $mensaje = $this->get('translator')->trans('views.recordactions.confirm', array(), 'MWSimpleAdminCrudBundle');
        $onclick = 'return confirm("'.$mensaje.'");';
        return $this->createFormBuilder()
            ->setAction($this->generateUrl($config['delete'], array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array(
                'translation_domain' => 'MWSimpleAdminCrudBundle',
                'label'              => 'views.recordactions.delete',
                'attr'               => array(
                    'class'   => 'form-control btn-danger',
                    'col'     => 'col-lg-2',
                    'onclick' => $onclick,
                )
            ))
            ->getForm()
        ;
    }

    protected function getConfig(){
        $configs = Yaml::parse(file_get_contents($this->get('kernel')->getRootDir().'/../src/'.$this->config['yml']));
        foreach ($configs as $key => $value) {
            $config[$key] = $value;
        }
        foreach ($this->config as $key => $value) {
            if ($key != 'yml') {
                $config[$key] = $value;
            }
        }
        return $config;
    }

    public function getAutocompleteFormsMwsAction($options)
    {
        $request = $this->getRequest();
        $term = $request->query->get('q', null);

        $em = $this->getDoctrine()->getManager();

        $qb = $em->getRepository($options['repository'])->createQueryBuilder('a');
        $qb
            ->where("a.".$options['field']." LIKE :term")
            ->orderBy("a.".$options['field'], "ASC")
            ->setParameter("term", "%" . $term . "%")
        ;
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

    protected function useACL($entity, $action)
    {
        $aclConf = $this->container->hasParameter('mw_simple_admin_crud.acl') ?
            $this->container->getParameter('mw_simple_admin_crud.acl') : null;

        if ($aclConf['use']) {
            if ($this->isInstanceOf($entity, $aclConf['entities'])) {
                $aclManager = $this->container->get('mws_acl_manager');
                switch ($action) {
                    case 'create':
                        $aclManager->createACL($entity);
                        break;
                    case 'show':
                        $aclManager->controlACL($entity, 'VIEW', $aclConf['exclude_role']);
                        break;
                    case 'edit':
                        $aclManager->controlACL($entity, 'EDIT', $aclConf['exclude_role']);
                        break;
                    case 'update':
                        $aclManager->controlACL($entity, 'EDIT', $aclConf['exclude_role']);
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
}