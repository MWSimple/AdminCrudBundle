<?php

namespace MWSimple\Bundle\AdminCrudBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EntityType;

class DualListType extends AbstractType
{
    public function getParent()
    {
        return EntityType::class;
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
        return 'mws_duallist';
    }
}
