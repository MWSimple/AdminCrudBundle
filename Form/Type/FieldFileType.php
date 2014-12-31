<?php

namespace MWSimple\Bundle\AdminCrudBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * FieldFile class
 *
 * @author MWS
 */
class FieldFileType extends FileType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setOptional(array('file_path'));
        $resolver->setOptional(array('show_path'));
        $resolver->setDefaults(array(
            'compound'   => false,
            'data_class' => 'Symfony\Component\HttpFoundation\File\File',
            'empty_data' => null,
            'show_path'  => false,
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars = array_replace($view->vars, array(
            'type'  => 'file',
            'value' => '',
        ));
        if (array_key_exists('file_path', $options)) {
            $parentData = $form->getParent()->getData();
            if (!is_null($parentData)) {
                $accessor = PropertyAccess::getPropertyAccessor();
                $imageUrl = $accessor->getValue($parentData, $options['file_path']);
                $value = $accessor->getValue($parentData, 'filePath');
            } else {
                $imageUrl = null;
                $value = null;
            }
            $view->vars['file_url'] = $imageUrl;
            $view->vars['value'] = $value;
        }
        $view->vars['file_path'] = $options['show_path'];
    }
    public function getParent()
    {
        return 'file';
    }

    public function getName()
    {
        return 'mws_field_file';
    }

}