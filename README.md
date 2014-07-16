AdminCrudBundle
===============

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
    page_range: 5                      # default page range used in pagination control
    default_options:
        page_name: page                # page query parameter name
        sort_field_name: sort          # sort field query parameter name
        sort_direction_name: direction # sort direction query parameter name
        distinct: true                 # ensure distinct results, useful when ORM queries are using GROUP BY statements
    template:
        pagination: KnpPaginatorBundle:Pagination:twitter_bootstrap_v3_pagination.html.twig # bootstrap 3 sliding pagination controls template
        sortable: KnpPaginatorBundle:Pagination:sortable_link.html.twig # sort link template
```

### Install assets

```cli
app/console assets:install
```

## Dependencies

This bundle add a paginator using [KnpPaginatorBundle](https://github.com/KnpLabs/KnpPaginatorBundle) and filter using [LexikFormFilterBundle](https://github.com/lexik/LexikFormFilterBundle) .

## Usage

### Create entity

```cli
app/console generate:doctrine:entity
```
Example test entity:
see vendor/mwsimple/admin-crud/MWSimple/Bundle/AdminCrudBundle/Entity/Post.dist

### Configure entity

You can create `file.yml` in Resources/config
```yaml
fields.index: #Fields view index
    a.field: #a (query) . field (name field)
        name: 'Field' #name display field
fields.show: #Fields view show
    a.field:
        name: 'Field'
    a.datetime:
        name: 'Datetime'
        date: 'Y-m-d H:i:s'
```

Example yml:
see vendor/mwsimple/admin-crud/MWSimple/Bundle/AdminCrudBundle/Resources/config/post.dist

### Create Controller:
see vendor/mwsimple/admin-crud/MWSimple/Bundle/AdminCrudBundle/Controller/PostController.dist

### Create Form:
see vendor/mwsimple/admin-crud/MWSimple/Bundle/AdminCrudBundle/Form/PostType.dist
see vendor/mwsimple/admin-crud/MWSimple/Bundle/AdminCrudBundle/Form/PostFilterType.dist

...

## Author

Gonzalo Alonso - gonkpo@gmail.com
