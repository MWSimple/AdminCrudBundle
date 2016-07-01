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
