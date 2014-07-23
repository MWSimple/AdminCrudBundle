<?php

namespace MWSimple\Bundle\AdminCrudBundle\Menu;


use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Yaml\Yaml;

class Builder extends ContainerAware {

    public function adminMenu(FactoryInterface $factory, array $options) {
        //$arrayMenu = Yaml::parse(file_get_contents('menu.yml'));
        
        $arrayMenu = Yaml::parse(file_get_contents(realpath('../app/config/menu.yml')));
        
        $menu = $factory->createItem('root');
        //ladybug_dump_die($arrayMenu);
        foreach ($arrayMenu as $m) {
            foreach ($m['menu'] as $mn) {
                $menu->addChild($mn['name'], array('route' => null/*$mn['url']*/)); 
            }
        }
        
        
        
        

        /*$arrayMenu = Yaml::parse(file_get_contents($this->get('kernel')->getRootDir() . '/../src/menu.yml'));
        $mFactory = new MenuFactory();
        ladybug_dump_die($arrayMenu);
        $menu = $factory->createItem('root');
        
        foreach ($arrayMenu as $m) {
            //si child esta vacia devuelve true
            if(!empty($m['child'])){
                //name del submenu
                $menu->addChild($m['name'])->setAttribute('dropdown', true);
                foreach ($m['submenu'] as $m) {
                    
                }
            }else{
                $menu->addChild($m['name'], array('route' => $m['url']));
            }
        }*/


        return $menu;
    }

}
