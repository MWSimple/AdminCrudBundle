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
    //Agregado por Tecspro para setear el servicio
    private $mws_tecspro_comun;

    public function setMwsTecsproComun($mws_tecspro_comun) {
        $this->mws_tecspro_comun = $mws_tecspro_comun;
    }

    /**
     * Generate the CRUD controller.
     *
     * @param BundleInterface   $bundle           A bundle object
     * @param string            $entity           The entity relative class name
     * @param ClassMetadataInfo $metadata         The entity class metadata
     * @param string            $format           The configuration format (xml, yaml, annotation)
     * @param string            $routePrefix      The route name prefix
     * @param bool              $needWriteActions Whether or not to generate write actions
     * @param bool              $forceOverwrite   Whether or not to overwrite the controller
     *
     * @throws \RuntimeException
     */
    public function generate(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata, $format, $routePrefix, $needWriteActions, $forceOverwrite)
    {
        //Si es No es AppBundle entra
        if ($bundle->getName() <> "AppBundle") {
            //Reescribo el root dir para que sea el del bundle y cree ahi las views
            $this->rootDir = $bundle->getPath();
        }
        parent::generate($bundle, $entity, $metadata, $format, $routePrefix, $needWriteActions, $forceOverwrite);

        try {
            $this->generateFormFilter($bundle, $entity, $metadata, $forceOverwrite);
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
            'actions' => $this->actions,
            'route_prefix' => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'bundle' => $this->bundle->getName(),
            'entity' => $this->entity,
            'entity_singularized' => $this->entitySingularized,
            'entity_pluralized' => $this->entityPluralized,
            'identifier' => $this->metadata->identifier[0],
            'entity_class' => $entityClass,
            'namespace' => $this->bundle->getNamespace(),
            'entity_namespace' => $entityNamespace,
            'format' => $this->format,
            // BC with Symfony 2.7
            'use_form_type_instance' => !method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix'),
            // AdminCrud
            'associations' => $this->mws_tecspro_comun->getFieldsAssociationFromMetadata($this->metadata),
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
    public function generateFormFilter(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata, $forceOverwrite)
    {
        $parts       = explode('\\', $entity);
        $entityClass = array_pop($parts);

        $this->className = $entityClass.'FilterType';
        $dirPath         = $bundle->getPath().'/Form';
        $this->classPath = $dirPath.'/'.str_replace('\\', '/', $entity).'FilterType.php';

        if (!$forceOverwrite && file_exists($this->classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s form class as it already exists under the %s file', $this->className, $this->classPath));
        }

        if (count($metadata->identifier) > 1) {
            throw new \RuntimeException('The form generator does not support entity classes with multiple primary keys.');
        }

        $parts = explode('\\', $entity);
        array_pop($parts);

        $this->renderFile('form/FormFilterType.php.twig', $this->classPath, array(
            'namespace' => $bundle->getNamespace(),
            'entity_namespace' => implode('\\', $parts),
            'entity_class' => $entityClass,
            'bundle' => $bundle->getName(),
            'form_class' => $this->className,
            // BC with Symfony 2.7
            'get_name_required' => !method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix'),
            // AdminCrud
            'fields_data' => $this->getFieldsDataFromMetadata($metadata),
            'form_filter_type_name' => strtolower(str_replace('\\', '_', $bundle->getNamespace()).($parts ? '_' : '').implode('_', $parts).'_'.$this->className),
        ));
    }

    public function getFilterType($dbType, $columnName)
    {
        switch ($dbType) {
            case 'boolean':
                return 'Filters\BooleanFilterType::class';
            case 'datetime':
            case 'vardatetime':
            case 'datetimetz':
                return 'Filters\DateTimeRangeFilterType::class';
            case 'date':
                return 'Filters\DateRangeFilterType::class';
                break;
            case 'decimal':
            case 'float':
            case 'integer':
            case 'bigint':
            case 'smallint':
                return 'Filters\NumberRangeFilterType::class';
                break;
            case 'string':
            case 'text':
                return 'Filters\TextFilterType::class';
                break;
            case 'time':
                return 'Filters\TextFilterType::class';
                break;
            case 'entity':
            case 'collection':
                return 'EntityFilterType';
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
        // The dbType is not yet implemented
        $fieldsNotImplemented = ['array', 'virtual', 'guid'];
        // Convert type to filter widget
        foreach ($fieldsData as $fieldName => $data) {
            if (!in_array($fieldsData[$fieldName]['type'], $fieldsNotImplemented)) {
                $fieldsData[$fieldName]['fieldName']    = $fieldName;
                $fieldsData[$fieldName]['filterWidget'] = $this->getFilterType($fieldsData[$fieldName]['type'], $fieldName);
            }
        }

        return $fieldsData;
    }

    /**
     * (c) Gonzalo Alonso <gonkpo.com>
     * Genero el archivo de configuracion para el
     */
    public function generateConfAdminCrud($dirFileConf)
    {
        $parts = explode('\\', $this->entity);
        $entityClass = array_pop($parts);

        //Si es No es AppBundle entra
        if ($this->bundle->getName() <> "AppBundle") {
            //Reescribo el root dir para que sea el del bundle y cree ahi las views
            $entity_dir_view = $this->bundle->getName().":".str_replace('\\', '/', strtolower($this->entity)).":";
        } else {
            $entity_dir_view = str_replace('\\', '/', strtolower($this->entity))."/";
        }

        $this->renderFile('admincrud/admin_config.yml.twig', $dirFileConf.$entityClass.'.yml', array(
            'actions'           => $this->actions,
            'bundle'            => $this->bundle->getName(),
            'namespace'         => $this->bundle->getNamespace(),
            'entity'            => $this->entity,
            'entity_class'      => $entityClass,
            'entity_dir_view'   => $entity_dir_view,
            'fields'            => $this->metadata->fieldMappings,
            'associations'      => $this->metadata->associationMappings,
            'route_name_prefix' => $this->routeNamePrefix,
        ));
    }
}
