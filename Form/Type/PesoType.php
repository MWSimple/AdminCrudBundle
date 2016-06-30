<?php

namespace MWSimple\Bundle\AdminCrudBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class PesoType extends AbstractType
{
    public function getParent()
    {
        return NumberType::class;
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * Symfony 2.8+
     *
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'mws_peso';
    }
}
