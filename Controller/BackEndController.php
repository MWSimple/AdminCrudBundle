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
        return $this->render('MWSimpleAdminCrudBundle:Backend:index.html.twig', [
            'config' => null,
        ]);
    }
    /**
     * AJAX Enabled Disabled
     * @Route("/mws_ajax_enabled_disabled", name="mws_ajax_enabled_disabled")
     * @Method("POST")
     */
    public function ajaxEnabledDisabled(Request $request)
    {
        $repository = $request->request->get('repository');
        $id = $request->request->get('dataid');
        $fieldname = $request->request->get('datafieldname');

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository($repository)->find($id);

        if ($entity) {
            $fieldname_get = "get".$fieldname;
            $fieldname_set = "set".$fieldname;

            try {
                if ($entity->$fieldname_get()) {
                    $entity->$fieldname_set(false);
                } else {
                    $entity->$fieldname_set(true);
                }

                $em->flush();

                $array = array(
                    'resultado' => true,
                    'mensaje'   => "Ok"
                );
            } catch (Exception $e) {
                $array = array(
                    'resultado' => false,
                    'mensaje'   => "Error"
                );
            }
        } else {
            $array = array(
                'resultado' => false,
                'mensaje'   => "Error"
            );
        }

        $response = new JsonResponse();
        $response->setData($array);

        return $response;
    }
}
