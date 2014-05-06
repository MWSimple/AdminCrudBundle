<?php

namespace MWSimple\Bundle\AdminCrudBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Yaml\Yaml;

/**
 * MWSimpleAdminCrudBundle Default controller.
 * @author Gonzalo Alonso <gonkpo@gmail.com>
 */
class DefaultController extends Controller
{
	/**
     * @param array $config
     */
    public function indexAction($config)
    {
        list($filterForm, $queryBuilder) = $this->filter($config);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $queryBuilder,
            $this->get('request')->query->get('page', 1),
            (isset($this->container->parameters['knp_paginator.page_range'])) ? $this->container->parameters['knp_paginator.page_range'] : 10
        );
        //remove the form to return to the view
        unset($config['filterType']);
        //agrego los fieldsindex configurados en el yml segun entity.
        $config['fields'] = Yaml::parse(file_get_contents($this->get('kernel')->getRootDir().'/../src/'.$config['yml']))['fieldsindex'];
        // $config['fields'] = $this->container->getParameter('mwsadmincrud.'.$config['entityName'])['fieldsindex'];

        return array(
        	'config'     => $config,
            'entities'   => $pagination,
            'filterForm' => $filterForm->createView(),
        );
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
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository($config['repository'])
            ->createQueryBuilder('a')
            ->orderBy('a.id', 'DESC')
        ;
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
                $filterData = $filterForm->getData();
                $session->set($config['sessionFilter'], $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has($config['sessionFilter'])) {
                $filterData = $session->get($config['sessionFilter']);
                $filterForm = $this->createFilterForm($config, $filterData);
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
    private function createFilterForm($config, $filterData = null)
    {
        $form = $this->createForm($config['filterType'], $filterData, array(
            'action' => $this->generateUrl($config['index']),
            'method' => 'GET',
        ));

        $form
            ->add('filter', 'submit', array(
                'translation_domain' => 'MWSimpleCrudGeneratorBundle',
                'label'              => 'views.index.filter',
                'attr'               => array('class' => 'btn btn-success col-lg-1'),
            ))
            ->add('reset', 'submit', array(
                'translation_domain' => 'MWSimpleCrudGeneratorBundle',
                'label'              => 'views.index.reset',
                'attr'               => array('class' => 'btn btn-danger col-lg-1 col-lg-offset-1'),
            ))
        ;

        return $form;
    }

    /**
     * @param array $config
     */
    public function createAction($config)
    {
    	$request = $this->getRequest();
        $entity = new $config['entity']();
        $form   = $this->createCreateForm($config, $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
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
    private function createCreateForm($config, $entity)
    {
        $form = $this->createForm($config['newType'], $entity, array(
            'action' => $this->generateUrl($config['create']),
            'method' => 'POST',
        ));

        $form
            ->add(
                'save', 'submit', array(
                'translation_domain' => 'MWSimpleCrudGeneratorBundle',
                'label'              => 'views.new.save',
                'attr'               => array('class' => 'btn btn-success col-lg-2')
                )
            )
            ->add(
                'saveAndAdd', 'submit', array(
                'translation_domain' => 'MWSimpleCrudGeneratorBundle',
                'label'              => 'views.new.saveAndAdd',
                'attr'               => array('class' => 'btn btn-primary col-lg-2 col-lg-offset-1')
                )
            )
        ;

        return $form;
    }

    /**
     * @param array $config
     */
    public function newAction($config)
    {
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
     * @param $id
     * @param array $config
     */
    public function showAction($id, $config)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository($config['repository'])->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find '.$config['entityName'].' entity.');
        }

        $deleteForm = $this->createDeleteForm($config, $id);

        //agrego los fieldsshow configurados en el yml segun entity.
        $config['fields'] = Yaml::parse(file_get_contents($this->get('kernel')->getRootDir().'/../src/'.$config['yml']))['fieldsshow'];

        return array(
            'config'      => $config,
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * @param $id
     * @param array $config
     */
    public function editAction($id, $config)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository($config['repository'])->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find '.$config['entityName'].' entity.');
        }

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
    private function createEditForm($config, $entity)
    {
        $form = $this->createForm($config['editType'], $entity, array(
            'action' => $this->generateUrl($config['update'], array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form
            ->add(
                'save', 'submit', array(
                'translation_domain' => 'MWSimpleCrudGeneratorBundle',
                'label'              => 'views.new.save',
                'attr'               => array('class' => 'btn btn-success col-lg-2')
                )
            )
            ->add(
                'saveAndAdd', 'submit', array(
                'translation_domain' => 'MWSimpleCrudGeneratorBundle',
                'label'              => 'views.new.saveAndAdd',
                'attr'               => array('class' => 'btn btn-primary col-lg-2 col-lg-offset-1')
                )
            )
        ;

        return $form;
    }

    /**
     * @param $id
     * @param array $config
     */
    public function updateAction($id, $config)
    {
    	$request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository($config['repository'])->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find '.$config['entityName'].' entity.');
        }

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
     * @param $id
     * @param array $config
     */
    public function deleteAction($id, $config)
    {
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
    private function createDeleteForm($config, $id)
    {
        $mensaje = $this->get('translator')->trans('views.recordactions.confirm', array(), 'MWSimpleCrudGeneratorBundle');
        $onclick = 'return confirm("'.$mensaje.'");';
        return $this->createFormBuilder()
            ->setAction($this->generateUrl($config['delete'], array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array(
                'translation_domain' => 'MWSimpleCrudGeneratorBundle',
                'label'              => 'views.recordactions.delete',
                'attr'               => array(
                    'class'   => 'btn btn-danger col-lg-11',
                    'onclick' => $onclick,
                )
            ))
            ->getForm()
        ;
    }
}