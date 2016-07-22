### Subir archivos
Se incluye VichUploaderBundle para configurar
* [Documentación](https://github.com/dustin10/VichUploaderBundle/blob/master/Resources/doc/usage.md)
* [Guardar nombre de archivos](https://github.com/dustin10/VichUploaderBundle/blob/master/Resources/doc/namers.md)
* [image form type](https://github.com/dustin10/VichUploaderBundle/blob/master/Resources/doc/form/vich_image_type.md)

#### Utilizar VichImageType::class para especificar el tipo de campo:
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

#### Utilizar VichFileType::class para especificar el tipo de campo:
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

* Configuración para la vista previa de la imagen en el listado:

#### Archivo de configuración generado en: Bundle/Resources/config/Post.yml (Por ejemplo si la Entity fuera Post)
```yaml
fieldsindex: #Los campos que aparecen en la lista de índices
    a.image:
        label: 'Imagen' #Nombre que muestra
        name: 'Imagen' #Nombre del campo
        type: 'vich_file' #Tipo para la vista previa de la imagen
        file: 'imageFile' #El campo @Vich\UploadableField de la entidad
        export: false #Setear en false
fieldsshow: #Los campos que aparecen en el ver Entity
    a.imagen:
        ...
        type: 'vich_file' #Tipo para la vista previa de la imagen
        file: 'imageFile' #El campo @Vich\UploadableField de la entidad
        ...
```

[AdminCrud Documentación](Resources/doc/documentacion.md)
[README](https://github.com/MWSimple/AdminCrudBundle/blob/version30/README.md)
