<?php

namespace MWSimple\Bundle\AdminCrudBundle\Doctrine;

use Doctrine\ORM\Mapping\ClassMetaData,
    Doctrine\ORM\Query\Filter\SQLFilter;

class LogicalErasingFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        // Check if the entity implements the LocalAware interface
        if (!$targetEntity->reflClass->implementsInterface('MWSimple\Bundle\AdminCrudBundle\Entity\LogicalErasingInterface')) {
            return "";
        }

        return $targetTableAlias.'.logical_erasing = 0';
    }
}
