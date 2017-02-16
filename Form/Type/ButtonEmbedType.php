<?php

namespace MWSimple\Bundle\AdminCrudBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class ButtonEmbedType extends AbstractType
{
    public function getParent()
    {
        return HiddenType::class;
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
        return 'mws_button_embed';
    }
}
