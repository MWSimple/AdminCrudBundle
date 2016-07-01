## Usage

### Create entity

```cli
php bin/console generate:doctrine:entity
```

### Generate ADMIN CRUD Controller

```cli
php bin/console mwsimple:generate:admincrud
```
#### Si se necesita re generar el crud se puede pasar la opción --overwrite (*) Cuidado, pisa el código generado anteriormente.
```cli
php bin/console mwsimple:generate:admincrud --overwrite
```
## Config Crud

### archivo de configuración generado en: Bundle/Resources/config/Post.yml
```yaml
entityName: 'Post' #Nombre de la Entity
entity: 'Sistema\CPCEBundle\Entity\Post' #Ubicacion
repository: 'SistemaCPCEBundle:Post' #Repositorio
index: 'admin_post' #Rutas
new: 'admin_post_new'
edit: 'admin_post_edit'
show: 'admin_post_show'
create: 'admin_post_create'
update: 'admin_post_update'
delete: 'admin_post_delete'
export: 'admin_post_export' #Utiliza sonata export
sessionFilter: 'TrabajoControllerFilter' #session para el filtro segun la entity
saveAndAdd: true #Si es true entonces agrega el boton para guardar y agregar otro
validator: false #Si es true agrega validacion por JS en los formularios
fieldsindex: #Campos listados en el inicio
    a.id:
        label: 'Id' #Nombre que muestra
        name: 'Id' #Nombre del campo
        type: 'integer' #Tipos: 'datetime', 'datetimetz', 'date', 'time', 'boolean', 'ONE_TO_MANY', 'MANY_TO_MANY', 'string'
        export: true #Permite exportar el campo
fieldsshow:
    a.id:
        label: 'Codigo' #Nombre que muestra
        name: 'Id' #Nombre del campo
        type: 'integer' #Tipos: 'datetime', 'datetimetz', 'date', 'time', 'boolean', 'ONE_TO_MANY', 'MANY_TO_MANY', 'string'
        class: 'col-lg-8 col-md-6 col-sm-12' #Permite agregar class. Por defecto es col-12
        #closerow: true #Permite cerrar un row para dar inicio a otro row (http://getbootstrap.com/)
        separator: '<br>' #Opcionalmente se puede agregar separador html para ONE_TO_MANY || MANY_TO_MANY
```
#### Por defecto no escapa ```twig {{ value|raw }} ```
## List

### New block override Index list
#### actions
```twig
    {% block actions %}{% endblock %}
```
#### buttons
```twig
    {% block buttons %}{% endblock %}
```
#### buttons below
```twig
    {% block buttonsbelow %}{% endblock %}
```

### New Export list to csv.

## Forms

### New attr boostrap col
#### Example field form
```php
->add('text', 'text', array(
    'attr' => array(
        'col' => 'col-lg-6 col-md-6 col-sm-6',
    ),
))
```

### Form type: mwspeso - Use Type Number add $
#### Example field form
```php
->add('numero', \MWSimple\Bundle\AdminCrudBundle\Form\Type\PesoType::class)
```
### Use DualList.
#### FormType duallist [Documentation](http://bootsnipp.com/snippets/featured/bootstrap-dual-list)
```php
    $builder
        ->add('field', \MWSimple\Bundle\AdminCrudBundle\Form\Type\DualListType::class, array(
            'class'    => 'AppDemoBundle:Entity',
            'property' => 'name',
            'multiple' => true,
            'required' => false,
            'expanded' => true,
        ))
    ;
```
### Use Select2: If entity relationship generates select autocomplete.
#### En las Entities se requiere tener el mótodo toString()
```php
public function __toString()
{
    return (string)$this->getId();
}
```
#### Personalize field
```php
public function getAutocompleteEntity()
{
    $options = array(
        ...
        'field'      => "id", #change by field id to use for the search
    );
    ...
}
```
#### Personalize query join field
```php
public function getAutocompleteEntity()
{
    $options = array(
        'repository' => "AppBundle:Example",
        'field'      => "id",
    );
    //querybuilder $qb
    $em = $this->getDoctrine()->getManager();

    $qb = $em->getRepository($options['repository'])->createQueryBuilder('a');
    $qb
        ->where("a.".$options['field']." LIKE :term")
        ->orderBy("a.".$options['field'], "ASC")
    ;
    //set querybuilder $qb
    $response = parent::getAutocompleteFormsMwsAction($options, $qb);

    return $response;
}
```
#### Type Form. Configuración correcta para el type select2 utilizando boostrap, usar 'class' y 'col'.
```php
    $builder
        ->add('field', \MWSimple\Bundle\AdminCrudBundle\Form\Type\Select2entityType::class, array(
            'attr' => array(
                'class' => "col-lg-12 col-md-12",
                'col'   => "col-lg-8 col-md-8",
            )
        )
    ;
```

##ACL

[Documentation](http://symfony.com/doc/2.3/cookbook/security/acl.html)
[implement user recommend FOSUserBundle](https://github.com/FriendsOfSymfony/FOSUserBundle)
### Configuration

You can configure `config.yml` default query parameter names and templates

```yaml
services:
#ACL manager
    mws_acl_manager:
        class: MWSimple\Bundle\AdminCrudBundle\Services\ACLManager
        arguments:
            - "@service_container"
#Listener delete ACL PostRemove
    mws_acl_listener:
        class: MWSimple\Bundle\AdminCrudBundle\EventListener\ACLListener
        arguments:
            - "@service_container"
        tags:
            - { name: doctrine.event_listener, event: preRemove }

mw_simple_admin_crud:
#...
    acl:
        use: true #default false
        exclude_role: ROLE_SUPER_ADMIN #exclude role the control, default false
#entities use
        entities:
            - Acme\DemoBundle\Entity\Post
            - Acme\DemoBundle\Entity\Post2
```

##Is included in the forms: jQuery plugin to validate form fields with Bootstrap 3+

[GitHub](https://github.com/nghuuphuoc/bootstrapvalidator)

##If Embed a Collection of Forms

[Documentation](http://symfony.com/doc/current/cookbook/form/form_collections.html)

###Using methods: addForm() and removeForm(), included in:

```twig
<script src="{{ asset('bundles/mwsimpleadmincrud/js/addForm.js') }}"></script>
```

*if not using the validation does not work

[README](https://github.com/MWSimple/AdminCrudBundle/blob/version30/README.md)
