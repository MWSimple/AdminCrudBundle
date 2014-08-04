<?php

namespace MWSimple\Bundle\AdminCrudBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Entity to JSON Many To Many
 */
class EntityToJsonOneTransformer implements DataTransformerInterface
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
        if (is_array($entities)) {
            if (array_key_exists(0, $entities)) {
                $jsonResponse = $entities->map(function ($entity) {
                    return array(
                        'id' => $entity->getId(),
                        'text' => $entity->__toString()
                    );
                })->toArray();
            } else {
                $jsonResponse = array(
                    'id'   => $entities->getId(),
                    'text' => $entities->__toString()
                );
            }
        } else {
            $om = $this->om;
            $class = $this->class;
            $entity = $om
                ->getRepository($class)
                ->findOneBy(array('id' => $entities))
            ;
            $jsonResponse = array(
                'id'   => $entity->getId(),
                'text' => $entity->__toString()
            );
        }

        return json_encode($jsonResponse);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($json)
    {
        $om = $this->om;
        $class = $this->class;
        $entityResponse = null;
        if (!$json) {
            return $entityResponse;
        }
        $jEntities = json_decode($json, true);
        if (array_key_exists(0, $jEntities)) {
            foreach ($jEntities as $j) {
                $entity = $om
                    ->getRepository($class)
                    ->findOneBy(array('id' => $j['id']))
                ;
                if ($entity) {
                    $entityResponse = $entity;
                }
            }
        } else {
            $entity = $om
                ->getRepository($class)
                ->findOneBy(array('id' => $jEntities['id']))
            ;
            if ($entity) {
                $entityResponse = $entity;
            }
        }

        return $entityResponse;
    }
}
