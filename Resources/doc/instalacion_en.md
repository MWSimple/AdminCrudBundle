## Instalación

### composer

Añadir la siguiente línea a su archivo `composer.json`:

### Support Symfony 3.x

```json
"require": {
    ...
    "mwsimple/admin-crud": "4.0.*@dev",
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
new SC\DatetimepickerBundle\SCDatetimepickerBundle(),
new Vich\UploaderBundle\VichUploaderBundle(),
```

### Configure imports config and translations (include en, es, ca, pt)

You can configure `config.yml`

```yaml
imports:
    ...
    - { resource: "@MWSimpleAdminCrudBundle/Resources/config/config.yml" }
framework:
    ...
    translator:      { fallback: %locale% } # uncomment line
```

### Configuration filter

Mayor configuracion: [LexikFormFilterBundle](https://github.com/lexik/LexikFormFilterBundle/blob/v5.0.1/Resources/doc/configuration.md)

### Configuration paginator and menu

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

knp_menu:
    twig:  # use "twig: false" to disable the Twig extension and the TwigRenderer
        template: MWSimpleAdminCrudBundle:Menu:knp_menu.html.twig
    templating: false # if true, enables the helper for PHP templates
    default_renderer: twig # The renderer to use, list is also available by default

#Los child son indices no deben ser iguales, aqui se agregan y configuran los item del menu
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
php bin/console assets:install
```

## Dependencies

This bundle extends [SensioGeneratorBundle](https://github.com/sensio/SensioGeneratorBundle) and add a paginator using [KnpPaginatorBundle](https://github.com/KnpLabs/KnpPaginatorBundle) and filter using [LexikFormFilterBundle](https://github.com/lexik/LexikFormFilterBundle) and menu using [KnpMenuBundle](https://github.com/KnpLabs/KnpMenuBundle) .