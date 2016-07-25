## Installation

### Using composer
Execute:
```cli
php composer.phar require mwsimple/admin-crud
```

Add it to the `AppKernel.php` class:

```php
// ...
new MWSimple\Bundle\AdminCrudBundle\MWSimpleAdminCrudBundle(),
new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
new Knp\Bundle\MenuBundle\KnpMenuBundle(),
new Lexik\Bundle\FormFilterBundle\LexikFormFilterBundle(),
new SC\DatetimepickerBundle\SCDatetimepickerBundle(),
new Liip\ImagineBundle\LiipImagineBundle(),
// new Vich\UploaderBundle\VichUploaderBundle(),
// new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
```

#### For instance VichUploaderBundle uncomment and View: [Uploads files](subirarchivos_en.md)
#### For instance IvoryCKEditorBundle uncomment and View: [Forms Text editor](forms_en.md)

### Configure imports config and translations (include en_US, es_AR, ca_ES, pt_BR, fr, pl)

You can configure `config.yml`

```yaml
imports:
    ...
    - { resource: "@MWSimpleAdminCrudBundle/Resources/config/config.yml" }
framework:
    ...
    translator:      { fallback: %locale% } # uncomment line
```

### Configuration filter default

En caso de necesitar mayor configuración: [LexikFormFilterBundle](https://github.com/lexik/LexikFormFilterBundle/blob/v5.0.1/Resources/doc/configuration.md)

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

#The Child are indices should not be the same, here are added and set the menu item.
#Permite Set the name, url, id, icon, roles who displayed, if you have submenu can be added.
mw_simple_admin_crud:
    menu_setting: { class: nav } # use nav
    menu:
        child: { name: inicio, url: mws_admin_crud_menu, id: inicio, icon: glyphicon glyphicon-home }
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
        #child3: { name: Users, url: admin_user, id: user, roles: ['ROLE_SUPER_ADMIN'], icon: glyphicon glyphicon-user }
```

### Configuration routing admin

You can configure `routing.yml` default query parameter names and templates

```yaml
mw_simple_admin_crud:
    resource: "@MWSimpleAdminCrudBundle/Controller/"
    type:     annotation
    prefix:   /admin

_liip_imagine:
    resource: "@LiipImagineBundle/Resources/config/routing.xml"
```

### Install assets
```cli
php bin/console assets:install
```

* [Generate CRUD](generacion_en.md)
* [README](README_EN.md)
