<?php

namespace MWSimple\Bundle\AdminCrudBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Entity to JSON Many To Many
 */
class EntityToJsonOneTransformerArticulo implements DataTransformerInterface
{
    /**
     * Class para conectarse
     */
    private $class;

    /**
     * ObjectManager
     */
    private $om;

    /**
     * devuelve o no cantidadMaxima
     */
    private $cantidadMaxima;

    /**
     * devuelve valorReal o valor cantidadMaxima
     */
    private $valCantidadMaxima;

    /***
     * Constructor
     */
    public function __construct($dataConnect)
    {
        $this->class = $dataConnect['class'];
        $this->om = $dataConnect['om'];
        $this->cantidadMaxima = $dataConnect['cantidadMaxima'];
        $this->valCantidadMaxima = $dataConnect['valCantidadMaxima'];
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
                    $array = array(
                        'id' => $entity->getId(),
                        'text' => $entity->__toString(),
                    );
                    if ($this->cantidadMaxima) {
                        if ($this->valCantidadMaxima) {
                            $array['cantidadMaxima'] = $entity->getCantidad();
                        } else {
                            $array['cantidadMaxima'] = $entity->getCantidadReal();
                        }
                    }
                    return $array;
                })->toArray();
            } else {
                $cliente = array(
                    'id'   => $entities->getId(),
                    'text' => $entities->__toString(),
                );
                if ($this->cantidadMaxima) {
                    if ($this->valCantidadMaxima) {
                            $cliente['cantidadMaxima'] = $entity->getCantidad();
                        } else {
                            $cliente['cantidadMaxima'] = $entity->getCantidadReal();
                        }
                }
                $jsonResponse = $cliente;
            }
        } else {
            $om = $this->om;
            $class = $this->class;
            $entity = $om
                ->getRepository($class)
                ->findOneBy(array('id' => $entities))
            ;
            $cliente = array(
                'id'   => $entity->getId(),
                'text' => $entity->__toString(),
            );
            if ($this->cantidadMaxima) {
                if ($this->valCantidadMaxima) {
                    $cliente['cantidadMaxima'] = $entity->getCantidad();
                } else {
                    $cliente['cantidadMaxima'] = $entity->getCantidadReal();
                }
            }
            $jsonResponse = $cliente;
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
