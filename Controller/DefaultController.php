<?php

namespace MWSimple\Bundle\AdminCrudBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Exporter\Source\DoctrineORMQuerySourceIterator;
use Exporter\Source\ArraySourceIterator;
use Exporter\Handler;

/**
 * MWSimpleAdminCrudBundle Default controller.
 *
 * @author Gonzalo Alonso <gonkpo@gmail.com>
 */
class DefaultController extends Controller {

    /**
     * Configuration file.
     */
    protected $config = array();

    /**
     * Index.
     */
    public function indexAction() {
        $config = $this->getConfig();

        return array(
            'config' => $config,
        );
    }

    /**
     * Create query.
     *
     * @return Doctrine\ORM\QueryBuilder $queryBuilder
     */
    protected function createQuery() {

        $config = $this->getConfig();
        $alias = $config['alias'];
        $select = array();
        $join = array();
        $tipoArray = array();
        $concat = '';
        $groupBy = false;
        $oneToMany = false;
        $concatBoolean = false;
        $contador = 0;
        foreach ($config['fieldsindex'] as $columnas) {
            //select
            if ($columnas['type'] == 'MANY_TO_ONE' || $columnas['type'] == 'ONE_TO_ONE' || $columnas['type'] == 'ONE_TO_MANY') {
                foreach ($columnas['campos'] as $key => $campos) {
                    if ($key == '0') {
                        $concat = $columnas['alias'] . '.' . $campos;
                    } else {
                        $concat = $concat . ",' '," . $columnas['alias'] . '.' . $campos;
                    }
                }
                if ($columnas['type'] == 'ONE_TO_MANY') {
                    $concat = 'GROUP_CONCAT(' . $concat . " SEPARATOR ',')";
                    $groupBy = true;
                    $oneToMany = true;
                } else {
                    //si concat tiene varios campos lo concatena
                    if (substr_count($concat, ',') > 0) {
                        $concat = 'concat(' . $concat . ')';
                        $concatBoolean = true;
                    }
                }

                $select[] = $concat;
                $join[] = array(
                    'alias' => $columnas['alias'],
                    'join' => $columnas['join'],
                );
                $columnas['type'] = 'string';
            } else {
                $select[] = $alias . '.' . $columnas['name'];
            }
            //formato
            if (isset($columnas['date']) || isset($columnas['datetime']) || isset($columnas['datetimetz'])) {
                $formato = $columnas['date'];
            } else {
                $formato = '';
            }
            $tipoArray[] = array(
                'column' => $contador,
                'type' => $columnas['type'],
                'format' => $formato,
            );
            ++$contador;
        }

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb
                ->select($select)
                ->from($config['repository'], 'a')
        ;

        if (count($join) > 0) {
            foreach ($join as $j) {
                if ($j['join'] == 'INNER') {
                    $qb->join('a.' . $j['alias'], $j['alias']);
                } else {
                    $qb->leftJoin('a.' . $j['alias'], $j['alias']);
                }
            }
            if ($groupBy) {
                $qb->groupBy('a.id');
            }
        }

        return array(
            'query' => $qb,
            'oneToMany' => $oneToMany,
            'concatBoolean' => $concatBoolean,
            'tipoArray' => $tipoArray,
        );
    }

    /**
     * Export Csv.
     */
    public function exportCsvAction($format) {
        $config = $this->getConfig();
        $queryBuilder = $this->createQuery($config['repository']);
        $campos = array();
        //array
        foreach ($config['fieldsindex'] as $key => $value) {
            //if is defined and true
            if (!empty($value['export']) && $value['export']) {
                if ($value['type'] == 'ONE_TO_ONE' || $value['type'] == 'ONE_TO_ONE' || $value['type'] == 'MANY_TO_ONE') {
                    $campos[] = $value['alias'] . '.' . $value['name'];
                } elseif ($value['type'] == 'datetime' || $value['type'] == 'datetimetz') {
                    $campos[] = "DATE_FORMAT(" . $key . ", '%d/%m/%Y %H:%s')";
                } elseif ($value['type'] == 'date') {
                    $campos[] = "DATE_FORMAT(" . $key . ", '%d/%m/%Y')";
                } elseif ($value['type'] == 'time') {
                    $campos[] = "DATE_FORMAT(" . $key . ", '%H:%s')";
                } else {
                    $campos[] = $key;
                }
            }
        }

        $queryBuilder['query']->select($campos);

        $array = $queryBuilder['query']->getQuery()->getArrayResult();

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
        //$exporter_source = new DoctrineORMQuerySourceIterator($query, $campos, 'Y-m-d H:i:s');      
        $exporter_source = new ArraySourceIterator($array);
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
     *
     * @param array $config
     *
     * @return array
     */
    protected function filter($config) {
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
                $this->get('lexik_form_filter.query_builder_updater')
                        ->addFilterConditions($filterForm, $queryBuilder);
                // Save filter to session
                $filterData = $request->get($filterForm->getName());
                $session->set($config['sessionFilter'], $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has($config['sessionFilter'])) {
                $filterData = $session->get($config['sessionFilter']);
                $filterForm->submit($filterData);
                $this->get('lexik_form_filter.query_builder_updater')
                        ->addFilterConditions($filterForm, $queryBuilder);
            }
        }

        return array($filterForm, $queryBuilder);
    }

    /**
     * Create filter form.
     *
     * @param array $config
     * @param $filterData
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createFilterForm($config, $filterData = null) {
        $form = $this->createForm($config['filterType'], $filterData, array(
            'action' => $this->generateUrl($config['index']),
            'method' => 'GET',
        ));

        $form
                ->add('filter', 'submit', array(
                    'translation_domain' => 'MWSimpleAdminCrudBundle',
                    'label' => 'views.index.filter',
                    'attr' => array('class' => 'btn btn-success col-lg-1'),
                ))
                ->add('reset', 'submit', array(
                    'translation_domain' => 'MWSimpleAdminCrudBundle',
                    'label' => 'views.index.reset',
                    'attr' => array('class' => 'reset_submit_filters btn btn-danger col-lg-1 col-lg-offset-1'),
                ))
        ;

        return $form;
    }

    /**
     * Create.
     */
    public function createAction() {
        $config = $this->getConfig();
        $request = $this->getRequest();
        $entity = new $config['entity']();
        $form = $this->createCreateForm($config, $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->useACL($entity, 'create');

            $this->get('session')->getFlashBag()->add('success', 'flash.create.success');

            $nextAction = $form->get('saveAndAdd')->isClicked() ? $this->generateUrl($config['new']) : $this->generateUrl($config['show'], array('id' => $entity->getId()));

            return $this->redirect($nextAction);
        }
        $this->get('session')->getFlashBag()->add('danger', 'flash.create.error');

        // remove the form to return to the view
        unset($config['newType']);

        return array(
            'config' => $config,
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a entity.
     *
     * @param array $config
     * @param $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createCreateForm($config, $entity) {
        $form = $this->createForm($config['newType'], $entity, array(
            'action' => $this->generateUrl($config['create']),
            'method' => 'POST',
        ));

        $form
                ->add('save', 'submit', array(
                    'translation_domain' => 'MWSimpleAdminCrudBundle',
                    'label' => 'views.new.save',
                    'attr' => array(
                        'class' => 'form-control btn-success',
                        'col' => 'col-lg-2',
                    ),
                ))
                ->add('saveAndAdd', 'submit', array(
                    'translation_domain' => 'MWSimpleAdminCrudBundle',
                    'label' => 'views.new.saveAndAdd',
                    'attr' => array(
                        'class' => 'form-control btn-primary',
                        'col' => 'col-lg-3',
                    ),
                ))
        ;

        return $form;
    }

    /**
     * New.
     */
    public function newAction() {
        $config = $this->getConfig();
        $entity = new $config['entity']();
        $form = $this->createCreateForm($config, $entity);

        // remove the form to return to the view
        unset($config['newType']);

        return array(
            'config' => $config,
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Show.
     *
     * @param $id
     */
    public function showAction($id) {
        $config = $this->getConfig();
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository($config['repository'])->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ' . $config['entityName'] . ' entity.');
        }
        $this->useACL($entity, 'show');
        $deleteForm = $this->createDeleteForm($config, $id);

        return array(
            'config' => $config,
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edit.
     *
     * @param $id
     */
    public function editAction($id) {
        $config = $this->getConfig();
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository($config['repository'])->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ' . $config['entityName'] . ' entity.');
        }
        $this->useACL($entity, 'edit');
        $editForm = $this->createEditForm($config, $entity);
        $deleteForm = $this->createDeleteForm($config, $id);

        // remove the form to return to the view
        unset($config['editType']);

        return array(
            'config' => $config,
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Creates a form to edit a entity.
     *
     * @param array $config
     * @param $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createEditForm($config, $entity) {
        $form = $this->createForm($config['editType'], $entity, array(
            'action' => $this->generateUrl($config['update'], array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form
                ->add('save', 'submit', array(
                    'translation_domain' => 'MWSimpleAdminCrudBundle',
                    'label' => 'views.new.save',
                    'attr' => array(
                        'class' => 'form-control btn-success',
                        'col' => 'col-lg-2',
                    ),
                ))
                ->add('saveAndAdd', 'submit', array(
                    'translation_domain' => 'MWSimpleAdminCrudBundle',
                    'label' => 'views.new.saveAndAdd',
                    'attr' => array(
                        'class' => 'form-control btn-primary',
                        'col' => 'col-lg-3',
                    ),
                ))
        ;

        return $form;
    }

    /**
     * Update.
     *
     * @param $id
     */
    public function updateAction($id) {
        $config = $this->getConfig();
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository($config['repository'])->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ' . $config['entityName'] . ' entity.');
        }
        $this->useACL($entity, 'update');
        $deleteForm = $this->createDeleteForm($config, $id);
        $editForm = $this->createEditForm($config, $entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.update.success');

            $nextAction = $editForm->get('saveAndAdd')->isClicked() ?
                    $this->generateUrl($config['new']) :
                    $this->generateUrl($config['show'], array('id' => $id))
            ;

            return $this->redirect($nextAction);
        }

        $this->get('session')->getFlashBag()->add('danger', 'flash.update.error');

        // remove the form to return to the view
        unset($config['editType']);

        return array(
            'config' => $config,
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Delete.
     *
     * @param $id
     */
    public function deleteAction($id) {
        $config = $this->getConfig();
        $request = $this->getRequest();
        $form = $this->createDeleteForm($config, $id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository($config['repository'])->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ' . $config['entityName'] . ' entity.');
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
    protected function createDeleteForm($config, $id) {
        $mensaje = $this->get('translator')
                ->trans('views.recordactions.confirm', array(), 'MWSimpleAdminCrudBundle');
        $onclick = 'return confirm("' . $mensaje . '");';

        return $this->createFormBuilder()
                        ->setAction($this->generateUrl($config['delete'], array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array(
                            'translation_domain' => 'MWSimpleAdminCrudBundle',
                            'label' => 'views.recordactions.delete',
                            'attr' => array(
                                'class' => 'form-control btn-danger',
                                'col' => 'col-lg-2',
                                'onclick' => $onclick,
                            ),
                        ))
                        ->getForm()
        ;
    }

    protected function getConfig() {
        $configs = Yaml::parse(file_get_contents($this->get('kernel')->getRootDir() . '/../src/' . $this->config['yml']));
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

    public function getAutocompleteFormsMwsAction($options) {
        $request = $this->getRequest();
        $term = $request->query->get('q', null);

        $em = $this->getDoctrine()->getManager();

        $qb = $em->getRepository($options['repository'])->createQueryBuilder('a');
        $qb
                ->add('where', 'a.' . $options['field'] . ' LIKE ?1')
                ->add('orderBy', 'a.' . $options['field'] . ' ASC')
                ->setParameter(1, '%' . $term . '%')
        ;
        $entities = $qb->getQuery()->getResult();

        $array = array();

        foreach ($entities as $entity) {
            $array[] = array(
                'id' => $entity->getId(),
                'text' => $entity->__toString(),
            );
        }

        $response = new JsonResponse();
        $response->setData($array);

        return $response;
    }

    protected function useACL($entity, $action) {
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

    protected function isInstanceOf($object, Array $classnames) {
        foreach ($classnames as $classname) {
            if ($object instanceof $classname) {
                return true;
            }
        }

        return false;
    }

    public function getTable($_qb = null) {
        $config = $this->getConfig();
        $table = $config['table'];
        if (is_null($_qb)) {
            $qb = $this->createQuery();
        } else {
            $qb = $_qb;
        }
        $oneToMany = false;
        $concatBoolean = false;

        $query = $qb['query']
                ->getQuery()
                ->getSQL()
        ;
        if (isset($qb['oneToMany'])) {
            $oneToMany = $qb['oneToMany'];
        }

        if (isset($qb['concatBoolean'])) {
            $concatBoolean = $qb['concatBoolean'];
        }

        $sql = str_replace('SELECT', '', $query);

        list($select, $from) = explode('FROM', $sql);
        if ($oneToMany) {
            $queryColumn = $this->separarQueryConConcatAndGroupConcat($select);
        } elseif ($concatBoolean) {
            $queryColumn = $this->separarQueryConConcat($select);
        } else {
            $queryColumn = explode(',', $select);
        }
        $columns = array();
        $contador = 0;
        $tipoArrayExist = true;
        if (isset($qb['tipoArray']) == false) {
            $format = '';
            $type = 'string';
            $tipoArrayExist = false;
        }
        foreach ($queryColumn as $col) {
            list($identy, $name) = explode('AS', $col);

            if ($tipoArrayExist) {
                list($type, $format) = $this->getTipoFormat($qb['tipoArray'], $contador);
            }

            $columns[] = array(
                'db' => $identy,
                'dt' => $contador,
                'name' => trim($name),
                'type' => $type,
                'format' => $format,
            );

            ++$contador;
        }

        //from 
        $from = ' FROM ' . $from;
        // Table's primary key
        $primaryKey = 'id';

        $request = $this->getRequest();
        $response = new JsonResponse();
        $ssp = $this->container->get('mws_datatable_ssp');

        $response->setData($ssp->simple($request, $table, $primaryKey, $columns, $from));

        return $response;
    }

    private function getTipoFormat($array, $index) {
        $type = 'string';
        $format = '';
        foreach ($array as $a) {
            if ($a['column'] == $index) {
                if (isset($a['type'])) {
                    $type = $a['type'];
                }
                if (isset($a['format'])) {
                    $format = $a['format'];
                }
                break;
            }
        }

        return array($type, $format);
    }

    private function separarQueryConConcat($query) {
        $config = $this->getConfig();
        $queryColumn = array();
        $tempRem = trim(str_replace('CONCAT', '|', $query));
        $temp = $tempRem;
        for ($i = 0; $i < count($config['fieldsindex']); ++$i) {
            if ($temp != '') {
                if ($temp[0] == '|') {
                    //caso con concat
                    $temp = substr($temp, 0, strpos($temp, ')') + 2);
                    $temp2 = trim(str_replace($temp, '', $tempRem));
                    $temp = $temp . substr($temp2, 0, strpos($temp2, ',') + 1);
                } else {
                    //caso sin concat 
                    if (strpos($temp, ',')) {
                        $temp = substr($temp, 0, strpos($temp, ',') + 1);
                    }
                }
                $queryColumn[] = str_replace('|', 'CONCAT', trim($temp, ','));
                $temp = trim(str_replace($temp, '', $tempRem));
                $tempRem = $temp;
            }
        }

        return $queryColumn;
    }

    private function separarQueryConConcatAndGroupConcat($query) {
        $config = $this->getConfig();
        $queryColumn = array();
        $tempRem = trim(str_replace('GROUP_CONCAT', '?', $query));
        $tempRem = trim(str_replace('CONCAT', '|', $tempRem));

        $temp = $tempRem;
        for ($i = 0; $i < count($config['fieldsindex']); ++$i) {
            if ($temp != '') {
                if ($temp[0] == '|') {
                    //caso con concat
                    $temp = substr($temp, 0, strpos($temp, ')') + 2);
                    $temp2 = trim(str_replace($temp, '', $tempRem));
                    if (strpos($temp2, ',')) {
                        $temp2 = substr($temp2, 0, strpos($temp2, ',') + 1);
                    }
                    $temp = $temp . $temp2;
                    $queryColumn[] = str_replace('|', 'CONCAT', trim($temp, ','));
                } elseif ($temp[0] == '?') {
                    //caso con Group concat
                    $temp = substr($temp, 0, strpos($temp, ')') + 2);
                    $temp2 = trim(str_replace($temp, '', $tempRem));
                    if (strpos($temp2, ',')) {
                        $temp2 = substr($temp2, 0, strpos($temp2, ',') + 1);
                    }
                    $temp = $temp . $temp2;
                    $queryColumn[] = str_replace('?', 'GROUP_CONCAT', trim($temp, ','));
                } else {
                    //caso sin concat 
                    if (strpos($temp, ',')) {
                        $temp = substr($temp, 0, strpos($temp, ',') + 1);
                    }
                    $queryColumn[] = trim($temp, ',');
                }
                $temp = trim(str_replace($temp, '', $tempRem));
                $tempRem = $temp;
            }
        }

        return $queryColumn;
    }

}
