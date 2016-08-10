<?php

namespace MWSimple\Bundle\AdminCrudBundle\Services;

use MWSimple\Bundle\AdminCrudBundle\Configuration\ConfigManager;

/**
 * Twig Extension.
 *
 * @author Gonzalo Alonso <gonkpo@gmail.com>
 */

class Twig extends \Twig_Extension
{
    private $configManager;

    public function __construct(ConfigManager $configManager)
    {
        $this->configManager = $configManager;
    }

    //Functions
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('admincrud_config', array($this, 'getBackendConfiguration')),
            'isActive' => new \Twig_Function_Method($this, 'isActive', array(
                'is_safe' => array('html')
            )),
            'ajaxActive' => new \Twig_Function_Method($this, 'ajaxActive', array(
                'is_safe' => array('html')
            )),
        );
    }

    /**
     * getBackendConfiguration 'setting.site_name' => $config['setting']['site_name'].
     *
     * @param string|null $key
     *
     * @return mixed
     */
    public function getBackendConfiguration($key = null)
    {
        return $this->configManager->getBackendConfig($key);
    }

    //Return icon by Enabled or Disabled
    public function isActive($active, $title_true = null, $title_false = null)
    {
        if ($active) {
            if ($title_true) {
                $res = '<i style="color: green;" class="glyphicon glyphicon-ok-circle tooltips" rel="tooltip" title="'.$title_true.'"></i>';
            } else {
                $res = '<i style="color: green;" class="glyphicon glyphicon-ok-circle"></i>';
            }
        } else {
            if ($title_false) {
                $res = '<i style="color: darkred;" class="glyphicon glyphicon-ban-circle tooltips" rel="tooltip" title="'.$title_false.'"></i>';
            } else {
                $res = '<i style="color: darkred;" class="glyphicon glyphicon-ban-circle"></i>';
            }
        }

        return $res;
    }

    //Enabled or Disabled by Ajax
    public function ajaxActive($active, $repository = null, $field_name = null, $id = null, $title_true = null, $title_false = null)
    {
        $res = '<input class="mws_checkbox" type="checkbox" value="'.$active.'" data-repository="'.$repository.'" data-fieldname="'.$field_name.'" data-id="'.$id.'"';
        if ($active) {
            $res = $res.' checked="checked">';
        } else {
            $res = $res.'>';
        }

        return $res;
    }

    public function getName()
    {
        return 'twig.extension';
    }
}
