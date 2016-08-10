<?php

/*
 * Inspirado en EasyAdminBundle.
 */

namespace MWSimple\Bundle\AdminCrudBundle\Configuration;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Administrar configuracion
 */
class ConfigManager
{
    private $backendConfig;
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * getBackendConfig('setting.site_name')
     *
     * @param string|null $propertyPath
     *
     * @return array
     */
    public function getBackendConfig($propertyPath = null)
    {
        if (null === $this->backendConfig) {
            $this->backendConfig = $this->processConfig();
        }

        if (empty($propertyPath)) {
            return $this->backendConfig;
        }

        // turns 'setting.site_name' into '[setting][site_name]', the format required by PropertyAccess
        $propertyPath = '['.str_replace('.', '][', $propertyPath).']';

        return $this->container->get('property_accessor')->getValue($this->backendConfig, $propertyPath);
    }
    /* Proceso la configuracion del bundle mw_simple_admin_crud */
    private function processConfig()
    {
        return $this->container->getParameter('mw_simple_admin_crud.setting');
    }
}
