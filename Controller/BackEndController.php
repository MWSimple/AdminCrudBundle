<?php

namespace MWSimple\Bundle\AdminCrudBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Yaml\Yaml;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/admincrud")
 */
class BackEndController extends Controller {

    /**
     * @Route("/", name="mws_admin_crud_menu")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        return array(
            'config' => 1,
        );
    }

}
