AdminCrudBundle
===============

## Installation

### Using composer

Add following lines to your `composer.json` file:

### Symfony 2.3.13 + Include Boostrap 3

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

### Configuration filter example

You can configure `config.yml` find Twig Configuration

```yaml
    twig:
        ...
        form:
            resources:
                - LexikFormFilterBundle:Form:form_div_layout.html.twig
```

### Configure translations (include en, es, ca)

You can configure `config.yml`

```yaml
    framework:
        ...
        translator:      { fallback: %locale% } # uncomment line
```

### Install assets

```cli
app/console assets:install
```

## Dependencies

This bundle add a paginator using [KnpPaginatorBundle](https://github.com/KnpLabs/KnpPaginatorBundle) and filter using [LexikFormFilterBundle](https://github.com/lexik/LexikFormFilterBundle) .

## Usage

...

## Author

Gonzalo Alonso - gonkpo@gmail.com