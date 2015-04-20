<?php

namespace MWSimple\Bundle\AdminCrudBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware {

    public function adminMenu(FactoryInterface $factory, array $options) {

        $arrayMenu = $this->container->getParameter('mw_simple_admin_crud.menu');

        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', $arrayMenu['setting']['class']);
        $menu->setChildrenAttribute('id', $arrayMenu['setting']['id']);
        foreach ($arrayMenu as $key => $m) {
            if ($key != 'setting') {
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

        return $menu;
    }

}
