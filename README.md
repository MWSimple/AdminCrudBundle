AdminCrudBundle
===============

Description
-----

The mwsimple:generate:admincrud generates a very basic controller for a given entity located in a given bundle. This controller extend the default controller implements [paginator], [filter] and allows to perform the [five basic operations] on a model, allows rewriting actions and views.

    Listing all records,
    Showing one given record identified by its primary key,
    Creating a new record,
    Editing an existing record,
    Deleting an existing record.

* Use only annotation in controller.

## Previews

![List](https://raw.githubusercontent.com/MWSimple/AdminCrudBundle/version27/Resources/doc/preview_list.png "List")
![New](https://raw.githubusercontent.com/MWSimple/AdminCrudBundle/version27/Resources/doc/preview_new.png "New")

## Installation

### Using composer

Add following lines to your `composer.json` file:

### Support Symfony 2.7.* + Include Boostrap 3

```json
"require": {
    ...
    "mwsimple/admin-crud": "3.0.*@dev",
}
```

Execute:

```cli
php composer.phar update "mwsimple/admin-crud"
```

Add it to the `AppKernel.php` class:

```php
// ...
new MWSimple\Bundle\AdminCrudBundle\MWSimpleAdminCrudBundle(),
new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
new Knp\Bundle\MenuBundle\KnpMenuBundle(),
new Lexik\Bundle\FormFilterBundle\LexikFormFilterBundle(),
```

### Configure translations (include en, es, ca)

You can configure `config.yml`

```yaml
imports:
    ...
    - { resource: "@MWSimpleAdminCrudBundle/Resources/config/services.yml" }
framework:
    ...
    translator:      { fallback: %locale% } # uncomment line
```

### Configuration filter example

You can configure `config.yml` find Twig Configuration

```yaml
twig:
    ...
    form:
        resources:
            - LexikFormFilterBundle:Form:form_div_layout.html.twig
```

### Configuration paginator example

You can configure `config.yml` default query parameter names and templates

```yaml
knp_paginator:
    page_range: 10                      # default page range used in pagination control
    default_options:
        page_name: page                # page query parameter name
        sort_field_name: sort          # sort field query parameter name
        sort_direction_name: direction # sort direction query parameter name
        distinct: true                 # ensure distinct results, useful when ORM queries are using GROUP BY statements
    template:
        # pagination: KnpPaginatorBundle:Pagination:twitter_bootstrap_v3_pagination.html.twig # bootstrap 3 sliding pagination controls template
        pagination: MWSimpleAdminCrudBundle:Pagination:twitter_bootstrap_v3_pagination.html.twig # bootstrap 3 sliding pagination controls template
        sortable: KnpPaginatorBundle:Pagination:sortable_link.html.twig # sort link template
```

### Configuration menu example

You can configure `config.yml` default query parameter names and templates option add icon and roles

```yaml
knp_menu:
    twig:  # use "twig: false" to disable the Twig extension and the TwigRenderer
        template: MWSimpleAdminCrudBundle:Menu:knp_menu.html.twig
    templating: false # if true, enables the helper for PHP templates
    default_renderer: twig # The renderer to use, list is also available by default

mw_simple_admin_crud:
    menu_setting: { class: nav } # use nav
    menu:
        child: { name: inicio, url: mws_admin_crud_menu, id: inicio }
        #child: { name: inicio, url: mws_admin_crud_menu, id: inicio, icon: glyphicon glyphicon-home }
        #child2:  
        #    name: help
        #    url: null
        #    id: help
        #    subMenu:
        #        indice:
        #            name: indice
        #            url: admin_indice
        #            icon: glyphicon glyphicon-home
        #            roles: ['ROLE_ADMIN']
        #    roles: ['ROLE_USER']
        #child3: 
        #    name: Usuario
        #    url: admin_user
        #    id: usuario
        #    roles: ['ROLE_SUPERADMIN']
```

### Configuration routing admin

You can configure `routing.yml` default query parameter names and templates

```yaml
mw_simple_admin_crud:
    resource: "@MWSimpleAdminCrudBundle/Controller/"
    type:     annotation
    prefix:   /admin
```

### Install assets

```cli
app/console assets:install
```

## Dependencies

This bundle extends [SensioGeneratorBundle](https://github.com/sensio/SensioGeneratorBundle) and add a paginator using [KnpPaginatorBundle](https://github.com/KnpLabs/KnpPaginatorBundle) and filter using [LexikFormFilterBundle](https://github.com/lexik/LexikFormFilterBundle) and menu using [KnpMenuBundle](https://github.com/KnpLabs/KnpMenuBundle) .

## Usage

### Create entity

```cli
php app/console generate:doctrine:entity
```

### Generate ADMIN CRUD Controller

```cli
php app/console mwsimple:generate:admincrud
```
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

### File upload
#### Entity
```php
...
use MWSimple\Bundle\AdminCrudBundle\Entity\BaseFile;
...
class Demo extends BaseFile {
    ...
    public function getUploadDir()
    {
        $this->uploadDir = 'uploads/files';
        return $this->uploadDir;
    }
}
```
#### Form
```php
->add('file', 'mws_field_file', array(
    'required'  => false,
    'file_path' => 'webPath',
    'label'     => 'Image',
    //'show_path' => true
))
```
### Use DualList.
#### FormType duallist [Documentation](http://bootsnipp.com/snippets/featured/bootstrap-dual-list)
```php
    $builder
        ->add('field', 'duallist', array(
            'class'    => 'AppDemoBundle:Entity',
            'property' => 'name',
            'multiple' => true,
            'required' => false,
            'expanded' => true,
        ))
    ;
```
### Use Select2: If entity relationship generates select autocomplete.
#### Entities required method toString()
```php
public function __toString()
{
    return (string)$this->getId();
}
```

#### Personalize
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


## Author

Gonzalo Alonso - gonkpo@gmail.com
