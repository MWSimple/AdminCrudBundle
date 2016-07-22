<?php

namespace MWSimple\Bundle\AdminCrudBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
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
     */
    public function ajaxEnabledDisabled(Request $request)
    {
        dump($request);
    }
}
