<?php

namespace MWSimple\Bundle\AdminCrudBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware {

    public function adminMenu(FactoryInterface $factory, array $options) {

        $arrayMenu = $this->container->getParameter('mw_simple_admin_crud.menu');

        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav nav-pills');
        foreach ($arrayMenu as $m) {
            $menu->addChild($m['name'], array('route' => $m['url']));
            if (!empty($m["icon"])) {
                $menu[$m['name']]->setAttribute('icon', $m["icon"]);
            }
        }

        return $menu;
    }

}
