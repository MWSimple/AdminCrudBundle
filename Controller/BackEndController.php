<?php

namespace MWSimple\Bundle\AdminCrudBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Yaml\Yaml;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Post controller.
 * @author Gonzalo Alonso <gonkpo@gmail.com>
 *
 * @Route("/admincrud")
 */
class BackEndController extends Controller
{
     
    /**
     * Lists all Post entities.
     *
     * @Route("/", name="mws_admin_crud_menu")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
       
        return array(
        	'config'     => 1,
        );
    }

}