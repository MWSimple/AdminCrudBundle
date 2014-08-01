<?php

namespace MWSimple\Bundle\AdminCrudBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entity to JSON Many To Many
 */
class EntityToJsonTransformer implements DataTransformerInterface
{
    /**
     * Class para conectarse
     */
    private $class;

    /**
     * ObjectManager
     */
    private $om;

    /***
     * Constructor
     */
    public function __construct($dataConnect)
    {
        $this->class = $dataConnect['class'];
        $this->om = $dataConnect['om'];
    }

    /**
     * {@inheritdoc}
     */
    public function transform($entities)
    {
        if (!$entities) {
            return null;
        };
        $jsonResponse = array();
        $jsonResponse = $entities->map(function ($entity) {
            return array(
                'id' => $entity->getId(),
                'text' => $entity->__toString()
            );
        })->toArray();

        return json_encode($jsonResponse);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($json)
    {
        $om = $this->om;
        $class = $this->class;
        $entitiesResponse = new ArrayCollection();
        if (!$json) {
            return $entitiesResponse;
        }
        $jEntities = json_decode($json, true);
        foreach ($jEntities as $j) {
            $entity = $om
                ->getRepository($class)
                ->findOneBy(array('id' => $j['id']))
            ;
            if (!$entitiesResponse->contains($entity)) {
                $entitiesResponse->add($entity);
           }
        }

        return $entitiesResponse;
    }
}