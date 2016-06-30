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
    public function transform($entities = null)
    {
        if (is_null($entities)) {
            $return = null;
        } else {
            $jsonResponse = array();
            foreach ($entities as $entity) {
                $arrayEntity = array(
                    'id' => $entity->getId(),
                    'text' => $entity->__toString()
                );
                array_push($jsonResponse, $arrayEntity);
            }

            $return = json_encode($jsonResponse);
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($json = null)
    {
        $entitiesResponse = new ArrayCollection();
        if (!is_null($json)) {
            $jEntities = json_decode($json, true);
            foreach ($jEntities as $j) {
                $entity = $this->om
                    ->getRepository($this->class)
                    ->findOneBy(array('id' => $j['id']))
                ;
                if (!$entitiesResponse->contains($entity)) {
                    $entitiesResponse->add($entity);
               }
            }
        }

        return $entitiesResponse;
    }
}
