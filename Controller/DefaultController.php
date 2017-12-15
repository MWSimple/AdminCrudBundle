<?php

namespace MWSimple\Bundle\AdminCrudBundle\Controller;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
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
    // Configuration file.
    protected $config = [];
    protected $configArray = [];
    // Entity.
    protected $entity;
    // Entity Manager.
    protected $em;
    // Query Builder.
    protected $queryBuilder;
    // Form.
    protected $form;
    // Allows you to configure options in the form an array must be set
    protected $optionsForm = null;
    // Not export relations
    protected $fieldsNotExport = ['ONE_TO_MANY','MANY_TO_MANY'];

    /**
     * Index
     */
    public function indexAction(Request $request)
    {
        $this->getConfig();
        $this->createQuery($this->configArray['repository']);

        $filterForm = $this->filter($request);
        
        if ($this->configArray['paginate']) {
            $paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $this->queryBuilder,
                $request->query->get('page', 1),
                ($this->container->hasParameter('knp_paginator.page_range')) ? $this->container->getParameter('knp_paginator.page_range'):10
            );
        } else {
            $pagination = $this->resultQuery();
        }
        //remove the form to return to the view
        unset($this->configArray['filterType']);

        return $this->render($this->configArray['view_index'], array(
            'config'     => $this->configArray,
            'entities'   => $pagination,
            'filterForm' => $filterForm,
        ));
    }

    /**
     * Create query.
     * @param string $repository
     */
    protected function createQuery($repository)
    {
        $this->em = $this->getDoctrine()->getManager();
        $this->queryBuilder = $this->em->getRepository($repository)
            ->createQueryBuilder('a')
            ->orderBy('a.id', 'DESC')
        ;
    }

    /* get result query */
    protected function resultQuery()
    {
        return $this->queryBuilder->getQuery()->getResult();
    }

    /**
     * Export Csv.
     */
    public function exportCsvAction($format)
    {
        $this->getConfig();

        if ($format == "pdf") {
            $response = $this->forward('MWSimpleAdminCrudBundle:Default:exportPdf', [
                'config' => $this->configArray
            ]);
        } else {
            $this->createQuery($this->configArray['repository']);
            $query  = $this->queryBuilder->getQuery();
            $fields = $this->getFields();
            $content_type = $this->getContentType($format);
            $export_to = 'php://output';// Location to Export this to
            $exporter_source = new DoctrineORMQuerySourceIterator($query, $fields, "Y-m-d H:i:s");// Data to export
            $exporter_writer = '\Exporter\Writer\\' . ucfirst($format) . 'Writer';// Get an Instance of the Writer
            $exporter_writer = new $exporter_writer($export_to);
            $response = new Response();// Generate response
            $response->headers->set('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');// Set headers
            $response->headers->set('Content-type', $content_type);
            $response->headers->set('Expires', 0);
            $response->headers->set('Pragma', 'public');
            $response->sendHeaders();// Send headers before outputting anything
            Handler::create($exporter_source, $exporter_writer)->export();// Export to the format
        }

        return $response;
    }

    /**
     * Export Pdf.
     */
    public function exportPdfAction($config)
    {
        $this->configArray = $config;

        $this->createQuery($this->configArray['repository']);
        
        $title_fontSize = $this->configArray['export_pdf']['title_fontSize'];
        $table_fontSize = $this->configArray['export_pdf']['table_fontSize'];

        $results = [
            'content' => [
                ['text' =>  $this->configArray['entityName'], 'fontSize' => $title_fontSize, 'bold' => true, 'margin' => [0, 20, 0, 8]],
                ['style' => 'tableExample', 'fontSize' => $table_fontSize, 'table' => ['headerRows' => 1, 'body' => []], 'layout' => 'lightHorizontalLines']
            ]
        ];

        $results['content'][1]['table']['body'] = $this->getBodyPdf();

        $response = new JsonResponse();
        $data = ['filename' => $this->configArray['entityName'].'.pdf', 'data' => $results];
        $response->setData($data);

        return $response;
    }

    protected function getBodyPdf()
    {
        $fields = $this->getFieldsForJson();
        $fieldsSelect = $this->getFieldsSelect();
        $fieldsTypes = $this->getFieldsTypes();
        $entities = $this->queryBuilder->getQuery()->getResult();

        $body = [];
        array_push($body, $fields);
        foreach ($entities as $key => $value) {
            $res = [];
            foreach ($fieldsSelect as $v) {
                $val = $value->$v();
                if (array_key_exists($v, $fieldsTypes)) {
                    if ($fieldsTypes[$v]['type'] == "date" && !is_null($val)) {
                        $val = $val->format($fieldsTypes[$v]['date']);
                    } elseif ($fieldsTypes[$v]['type'] == "TO_ONE" && !is_null($val)) {
                        if ($fieldsTypes[$v]['get'] == '__toString') {
                            $val = $val->__toString();
                        }
                    }
                }
                array_push($res, $val);
            }
            array_push($body, $res);
        }

        return $body;
    }

    protected function getFields()
    {
        $fields = [];
        foreach ($this->configArray['fieldsindex'] as $key => $value) {
            //if is defined and true
            if (!empty($value['export']) && $value['export']) {
                $fields[] = $value['name'];
            }
        }
        return $fields;
    }

    protected function getFieldsTypes()
    {
        $fields = [];
        foreach ($this->configArray['fieldsindex'] as $key => $value) {
            //if is defined and true
            if (!empty($value['export']) && $value['export']) {
                if (!in_array($value['type'], $this->fieldsNotExport)) {
                    if ($value['type'] == "datetime" || $value['type'] == "date" || $value['type'] == "time") {
                        $fields["get".$value['name']] = [
                            'type' => 'date',
                            'date' => $value['date']
                        ];
                    } elseif ($value['type'] == "MANY_TO_ONE" || $value['type'] == "ONE_TO_ONE") {
                        $fields["get".$value['name']] = [
                            'type' => 'TO_ONE',
                            'get' => '__toString'
                        ];
                    }
                }
            }
        }
        return $fields;
    }

    protected function getFieldsForJson()
    {
        $fields = [];
        foreach ($this->configArray['fieldsindex'] as $key => $value) {
            //if is defined and true
            if (!empty($value['export']) && $value['export']) {
                if (!in_array($value['type'], $this->fieldsNotExport)) {
                    $field['text'] = $value['label'];
                    $field['style'] = 'tableHeader';
                    array_push($fields, $field);
                }
            }
        }
        return $fields;
    }

    protected function getFieldsSelect()
    {
        $fields = [];
        foreach ($this->configArray['fieldsindex'] as $key => $value) {
            //if is defined and true
            if (!empty($value['export']) && $value['export']) {
                $fields[] = "get".$value['name'];
            }
        }
        return $fields;
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
     * If multiple array filterData is empty return 0.
     * @return array
     */
    protected function ifArrayIsEmpty($array, $keyCondition = null)
    {
        foreach ($array as $key => $item) {
            if(is_array($item)) {
                $array[$key] = $this->ifArrayIsEmpty($item);
            } else {
                if (empty($item)) {
                    if (is_null($keyCondition)) {
                        unset($array[$key]);
                    } elseif ($key = $keyCondition) {
                        unset($array[$key]);
                    }
                }
            }
        }
        foreach ($array as $key => $item) {
            if (empty($item)) {
                unset($array[$key]);
            }
        }
        return $array;
    }

    /**
     * Process filter request.
     * @return array
     */
    protected function filter(Request $request)
    {
        if (array_key_exists('filterType', $this->configArray)) {
            $session = $request->getSession();
            $filterForm = $this->createFilterForm();
            // Bind values from the request
            $filterForm->handleRequest($request);
            // Reset filter
            if ($this->configArray['sessionFilter'] && $filterForm->get('reset')->isClicked()) {
                $session->remove($this->configArray['sessionFilter']);
                $filterForm = $this->createFilterForm();
            }

            // Filter action
            if ($filterForm->get('filter')->isClicked()) {
                if ($filterForm->isValid()) {
                    // Build the query from the given form object
                    $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $this->queryBuilder);
                    // Save filter to session
                    $filterData = $request->get($filterForm->getName());
                    $isEmptyFilterData = count($this->ifArrayIsEmpty($filterData, "condition_pattern"));
                    if ($isEmptyFilterData > 0) {
                        $session->set($this->configArray['sessionFilter'], $filterData);
                    }
                }
            } else {
                // Get filter from session
                if ($this->configArray['sessionFilter'] && $session->has($this->configArray['sessionFilter'])) {
                    $filterData = $session->get($this->configArray['sessionFilter']);
                    $filterForm->submit($filterData);
                    $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $this->queryBuilder);
                }
            }

            $ret = $filterForm->createView();
        } else {
            $ret = null;
        }

        return $ret;
    }

    /**
     * Create filter form.
     * @param $filterData
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createFilterForm($filterData = null)
    {
        $optionsForm = [
            'action' => $this->generateUrl($this->configArray['index']),
            'method' => 'GET',
        ];
        if (is_array($this->optionsForm)) {
            foreach ($this->optionsForm as $key => $value) {
                $optionsForm[$key] = $value;
            }
        }

        $form = $this->createForm($this->configArray['filterType'], $filterData, $optionsForm);

        $form
            ->add('filter', SubmitType::class, array(
                'translation_domain' => 'MWSimpleAdminCrudBundle',
                'label'              => 'views.index.filter',
                'attr'               => array(
                    'class' => 'form-control btn-success',
                    'col'   => 'col-lg-2 col-md-4',
                ),
            ));
        if ($this->configArray['sessionFilter']) {
            $form
                ->add('reset', SubmitType::class, array(
                    'translation_domain' => 'MWSimpleAdminCrudBundle',
                    'label'              => 'views.index.reset',
                    'attr'               => array(
                        'class' => 'form-control reset_submit_filters btn-danger',
                        'col'   => 'col-lg-2 col-md-4',
                    ),
                ));
        }

        return $form;
    }

    /**
     * Create
     */
    public function createAction(Request $request)
    {
        $this->getConfig();
        $this->createNewEntity();
        $this->form = $this->createCreateForm();
        $this->form->handleRequest($request);

        if ($this->validateForm() && $this->form->isValid()) {
            $this->em = $this->getDoctrine()->getManager();
            $this->prePersistEntity();
            $this->em->persist($this->entity);
            $this->em->flush();
            //Crear ACL
            $this->useACL('create');
            //Set session mensaje
            $this->get('session')->getFlashBag()->add('success', 'flash.create.success');

            return $this->redirect($this->urlSuccess());
        }

        $this->get('session')->getFlashBag()->add('error', 'flash.create.error');

        // remove the form to return to the view
        unset($this->configArray['newType']);

        return $this->render($this->configArray['view_new'], array(
            'config' => $this->configArray,
            'entity' => $this->entity,
            'form'   => $this->form->createView(),
        ));
    }

    /**
    * Creates a form to create a entity.
    * @return \Symfony\Component\Form\Form The form
    */
    protected function createCreateForm()
    {
        $optionsForm = [
            'action' => $this->generateUrl($this->configArray['create']),
            'method' => 'POST',
        ];
        if (is_array($this->optionsForm)) {
            foreach ($this->optionsForm as $key => $value) {
                $optionsForm[$key] = $value;
            }
        }
        $form = $this->createForm($this->configArray['newType'], $this->entity, $optionsForm);

        $form
            ->add('save', SubmitType::class, array(
                'translation_domain' => 'MWSimpleAdminCrudBundle',
                'label'              => 'views.new.save',
                'attr'               => array(
                    'class' => 'form-control btn-success',
                    'col'   => 'col-lg-2 col-md-4',
                )
            ));

        if ($this->configArray['saveAndAdd']) {
            $form
                ->add('saveAndAdd', SubmitType::class, array(
                    'translation_domain' => 'MWSimpleAdminCrudBundle',
                    'label'              => 'views.new.saveAndAdd',
                    'attr'               => array(
                        'class' => 'form-control btn-primary',
                        'col'   => 'col-lg-3 col-md-4',
                    )
                ));
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
        $deleteForm = $this->createDeleteForm($id, true);

        return $this->render($this->configArray['view_show'], array(
            'config'      => $this->configArray,
            'entity'      => $this->entity,
            'delete_form' => $deleteForm,
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
        $deleteForm = $this->createDeleteForm($id, true);

        // remove the form to return to the view
        unset($this->configArray['editType']);

        return $this->render($this->configArray['view_edit'], array(
            'config'      => $this->configArray,
            'entity'      => $this->entity,
            'form'        => $form->createView(),
            'delete_form' => $deleteForm,
        ));
    }

    /**
    * Creates a form to edit a entity.
    * @return \Symfony\Component\Form\Form The form
    */
    protected function createEditForm()
    {
        $optionsForm = [
            'action' => $this->generateUrl($this->configArray['update'], array('id' => $this->entity->getId())),
            'method' => 'PUT',
        ];
        if (is_array($this->optionsForm)) {
            foreach ($this->optionsForm as $key => $value) {
                $optionsForm[$key] = $value;
            }
        }
        $form = $this->createForm($this->configArray['editType'], $this->entity, $optionsForm);

        $form
            ->add('save', SubmitType::class, array(
                'translation_domain' => 'MWSimpleAdminCrudBundle',
                'label'              => 'views.new.save',
                'attr'               => array(
                    'class' => 'form-control btn-success',
                    'col'   => 'col-lg-2 col-md-4',
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
                        'col'   => 'col-lg-3 col-md-4',
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
        $deleteForm = $this->createDeleteForm($id, true);
        $this->form = $this->createEditForm();
        $this->preHandleRequestEntity();
        $this->form->handleRequest($request);

        $this->preFormIsValid();
        if ($this->validateForm() && $this->form->isValid()) {
            $this->preUpdateEntity();
            $this->em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.update.success');

            return $this->redirect($this->urlSuccess());
        }

        $this->get('session')->getFlashBag()->add('error', 'flash.update.error');

        // remove the form to return to the view
        unset($this->configArray['editType']);

        return $this->render($this->configArray['view_edit'], array(
            'config'      => $this->configArray,
            'entity'      => $this->entity,
            'form'        => $this->form->createView(),
            'delete_form' => $deleteForm,
        ));
    }

    /* Execute after success flush entity in createAction and updateAction */
    protected function urlSuccess()
    {
        if ($this->configArray['saveAndAdd']) {
            $urlSuccess = $this->form->get('saveAndAdd')->isClicked()
            ? $this->generateUrl($this->configArray['new'])
            : $this->generateUrl($this->configArray['show'], array('id' => $this->entity->getId()));
        } else {
            $urlSuccess = $this->generateUrl($this->configArray['show'], array('id' => $this->entity->getId()));
        }

        return $urlSuccess;
    }

    /**
     * Delete
     * @param $id
     */
    public function deleteAction(Request $request, $id)
    {
        $this->getConfig();
        $urlSuccess = $this->generateUrl($this->configArray['index']);
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->em = $this->getDoctrine()->getManager();
            $this->entity = $this->em->getRepository($this->configArray['repository'])->find($id);

            if (!$this->entity) {
                throw $this->createNotFoundException('Unable to find '.$this->configArray['entityName'].' entity.');
            }

            $this->preRemoveEntity();
            try {
                $this->em->remove($this->entity);
                $this->em->flush();
                $this->get('session')->getFlashBag()->add('success', 'flash.delete.success');
            } catch (ForeignKeyConstraintViolationException $e) {
                $this->get('session')->getFlashBag()->add('error', 'flash.delete.foreignkey');
                $urlSuccess = $this->generateUrl($this->configArray['show'], array('id' => $this->entity->getId()));
            }
        }

        return $this->redirect($urlSuccess);
    }

    /**
     * Creates a form to delete a Post entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createDeleteForm($id, $view = false)
    {
        if (array_key_exists('delete', $this->configArray)) {
            $mensaje = $this->get('translator')->trans('views.recordactions.confirm', array(), 'MWSimpleAdminCrudBundle');
            $onclick = 'return confirm("'.$mensaje.'");';
            $ret = $this->createFormBuilder()
                ->setAction($this->generateUrl($this->configArray['delete'], array('id' => $id)))
                ->setMethod('DELETE')
                ->add('submit', SubmitType::class, array(
                    'translation_domain' => 'MWSimpleAdminCrudBundle',
                    'label'              => 'views.recordactions.delete',
                    'attr'               => array(
                        'class'   => 'btn-danger',
                        'onclick' => $onclick,
                    )
                ))
                ->getForm();
            if ($view) {
                $ret = $ret->createView();
            }
        } else {
            $ret = null;
        }

        return $ret;
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
        //Set options default in config
        $this->setDefaultConfig();
    }

    public function getAutocompleteFormsMwsAction(Request $request, $options, $qb = null, $text = null, $operand = null)
    {
        $term = $request->query->get('q', null);
        $pageLimit = $request->query->get('page_limit', null);

        if (is_null($qb)) {
            $em = $this->getDoctrine()->getManager();

            $qb = $em->getRepository($options['repository'])->createQueryBuilder('a');
            $qb
                ->where("a.".$options['field']." LIKE :term")
                ->setMaxResults($pageLimit)
                ->orderBy("a.".$options['field'], "ASC")
            ;
        }
        switch ($operand) {
            case 'start':
                $term = $term . "%";
                break;
            case 'end':
                $term = "%" . $term;
                break;
            case 'equal':
                break;
            default:
                $term = "%" . $term . "%";
                break;
        }
        $qb->setParameter("term", $term);

        $entities = $qb->getQuery()->getResult();

        $array = array();

        if (is_null($text)) {
            $text = "__toString";
        }

        foreach ($entities as $entity) {
            $array[] = array(
                'id'   => $entity->getId(),
                'text' => $entity->$text(),
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

    //DEFAULT CONFIG
    protected function setDefaultConfig()
    {
        //Si no existe parameter_root o fue comentado entra y setea null
        if (!array_key_exists('parameter_root', $this->configArray)) {
            $this->configArray['parameter_root'] = null;
        }
        //Si no existe site_view_layout o fue comentado entra y setea null
        if (!array_key_exists('site_view_layout', $this->configArray)) {
            $this->configArray['site_view_layout'] = null;
        }
        //Si no existe saveAndAdd, es false o fue comentado entra y setea en true
        if (!array_key_exists('saveAndAdd', $this->configArray)) {
            $this->configArray['saveAndAdd'] = true;
        } elseif ($this->configArray['saveAndAdd'] !== false) {
            $this->configArray['saveAndAdd'] = true;
        }
        //Si no existe sessionFilter o fue comentado entra y setea en false
        if (!array_key_exists('sessionFilter', $this->configArray)) {
            $this->configArray['sessionFilter'] = false;
        }
        //Si no existe paginate o fue comentado entra y setea en true
        if (!array_key_exists('paginate', $this->configArray)) {
            $this->configArray['paginate'] = true;
        }
        //Si no existe export_pdf o fue comentado entra y setea default
        if (!array_key_exists('export_pdf', $this->configArray)) {
            $this->configArray['export_pdf'] = [];
        }
        //Si no existe title_fontSize en export_pdf
        if (!array_key_exists('title_fontSize', $this->configArray['export_pdf'])) {
            $this->configArray['export_pdf']['title_fontSize'] = 14;
        }
        //Si no existe table_fontSize en export_pdf
        if (!array_key_exists('table_fontSize', $this->configArray['export_pdf'])) {
            $this->configArray['export_pdf']['table_fontSize'] = 12;
        }
    }

    /* Execute new entity newAction and createAction */
    protected function createNewEntity()
    {
        $this->entity = new $this->configArray['entity']();
    }
    /* Execute before persist the entity createAction */
    protected function prePersistEntity()
    {
    }
    /* Execute before handleRequest the entity updateAction */
    protected function preHandleRequestEntity()
    {
    }
    /* Execute before form->isValid() updateAction */
    protected function preFormIsValid()
    {
    }
    /* Execute before flush the entity updateAction */
    protected function preUpdateEntity()
    {
    }
    /* Execute before remove the entity deleteAction */
    protected function preRemoveEntity()
    {
    }
    /* Execute before form->isValid() entity createAction and updateAction */
    protected function validateForm()
    {
        return true;
    }
}
