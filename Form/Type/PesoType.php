<?php

namespace MWSimple\Bundle\AdminCrudBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

class PesoType extends AbstractType
{
    public function getParent()
    {
        return 'number';
    }

    public function getName()
    {
        return 'mwspeso';
    }
}