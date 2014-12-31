<?php

namespace MWSimple\Bundle\AdminCrudBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

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
        $resolver->setRequired(array('file_path'));
        $resolver->setDefaults(array(
            'file_type'  => false,
            'compound'   => false,
            'data_class' => 'Symfony\Component\HttpFoundation\File\File',
            'empty_data' => null,
            'show_path'  => false,
            'dir_tmp'    => false,
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $data = $form->getViewData();

        parent::buildView($view, $form, $options);

        $view->vars = array_replace($view->vars, array(
            'type'      => 'file',
            'value'     => '',
            'show_path' => $options['show_path'],
        ));
        if (array_key_exists('file_path', $options)) {
            $parentData = $form->getParent()->getData();
            if (!is_null($parentData)) {
                $accessor = PropertyAccess::getPropertyAccessor();
                $imageUrl = $accessor->getValue($parentData, $options['file_path']);
                $value    = $accessor->getValue($parentData, 'filePath');
                if ($options['dir_tmp'] && is_null($value) && count($form->getErrors()) <= 0 && $data != 0) {
                    $imageUrl = $this->saveFileTemp($data, $options);
                }
            } else {
                $imageUrl = null;
                $value    = null;
            }
            $view->vars['file_path'] = $imageUrl;
            $view->vars['value']     = $value;
        }
    }
    public function getParent()
    {
        return 'file';
    }

    public function getName()
    {
        return 'mws_field_file';
    }

    private function saveFileTemp($data, $options) {
        $uploadDir = __DIR__ . '/../../../../../../../../web/'.$options['dir_tmp'];
        $filename = $data->getFilename().'.'.$data->guessExtension();
        $data->move($uploadDir, $filename);
        $imageUrl = $options['dir_tmp']."/".$filename;

        return $imageUrl;
    }

}