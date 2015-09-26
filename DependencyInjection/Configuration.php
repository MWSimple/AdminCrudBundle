<?php

namespace MWSimple\Bundle\AdminCrudBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('mw_simple_admin_crud');

        $this->addMenuSection($rootNode);
        $this->addAclSection($rootNode);
        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        return $treeBuilder;
    }

    private function addMenuSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('menu')
                    ->prototype('array')
                         ->children()
                            ->scalarNode('class')->end()
                         ->end()
                        ->children()
                            ->scalarNode('name')->end()
                            ->scalarNode('url')->end()
                            ->scalarNode('icon')->end()
                            ->scalarNode('id')->end()
                            ->arrayNode('subMenu')
                                    ->prototype('array')
                                        ->children()
                                            ->scalarNode('name')->end()
                                            ->scalarNode('url')->end()
                                            ->scalarNode('icon')->end()
                                            ->scalarNode('id')->end()
                                            ->arrayNode('roles')
                                                ->prototype('array')
                                                    ->children()
                                                            ->scalarNode('name')->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                            ->end() //menu
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
