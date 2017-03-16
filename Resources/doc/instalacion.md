## Instalación

### Composer
Ejecutar el comando por consola
```cli
php composer.phar require mwsimple/admin-crud
```

Agregar a la clase `app/AppKernel.php`:

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

#### Para instanciar VichUploaderBundle descomentar y Ver: [Subiendo archivos](subirarchivos.md)
#### Para instanciar IvoryCKEditorBundle descomentar y Ver: [Formularios Editor de texto](forms.md)

### Importar configuración y permitir traducciones (incluye en_US, es_AR, ca_ES, pt_BR, fr, pl)

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
    page_range: 10                     # default page range used in pagination control
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
#- site_name: Permite configurar el nombre.
#- site_view_layout: Permite configurar el layout a extender por el CRUD.
#- site_view_footer: Permite configurar el footer a incluir por el CRUD.
#- fos_user: Poner a true en caso de utilizar FosUserBundle.
#- menu_horizontal: Para menu horizontal poner en true. Ademas agregar en menu_setting.class: nav navbar-nav
#menu: Los child son índices no deben ser iguales, aquí se agregan y configuran los item del menu.
#menu: Permite configurar el nombre, url, id, icono, roles a quien visualiza, si tiene submenu se puede agregar.
mw_simple_admin_crud:
    setting: { site_name: Administración, fos_user: false } # Default: site_name: AdminCrud, site_view_layout: 'MWSimpleAdminCrudBundle::layout.html.twig', site_view_footer: '::footer.html.twig', fos_user: false, menu_horizontal: false
    menu_setting: { id: side-menu, class: nav } # use id: side-menu and class: nav. If is horizontal use class: nav navbar-nav
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
        #child3: { name: Usuarios, url: admin_usuario, id: usuario, roles: ['ROLE_SUPERADMIN'], icon: glyphicon glyphicon-user }

vich_uploader:
    db_driver: orm
```

### Configurar la ruta admin

En el archivo `app/config/routing.yml`
incluye http://symfony.com/doc/current/routing/redirect_trailing_slash.html

```yaml
mw_simple_admin_crud:
    resource: "@MWSimpleAdminCrudBundle/Controller/"
    type:     annotation
    prefix:   /admin

_liip_imagine:
    resource: "@LiipImagineBundle/Resources/config/routing.xml"

# DEBE IR AL FINAL
remove_trailing_slash:
    path: /{url}
    defaults: { _controller: MWSimpleAdminCrudBundle:Redirecting:removeTrailingSlash }
    requirements:
        url: .*\/$
        _method: GET
```

### Instalar assets
```cli
php bin/console assets:install
```

* [Generar CRUD](generacion.md)
* [README](https://github.com/MWSimple/AdminCrudBundle/blob/version30/README.md)
