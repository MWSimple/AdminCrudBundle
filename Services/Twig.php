<?php
namespace MWSimple\Bundle\AdminCrudBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Twig Extension.
 *
 * @author Gonzalo Alonso <gonkpo@gmail.com>
 */

class Twig extends \Twig_Extension
{
    protected $container;

    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            'isActive' => new \Twig_Function_Method($this, 'isActive', array(
                'is_safe' => array('html')
            )),
        );
    }

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

    public function getName()
    {
        return 'twig.extension';
    }
}
