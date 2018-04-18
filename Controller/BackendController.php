<?php

namespace MWSimple\Bundle\AdminCrudBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/")
 */
class BackendController extends Controller
{
    /**
     * @Route("/", name="mws_admin_crud_menu")
     * @Method("GET")
     */
    public function indexAction(Request $request) {
        $config['one_page_show_layout'] = false;

        return $this->render('MWSimpleAdminCrudBundle:Backend:index.html.twig', [
            'config' => $config,
        ]);
    }
    /**
     * AJAX Enabled Disabled
     * @Route("/mws_ajax_enabled_disabled", name="mws_ajax_enabled_disabled")
     * @Method("POST")
     */
    public function ajaxEnabledDisabledAction(Request $request) {
        $res = false;
        $fieldname = $request->request->get('datafieldname');
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository($request->request->get('repository'))->find($request->request->get('dataid'));

        if ($entity) {
            $fieldname_value = null;
            if (method_exists($entity, "is".$fieldname)) {
                $fieldname_value = "is".$fieldname;
            } elseif (method_exists($entity, "get".$fieldname)) {
                $fieldname_value = "get".$fieldname;
            }

            if (!is_null($fieldname_value) && method_exists($entity, "set".$fieldname)) {
                $fieldname_set = "set".$fieldname;

                try {
                    if ($entity->$fieldname_value()) {
                        $entity->$fieldname_set(false);
                    } else {
                        $entity->$fieldname_set(true);
                    }

                    $em->flush();
                    $res = true;
                } catch (Exception $e) {}
            }
        }

        $response = new JsonResponse();
        $response->setData($res);

        return $response;
    }
}
