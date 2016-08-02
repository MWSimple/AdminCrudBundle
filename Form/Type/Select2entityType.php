<?php

namespace MWSimple\Bundle\AdminCrudBundle\Form\Type;

/**
 * This file is part of the GenemuFormBundle package.
 *
 * (c) Olivier Chauvel <olivier@generation-multiple.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Doctrine\Common\Persistence\ObjectManager;
use MWSimple\Bundle\AdminCrudBundle\Form\DataTransformer\EntityToJsonTransformer;
use MWSimple\Bundle\AdminCrudBundle\Form\DataTransformer\EntityToJsonOneTransformer;

/**
 * Select2entityType to JQueryLib
 *
 * @author Bilal Amarni <bilal.amarni@gmail.com>
 * @author Chris Tickner <chris.tickner@gmail.com>
 */
class Select2entityType extends AbstractType
{
    /**
     * @var ObjectManager
     */
     private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $dataConnect = array('class' => $options['class'], 'om' => $this->om);
        if ($options['configs']['multiple'] === true) {
            $transformer = new EntityToJsonTransformer($dataConnect);
        } else {
            $transformer = new EntityToJsonOneTransformer($dataConnect);
        }
        $builder
            ->addModelTransformer($transformer)
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['placeholder'] != '') {
            $options['configs']['placeholder'] = $options['placeholder'];
        }
        if (!$options['required']) {
            $options['configs']['allowClear'] = true;
        }
        $view->vars['url'] = $options['url'];
        $view->vars['configs'] = $options['configs'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $defaults = array(
            'placeholder'        => 'Seleccione...',
            'allowClear'         => false,
            'minimumInputLength' => 0,
            'width'              => 'off',
            'multiple'           => true,
            'locked'             => false,
        );

        $resolver
            ->setDefaults(array(
                'configs'     => $defaults,
                'url'         => '',
                'placeholder' => 'Seleccione...',
                )
            )
        ;
        $resolver
            ->setRequired(
                array(
                    'class',
                )
            )
        ;
    }

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
        return 'mws_select2entity';
    }
}
