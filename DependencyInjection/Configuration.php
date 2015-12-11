<?php

namespace MWSimple\Bundle\AdminCrudBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('mw_simple_admin_crud');

        $this->addMenuSettingSection($rootNode);
        $this->addMenuSection($rootNode);
        $this->addAclSection($rootNode);
        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        return $treeBuilder;
    }

    private function addMenuSettingSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('menu_setting')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('id')->defaultValue(false)->end()
                        ->scalarNode('class')->defaultValue(false)->end()
                    ->end()
                ->end() //setting
            ->end()
        ;
    }

    private function addMenuSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('menu')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('name')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('url')->isRequired()->end()
                            ->scalarNode('icon')->end()
                            ->scalarNode('id')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('class')->end()
                            ->arrayNode('subMenu')
                                    ->prototype('array')
                                        ->children()
                                            ->scalarNode('name')->isRequired()->cannotBeEmpty()->end()
                                            ->scalarNode('url')->isRequired()->cannotBeEmpty()->end()
                                            ->scalarNode('icon')->end()
                                            ->scalarNode('id')->end()
                                            ->scalarNode('class')->end()
                                            ->arrayNode('roles')
                                                ->prototype('scalar')
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                            ->end() //submenu
                            ->arrayNode('roles')
                                ->prototype('scalar')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end() //menu
            ->end()
        ;
    }

    private function addAclSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('acl')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('use')->defaultValue(false)->end()
                        ->scalarNode('exclude_role')->defaultValue(false)->end()
                        ->arrayNode('entities')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end() //acl
            ->end()
        ;
    }

}