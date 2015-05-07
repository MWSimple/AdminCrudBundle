<?php

namespace MWSimple\Bundle\AdminCrudBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware {

    public function adminMenu(FactoryInterface $factory, array $options) {

        $array = $this->container->get('security.context')->getToken()->getRoles();

        // $array = array();
        // foreach ($arrayRoles as $valor) {
        //     array_push($array, $valor->getName());
        // }

        $arrayMenu = $this->container->getParameter('mw_simple_admin_crud.menu');

        $menu = $factory->createItem('root');
        if (!empty($arrayMenu['setting']['id'])) {
            $menu->setChildrenAttribute('id', $arrayMenu['setting']['id']);
        }
        if (!empty($arrayMenu['setting']['class'])) {
            $menu->setChildrenAttribute('class', $arrayMenu['setting']['class']);
        }
        foreach ($arrayMenu as $key => $m) {
            if ($key != 'setting') {
                $exist = false;
                if (empty($m['roles'])) {
                    $exist = true;
                }
                foreach ($m['roles'] as $r) {
                    if (in_array($r, $array)) {
                        $exist = true;
                    }
                }
                if ($exist) {
                    if (!empty($m['url'])) {
                        $menu->addChild($m['name'], array('route' => $m['url']));
                    } else {
                        $menu->addChild($m['name']);
                    }
                    if (!empty($m["icon"])) {
                        $menu[$m['name']]->setAttribute('icon', $m["icon"]);
                    }
                    if (!empty($m["id"])) {
                        $menu[$m['name']]->setAttribute('id', $m["id"]);
                    }

                    if (!empty($m['subMenu'])) {
                        foreach ($m['subMenu'] as $subMenu) {
                            $menu[$m['name']]->setChildrenAttribute('class', 'dropdown-menu');
                            $menu[$m['name']]->addChild($subMenu['name'], array('route' => $subMenu['url']));
                        }
                    }
                }
            }
        }

        return $menu;
    }

}
