<?php

namespace MWSimple\Bundle\AdminCrudBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware {

    public function adminMenu(FactoryInterface $factory, array $options) {

        $arrayMenu = $this->container->getParameter('mw_simple_admin_crud.menu');

        $arrayMenuConfig = $this->container->getParameter('mw_simple_admin_crud.menu_setting');

        $this->translator = $this->container->get('translator');

        $menu = $factory->createItem('root');

        $this->setConfiguracionMenuRoot($menu, $arrayMenu);

        $this->crearChildren($menu, $arrayMenu, $arrayMenuConfig);

        return $menu;
    }

    public function crearChildren(&$menu, $children, &$arrayMenuConfig) {

        $translator = $this->container->get('translator');
        
        $translation = null;

        if(isset($arrayMenuConfig['translation'])) {

            $translation = $arrayMenuConfig['translation'];
        }

        foreach ($children as $key => $m) {

            if (isset($m['setting']['title'])) {

                $name_traslated = $translator->trans($m['setting']['title'], array(), $translation);

                $m['setting']['title'] = $name_traslated;
            }

            if ($key != 'setting') {

                if (empty($m['roles'])) {

                    $exist = true;
                } else {

                    $exist = false;

                    if ($this->container->get('security.authorization_checker')->isGranted($m['roles'])) {

                        $exist = true;
                    }
                }

                $name_traslated = $translator->trans($m['name'], array(), $translation);

                $m['name'] = $name_traslated;

                if ($exist) {

                    if (isset($m['url'])) {

                        $menu->addChild($m['name'], array('route' => $m['url']));

                    } else {

                        $menu->addChild($m['name']);
                    }
                    if (isset($m['setting'])) {

                        $this->setConfiguracionMenuChildren($menu, $m);
                    }

                    if (!empty($m['subMenu'])) {

                        if (isset($m['subMenu']['setting'])) {

                            $this->setConfiguracionMenuRoot($menu[$m['name']], $m['subMenu']);
                        }

                        $menu_translated = $menu;

                        $name_traslated = $translator->trans($menu->getName(), array(), $translation);

                        $menu_translated->setName($name_traslated);

                        $this->crearChildren($menu_translated[$m['name']], $m['subMenu'], $arrayMenuConfig);
                    }
                }
            }
        }
    }

    public function setConfiguracionMenuRoot(&$menu, $configuracion) {

        $settings = array_keys($configuracion['setting']);
        foreach ($settings as $setting) {
            if (!empty($configuracion['setting'][$setting]))
                $menu->setChildrenAttribute($setting, $configuracion['setting'][$setting]);
        }
    }

    public function setConfiguracionMenuChildren(&$menu, $configuracion) {
        $settings = array_keys($configuracion['setting']);
        foreach ($settings as $setting) {
            if (!empty($configuracion['setting'][$setting]))
                $menu[$configuracion['name']]->setAttribute($setting, $configuracion['setting'][$setting]);
        }
    }
}
