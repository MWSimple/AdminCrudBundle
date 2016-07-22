### Uploads files
Se incluye VichUploaderBundle para configurar
* [Documentación](https://github.com/dustin10/VichUploaderBundle/blob/master/Resources/doc/usage.md)
* [image form type](https://github.com/dustin10/VichUploaderBundle/blob/master/Resources/doc/form/vich_image_type.md)

#### Use the VichImageType::class to specify the field type:
```php
// ...
use Vich\UploaderBundle\Form\Type\VichImageType;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // ...

        $builder->add('imageFile', VichImageType::class, array(
            'required'      => false,
            'allow_delete'  => true, // not mandatory, default is true
            'download_link' => true, // not mandatory, default is true
        ));
    }
}
```

* [file form type](https://github.com/dustin10/VichUploaderBundle/blob/master/Resources/doc/form/vich_file_type.md)

#### Use the VichFileType::class to specify the field type:
```php
// ...
use Vich\UploaderBundle\Form\Type\VichFileType;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // ...

        $builder->add('imageFile', VichFileType::class, array(
            'required'      => false,
            'allow_delete'  => true, // not mandatory, default is true
            'download_link' => true, // not mandatory, default is true
        ));
    }
}
```

* Settings for the preview image in the list:

#### File is generate: Bundle/Resources/config/Post.yml (Example Entity is Post)
```yaml
fieldsindex: #Fields displayed in the index list
    a.image:
        label: 'Image' #Label
        name: 'Image' #Field name
        type: 'vich_file' #Type for preview image
        file: 'imageFile' #Field @Vich\UploadableField of the entity
        export: false #Setear false
fieldsshow: #Fields displayed in the show Entity
    a.image:
        ...
        type: 'vich_file' #Type for preview image
        file: 'imageFile' #Field @Vich\UploadableField of the entity
        ...
```

[AdminCrud Documentación](Resources/doc/documentacion.md)
[README](README_EN.md)
