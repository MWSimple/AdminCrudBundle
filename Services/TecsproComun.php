<?php
namespace MWSimple\Bundle\AdminCrudBundle\Services;

use Doctrine\ORM\Mapping\ClassMetadataInfo;

class TecsproComun
{
    public function getFieldsAssociationFromMetadata(ClassMetadataInfo $metadata)
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
