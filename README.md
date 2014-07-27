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

## Installation

### Using composer

Add following lines to your `composer.json` file:

### Symfony 2.3.* + Include Boostrap 3

```json
"require": {
    ...
    "mwsimple/admin-crud": "2.3.*@dev"
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
        pagination: KnpPaginatorBundle:Pagination:twitter_bootstrap_v3_pagination.html.twig # bootstrap 3 sliding pagination controls template
        sortable: KnpPaginatorBundle:Pagination:sortable_link.html.twig # sort link template
```

### Configuration menu example

You can configure `config.yml` default query parameter names and templates

```yaml
mw_simple_admin_crud:
    menu:
        child: { name: inicio, url: mws_admin_crud_menu }
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

## Author

Gonzalo Alonso - gonkpo@gmail.com
