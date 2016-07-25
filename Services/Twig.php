<?php
namespace MWSimple\Bundle\AdminCrudBundle\Services;

/**
 * Twig Extension.
 *
 * @author Gonzalo Alonso <gonkpo@gmail.com>
 */

class Twig extends \Twig_Extension
{
    //Functions
    public function getFunctions()
    {
        return array(
            'isActive' => new \Twig_Function_Method($this, 'isActive', array(
                'is_safe' => array('html')
            )),
            'ajaxActive' => new \Twig_Function_Method($this, 'ajaxActive', array(
                'is_safe' => array('html')
            )),
        );
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
