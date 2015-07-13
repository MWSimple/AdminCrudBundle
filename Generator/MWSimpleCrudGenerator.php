<?php

namespace MWSimple\Bundle\AdminCrudBundle\Generator;

use Sensio\Bundle\GeneratorBundle\Generator\DoctrineCrudGenerator;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Generates a CRUD for a Doctrine entity.
 *
 * @author Gonzalo Alonso <gonkpo@gmail.com>
 */
class MWSimpleCrudGenerator extends DoctrineCrudGenerator
{
    public function generate(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata, $format, $routePrefix, $needWriteActions, $forceOverwrite)
    {
        parent::generate($bundle, $entity, $metadata, $format, $routePrefix, $needWriteActions, $forceOverwrite);

        try {
            $this->generateFormFilter($bundle, $entity, $metadata);
        } catch (\RuntimeException $e ) {
            // form already exists
        }

        $dirFileConf = sprintf('%s/Resources/config/', $this->bundle->getPath());
        $this->generateConfAdminCrud($dirFileConf);
    }

    /**
     * Generates the controller class only.
     *
     */
    protected function generateControllerClass($forceOverwrite)
    {
        $dir = $this->bundle->getPath();

        $parts = explode('\\', $this->entity);
        $entityClass = array_pop($parts);
        $entityNamespace = implode('\\', $parts);

        $target = sprintf(
            '%s/Controller/%s/%sController.php',
            $dir,
            str_replace('\\', '/', $entityNamespace),
            $entityClass
        );

        if (!$forceOverwrite && file_exists($target)) {
            throw new \RuntimeException('Unable to generate the controller as it already exists.');
        }

        $this->renderFile('crud/controller.php.twig', $target, array(
            'actions'           => $this->actions,
            'route_prefix'      => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'bundle'            => $this->bundle->getName(),
            'entity'            => $this->entity,
            'entity_class'      => $entityClass,
            'namespace'         => $this->bundle->getNamespace(),
            'entity_namespace'  => $entityNamespace,
            'format'            => $this->format,
            'associations'      => $this->getFieldsAssociationFromMetadata($this->metadata),
        ));
    }

    /** (c) Jordi Llonch <llonch.jordi@gmail.com> */

    /**
     * Generates the entity form class if it does not exist.
     *
     * @param BundleInterface   $bundle   The bundle in which to create the class
     * @param string            $entity   The entity relative class name
     * @param ClassMetadataInfo $metadata The entity metadata class
     */
    public function generateFormFilter(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata)
    {
        $parts       = explode('\\', $entity);
        $entityClass = array_pop($parts);

        $this->className = $entityClass.'FilterType';
        $dirPath         = $bundle->getPath().'/Form';
        $this->classPath = $dirPath.'/'.str_replace('\\', '/', $entity).'FilterType.php';

        if (file_exists($this->classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s form class as it already exists under the %s file', $this->className, $this->classPath));
        }

        if (count($metadata->identifier) > 1) {
            throw new \RuntimeException('The form generator does not support entity classes with multiple primary keys.');
        }

        $parts = explode('\\', $entity);
        array_pop($parts);

        $this->renderFile('form/FormFilterType.php.twig', $this->classPath, array(
            'fields_data'      => $this->getFieldsDataFromMetadata($metadata),
            'namespace'        => $bundle->getNamespace(),
            'entity_namespace' => implode('\\', $parts),
            'entity_class'     => $entityClass,
            'bundle'           => $bundle->getName(),
            'form_class'       => $this->className,
            'form_filter_type_name'   => strtolower(str_replace('\\', '_', $bundle->getNamespace()).($parts ? '_' : '').implode('_', $parts).'_'.$this->className),
        ));
    }

    public function getFilterType($dbType, $columnName)
    {
        switch ($dbType) {
            case 'boolean':
                return 'filter_choice';
            case 'datetime':
            case 'vardatetime':
            case 'datetimetz':
                return 'filter_date_range';
            case 'date':
                return 'filter_date_range';
                break;
            case 'decimal':
            case 'float':
            case 'integer':
            case 'bigint':
            case 'smallint':
                return 'filter_number_range';
                break;
            case 'string':
            case 'text':
                return 'filter_text_like';
                break;
            case 'time':
                return 'filter_text';
                break;
            case 'entity':
            case 'collection':
                return 'filter_entity';
                break;
            case 'array':
                throw new \Exception('The dbType "'.$dbType.'" is only for list implemented (column "'.$columnName.'")');
                break;
            case 'virtual':
                throw new \Exception('The dbType "'.$dbType.'" is only for list implemented (column "'.$columnName.'")');
                break;
            default:
                throw new \Exception('The dbType "'.$dbType.'" is not yet implemented (column "'.$columnName.'")');
                break;
        }
    }

    /**
     * Returns an array of fields data (name and filter widget to use).
     * Fields can be both column fields and association fields.
     *
     * @param ClassMetadataInfo $metadata
     * @return array $fields
     */
    private function getFieldsDataFromMetadata(ClassMetadataInfo $metadata)
    {
        $fieldsData = (array) $metadata->fieldMappings;

        // Convert type to filter widget
        foreach ($fieldsData as $fieldName => $data) {
            $fieldsData[$fieldName]['fieldName'] = $fieldName;
            $fieldsData[$fieldName]['filterWidget'] = $this->getFilterType($fieldsData[$fieldName]['type'], $fieldName);
        }

        return $fieldsData;
    }

    /** (c) Gonzalo Alonso <gonkpo.com> */
    /**
     * Generates the config entity yml.
     *
     * @param BundleInterface   $bundle   The bundle in which to create the class
     * @param string            $entity   The entity relative class name
     * @param ClassMetadataInfo $metadata The entity metadata class
     */
    public function generateConfAdminCrud($dirFileConf)
    {
        $parts = explode('\\', $this->entity);
        $entityClass = array_pop($parts);

        $this->renderFile('admincrud/admin_config.yml.twig', $dirFileConf.$entityClass.'.yml', array(
            'bundle'            => $this->bundle->getName(),
            'namespace'         => $this->bundle->getNamespace(),
            'entity'            => $this->entity,
            'entity_class'      => $entityClass,
            'fields'            => $this->metadata->fieldMappings,
            'associations'      => $this->metadata->associationMappings,
            'route_name_prefix' => $this->routeNamePrefix,
        ));
    }

    /**
     * Returns an array of fields data (name and filter widget to use).
     * Fields can be both column fields and association fields.
     *
     * @param ClassMetadataInfo $metadata
     * @return array $fields
     */
    private function getFieldsAssociationFromMetadata(ClassMetadataInfo $metadata)
    {
        $associations = array();
        foreach ($metadata->associationMappings as $value) {
            $parts = explode('\\', $value['targetEntity']);
            if (count($parts) === 3) {  
                $repository = $parts[0].":".$parts[2];
                $actionName = $parts[2];
            } else {
                if ($parts[1] == "Bundle") {
                    $repository = $parts[0].$parts[2].":".$parts[4];
                    $actionName = $parts[4];
                } else {
                    $repository = $parts[0].$parts[1].":".$parts[3];
                    $actionName = $parts[3];
                }
            }
            $associations[$value['fieldName']]['targetEntity'] = $value['targetEntity'];
            $associations[$value['fieldName']]['repository'] = $repository;
            $associations[$value['fieldName']]['actionName'] = $actionName;
            $associations[$value['fieldName']]['type'] = $value['type'];
        }

        return $associations;
    }
}