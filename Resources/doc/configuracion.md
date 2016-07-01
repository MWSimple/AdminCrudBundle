## Configuración

### Archivo de configuración generado en: Bundle/Resources/config/Post.yml (Por ejemplo si la Entity fuera Post)
```yaml
entityName: 'Post' #Nombre de la Entity
entity: 'Sistema\CPCEBundle\Entity\Post' #Ubicacion
repository: 'SistemaCPCEBundle:Post' #Repositorio
index: 'admin_post' #Rutas para enrutamiento
new: 'admin_post_new'
edit: 'admin_post_edit'
show: 'admin_post_show'
create: 'admin_post_create'
update: 'admin_post_update'
delete: 'admin_post_delete'
export: 'admin_post_export' #Utiliza sonata export
sessionFilter: 'TrabajoControllerFilter' #session para el filtro segun la entity
saveAndAdd: true #Si es true entonces agrega el boton para guardar y agregar otro
validator: false #Si es true agrega validacion por JS en los formularios (*Sin mantenimiento)
fieldsindex: #Los campos que aparecen en la lista de índices
    a.id:
        label: 'Código' #Nombre que muestra
        name: 'Id' #Nombre del campo
        type: 'integer' #Tipos: 'datetime', 'datetimetz', 'date', 'time', 'boolean', 'ONE_TO_MANY', 'MANY_TO_MANY', 'string'
        export: true #Permite exportar el campo
fieldsshow: #Los campos que aparecen en el ver Entity
    a.id:
        label: 'Código' #Nombre que muestra
        name: 'Id' #Nombre del campo
        type: 'integer' #Tipos: 'datetime', 'datetimetz', 'date', 'time', 'boolean', 'ONE_TO_MANY', 'MANY_TO_MANY', 'string'
        class: 'col-lg-8 col-md-6 col-sm-12' #Permite agregar class. Por defecto es col-12
        #closerow: true #Permite cerrar un row para dar inicio a otro row (http://getbootstrap.com/)
        separator: '<br>' #Opcionalmente se puede agregar separador html para ONE_TO_MANY || MANY_TO_MANY
```
#### Por defecto no escapa ```twig {{ value|raw }} ```

* [DOC](documentacion.md)
* [README](https://github.com/MWSimple/AdminCrudBundle/blob/version30/README.md)
