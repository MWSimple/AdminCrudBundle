<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MWSimple\Bundle\AdminCrudBundle\Generator;

use Sensio\Bundle\GeneratorBundle\Generator\DoctrineFormGenerator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Generates a form class based on a Doctrine entity.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Hugo Hamon <hugo.hamon@sensio.com>
 */
class MWSimpleFormGenerator extends DoctrineFormGenerator
{
    /**
     * Generates the entity form class if it does not exist.
     *
     * @param BundleInterface   $bundle   The bundle in which to create the class
     * @param string            $entity   The entity relative class name
     * @param ClassMetadataInfo $metadata The entity metadata class
     */
    public function generate(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata)
    {
        $parts       = explode('\\', $entity);
        $entityClass = array_pop($parts);

        $this->className = $entityClass.'Type';
        $dirPath         = $bundle->getPath().'/Form';
        $this->classPath = $dirPath.'/'.str_replace('\\', '/', $entity).'Type.php';

        if (file_exists($this->classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s form class as it already exists under the %s file', $this->className, $this->classPath));
        }

        if (count($metadata->identifier) > 1) {
            throw new \RuntimeException('The form generator does not support entity classes with multiple primary keys.');
        }

        $parts = explode('\\', $entity);
        array_pop($parts);
        
        $this->renderFile('form/FormType.php.twig', $this->classPath, array(
            'fields'           => $this->getFieldsFromMetadata($metadata),
            'namespace'        => $bundle->getNamespace(),
            'entity_namespace' => implode('\\', $parts),
            'entity_class'     => $entityClass,
            'bundle'           => $bundle->getName(),
            'form_class'       => $this->className,
            'form_type_name'   => strtolower(str_replace('\\', '_', $bundle->getNamespace()).($parts ? '_' : '').implode('_', $parts).'_'.substr($this->className, 0, -4)),
            'associations'     => $this->getFieldsAssociationFromMetadata($metadata),
        ));
    }

    /**
     * Returns an array of fields. Fields can be both column fields and
     * association fields.
     *
     * @param  ClassMetadataInfo $metadata
     * @return array             $fields
     */
    private function getFieldsFromMetadata(ClassMetadataInfo $metadata)
    {
        $fields = (array) $metadata->fieldMappings;

        // Remove the primary key field if it's not managed manually
        if (!$metadata->isIdentifierNatural()) {
            foreach ($metadata->identifier as $id) {
                if(array_key_exists($id, $fields)) {
                    unset($fields[$id]);
                }
            }
        }

        return $fields;
    }

    /**
     * Returns an array of fields data (name and filter widget to use).
     * Fields can be both column fields and association fields.
     *
     * @param  ClassMetadataInfo $metadata
     * @return array             $fields
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
