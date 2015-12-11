<?php

namespace MWSimple\Bundle\AdminCrudBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    private $hasRole;

    public function adminMenu(FactoryInterface $factory, array $options) 
    {
        $menu = $factory->createItem('root');

        $arrayMenuSetting = $this->container->getParameter('mw_simple_admin_crud.menu_setting');
        foreach ($arrayMenuSetting as $key => $ms) {
            $menu->setChildrenAttribute($key, $ms);
        }

        $arrayMenu = $this->container->getParameter('mw_simple_admin_crud.menu');
        foreach ($arrayMenu as $key => $m) {
            $this->controlRole($m);
            if ($this->hasRole) {
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
                        $this->controlRole($subMenu);
                        if ($this->hasRole) {
                            if (!empty($m["id"])) {
                                $menu[$m['name']]->setChildrenAttribute('id', $m["id"]."List");
                            }
                            $menu[$m['name']]->setChildrenAttribute('class', 'nav collapse');
                            $menu[$m['name']]->addChild($subMenu['name'], array('route' => $subMenu['url']));
                        }
                    }
                }
            }
        }

        return $menu;
    }

    private function controlRole($m){
        $this->hasRole = false;
        if (empty($m['roles'])) {
            $this->hasRole = true;
        } else {
            foreach ($m['roles'] as $role) {
                if ($this->container->get('security.authorization_checker')->isGranted($role)) {
                    $this->hasRole = true;
                    break;
                }
            }
        }
    }
}