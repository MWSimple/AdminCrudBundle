### Uploads files
Se incluye VichUploaderBundle para configurar
* [Documentación](https://github.com/dustin10/VichUploaderBundle/blob/master/Resources/doc/usage.md)
* [file form type](https://github.com/dustin10/VichUploaderBundle/blob/master/Resources/doc/form/vich_file_type.md)
* [image form type](https://github.com/dustin10/VichUploaderBundle/blob/master/Resources/doc/form/vich_image_type.md)
* Settings for the preview image in the list:
#### Archivo de configuración generado en: Bundle/Resources/config/Post.yml (Por ejemplo si la Entity fuera Post)
```yaml
fieldsindex: #Los campos que aparecen en la lista de índices
    a.image:
        label: 'Image' #Label
        name: 'Image' #Field name
        type: 'vich_file' #Type for preview image
        file: 'imageFile' #Field @Vich\UploadableField of the entity
        export: false #Setear false
```

[AdminCrud Documentación](Resources/doc/documentacion.md)
[README](README_EN.md)
