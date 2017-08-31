## Configuración

### Archivo de configuración generado en: Bundle/Resources/config/Post.yml (Por ejemplo si la Entity fuera Post)
#### Para no incluir una acción en el CRUD comentar el índice.
```yaml
entityName: 'Post' #Nombre de la Entity
entity: 'Acme\DemoBundle\Entity\Post' #Ubicacion
repository: 'AcmeDemoBundle:Post' #Repositorio
paginate: true #Por defecto siempre es true. Para desactivar la paginacion agregar esta linea con valor false.
#Rutas para enrutamiento
index: 'admin_post' #Si comento esta linea no se muestra en las acciones
view_index: 'AcmeDemoBundle:post:index.html.twig'
new: 'admin_post_new' #Si comento esta linea no se muestra en el index
create: 'admin_post_create'
view_new: 'AcmeDemoBundle:post:new.html.twig'
edit: 'admin_post_edit' #Si comento esta linea no se muestra en el index y show
update: 'admin_post_update'
view_edit: 'AcmeDemoBundle:post:edit.html.twig'
show: 'admin_post_show'
view_show: 'AcmeDemoBundle:post:show.html.twig'
delete: 'admin_post_delete' #Si comento esta linea no se muestra en el edit y show
export: 'admin_post_export' #Utiliza sonata export
sessionFilter: 'PostControllerFilter' #Si comento esta linea no utiliza session. Para quitar el filtro comentar linea de filterType en el Controller generado
saveAndAdd: true #Si es true entonces agrega el boton para guardar y agregar otro
validator: false #Si es true agrega validacion por JS en los formularios
site_view_layout: null #Opcional por ejemplo 'AppBundle::layout.html.twig'
parameter_root: null #Opcional de ser necesario cargar la configuracion en la vista con la funcion admincrud_config() desde otro bundle se puede setear su valor root: Configuration - treeBuilder - root
#ajax_enabled_disabled: 'path_ajax_enabled_disabled' #Opcional si necesita personalizar
fieldsindex: #Los campos que aparecen en la lista de índices
    a.id:
        label: 'Código' #Nombre que muestra
        name: 'Id' #Nombre del campo
        type: 'integer' #Tipos: 'string', 'datetime', 'datetimetz', 'date', 'time', 'boolean', 'boolean_view', 'ONE_TO_MANY', 'MANY_TO_MANY', 'vich_file', 'money'
        class: 'hidden-xs' #Columna deshabilitada en el móvil
        export: true #Permite exportar el campo
    a.imagen:
        ...
        type: 'vich_file' #Tipo para la vista previa de la imagen
        file: 'imageFile' #El campo @Vich\UploadableField de la entidad
        export: false #Setear en false
    a.moneda:
        ...
        type: 'money'
        currency_style: 'currency'
        currency_type: 'double'
        ...
fieldsshow_title: #Agregar si se necesita concatenar un label y un campo de la entity en el titulo
    label: 'ID'
    name: 'Id'
fieldsshow: #Los campos que aparecen en el ver Entity
    a.id:
        label: 'Código' #Nombre que muestra
        name: 'Id' #Nombre del campo
        type: 'integer' #Tipos: 'string', 'datetime', 'datetimetz', 'date', 'time', 'boolean', 'ONE_TO_MANY', 'MANY_TO_MANY', 'vich_file', 'vich_file_many', 'money'
        class: 'col-lg-8 col-md-6 col-sm-12' #Permite agregar class. Por defecto es col-12
        #closerow: true #Permite cerrar un row para dar inicio a otro row (http://getbootstrap.com/)
        separator: '<br>' #Opcionalmente se puede agregar separador html para ONE_TO_MANY || MANY_TO_MANY
    a.entities: #Opcion para visualizar ONE_TO_MANY o MANY_TO_MANY en formato tabla agregando la siguiente configuracion
        ...
        type: 'ONE_TO_MANY' #o 'MANY_TO_MANY'
        fields:
            Label1: field1 #Ej. TituloColumna: NombreCampo
            Label2: field2
            Label3: field3
        actions: #Si no desea acciones no agregar la linea
            show: path_show #Agrega accion ver (ruta al ver), si no necesita comentar linea.
            edit: path_edit #Agrega accion editar (ruta al editar), si no necesita comentar linea.
        ...
    a.imagen:
        ...
        type: 'vich_file' #Tipo para la vista previa de la imagen
        file: 'imageFile' #El campo @Vich\UploadableField de la entidad
        ...
    a.imagenes:
        ...
        type: 'vich_file_many' #Tipo para la vista previa de la colección de imagenes
        file: 'imageFile' #El campo @Vich\UploadableField de la entidad relacionada
        file_name: 'imageName' #El campo donde se guarda el nombre o path de la imagen
        # file_class: 'col-md-1' #Por defecto es 'col-md-1'
        # imagine_filter: 'my_thumb_list' #Opcionalmente se puede utilizar filtro por ejemplo 'my_thumb_list' del listado
        ...
    a.moneda:
        ...
        type: 'money'
        currency_style: 'currency'
        currency_type: 'double'
        ...
    a.boton:
        label: 'Accion'
        title: 'Accion a ejecutar'
        path: 'nombre_de_la_ruta'
        type: 'button'
        icon: 'glyphicon glyphicon-wrench'
        ...
```
#### Por defecto no escapa ```twig {{ value|raw }} ```

#### Documentacion para el campo money se utiliza localizedcurrency: [TwigExtensions](http://twig.sensiolabs.org/doc/extensions/intl.html) .

* [Controlador](controlador.md)
* [README](https://github.com/MWSimple/AdminCrudBundle/blob/version30/README.md)
