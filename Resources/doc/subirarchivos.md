### Subir archivos
Se incluye VichUploaderBundle para configurar
* [Documentación](https://github.com/dustin10/VichUploaderBundle/blob/master/Resources/doc/usage.md)
* [file form type](https://github.com/dustin10/VichUploaderBundle/blob/master/Resources/doc/form/vich_file_type.md)
* [image form type](https://github.com/dustin10/VichUploaderBundle/blob/master/Resources/doc/form/vich_image_type.md)
* Configuración para la vista previa de la imagen en el listado:
#### File is generate: Bundle/Resources/config/Post.yml (Example Entity is Post)
```yaml
fieldsindex: #Los campos que aparecen en la lista de índices
    a.image:
        label: 'Imagen' #Nombre que muestra
        name: 'Imagen' #Nombre del campo
        type: 'vich_file' #Tipo para la vista previa de la imagen
        file: 'imageFile' #El campo @Vich\UploadableField de la entidad
        export: false #Setear en false
```

[AdminCrud Documentación](Resources/doc/documentacion.md)
[README](https://github.com/MWSimple/AdminCrudBundle/blob/version30/README.md)
