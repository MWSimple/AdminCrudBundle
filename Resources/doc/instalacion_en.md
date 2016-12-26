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
new Tetranz\Select2EntityBundle\TetranzSelect2EntityBundle(),
new Vich\UploaderBundle\VichUploaderBundle(),
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

#setting:
- site_name: Can set the name.
- site_view_layout: Allows you to configure the layout to be extended by the CRUD.
- fos_user: Putting true when using FosUserBundle.
#menu: The Child are indices should not be the same, here are added and set the menu item.
#menu: Set the name, url, id, icon, roles who displayed, if you have submenu can be added.
mw_simple_admin_crud:
    setting: { site_name: Administration, fos_user: false } # Default: site_name: AdminCrud, site_view_layout: MWSimpleAdminCrudBundle::layout.html.twig, fos_user: false
    menu_setting: { id: side-menu, class: nav } # use id side-menu and class nav
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

vich_uploader:
    db_driver: orm
```

### Configuration routing admin

In the file `app/config/routing.yml`
include http://symfony.com/doc/current/routing/redirect_trailing_slash.html

```yaml
mw_simple_admin_crud:
    resource: "@MWSimpleAdminCrudBundle/Controller/"
    type:     annotation
    prefix:   /admin

_liip_imagine:
    resource: "@LiipImagineBundle/Resources/config/routing.xml"

# MUST GO TO THE END
remove_trailing_slash:
    path: /{url}
    defaults: { _controller: MWSimpleAdminCrudBundle:Redirecting:removeTrailingSlash }
    requirements:
        url: .*\/$
        _method: GET
```

### Install assets
```cli
php bin/console assets:install
```

* [Generate CRUD](generacion_en.md)
* [README](README_EN.md)
