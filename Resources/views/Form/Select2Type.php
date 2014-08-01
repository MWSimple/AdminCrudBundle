<?php

namespace Sistema\MWSFORMBundle\Form\Type;

/**
 * This file is part of the GenemuFormBundle package.
 *
 * (c) Olivier Chauvel <olivier@generation-multiple.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Sistema\MWSFORMBundle\Form\DataTransformer\EntityToJsonTransformer;
use Sistema\MWSFORMBundle\Form\DataTransformer\EntityToJsonOneTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;

use Doctrine\Common\Persistence\ObjectManager;

/**
 * Select2Type to JQueryLib
 *
 * @author Bilal Amarni <bilal.amarni@gmail.com>
 * @author Chris Tickner <chris.tickner@gmail.com>
 */
class Select2Type extends AbstractType
{
     /**
     * @var ObjectManager
     **/
     private $om;

    /**
    * @param ObjectManager $om
    **/
    public function __construct(ObjectManager $om)
    {
              $this->om = $om;
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {

        if ($options['placeholder'] != '') {
            $options['configs']['placeholder'] = $options['placeholder'];
        }
        $view->vars['url'] = $options['url'];
        $view->vars['configs'] = $options['configs'];
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $defaults = array(
            'placeholder'        => 'Ingrese valor...',
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
                'placeholder' => '',
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
        return 'hidden';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'select2';
    }
}
