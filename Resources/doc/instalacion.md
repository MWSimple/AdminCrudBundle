## Instalación

### composer

Añadir la siguiente línea a su archivo `composer.json`:

```json
"require": {
    ...
    "mwsimple/admin-crud": "4.0.*@dev",
}
```

Ejecutar:

```cli
php composer.phar update "mwsimple/admin-crud"
```

Agregar a la clase `app/AppKernel.php`:

```php
// ...
new MWSimple\Bundle\AdminCrudBundle\MWSimpleAdminCrudBundle(),
new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
new Knp\Bundle\MenuBundle\KnpMenuBundle(),
new Lexik\Bundle\FormFilterBundle\LexikFormFilterBundle(),
new SC\DatetimepickerBundle\SCDatetimepickerBundle(),
new Vich\UploaderBundle\VichUploaderBundle(),
```

### Importar configuración y configurar traducciones (incluye en, es, ca, pt)

En el archivo `app/config/config.yml`

```yaml
imports:
    ...
    - { resource: "@MWSimpleAdminCrudBundle/Resources/config/config.yml" }
framework:
    ...
    translator:      { fallback: %locale% } # uncomment line
```

### La configuración de los filtros esta incluída por defecto.

En caso de necesitar mayor configuración: [LexikFormFilterBundle](https://github.com/lexik/LexikFormFilterBundle/blob/v5.0.1/Resources/doc/configuration.md)

### Configurar paginador y menu

En el archivo `app/config/config.yml`

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

#Los child son índices no deben ser iguales, aquí se agregan y configuran los item del menu. Permite configurar el nombre, url, id, icono, roles a quien visualiza, si tiene submenu se puede agregar.
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

### Configurar la ruta admin

En el archivo `app/config/routing.yml`

```yaml
mw_simple_admin_crud:
    resource: "@MWSimpleAdminCrudBundle/Controller/"
    type:     annotation
    prefix:   /admin
```

### Instalar assets
```cli
php bin/console assets:install
```

[Documentación](Resources/doc/documentacion.md)
[README](README.md)
