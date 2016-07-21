## Usage

### Create entity (if not exist)

```cli
php bin/console generate:doctrine:entity
```

### Generate CRUD Controller

```cli
php bin/console mwsimple:generate:admincrud
```
#### If necessary rebuild pass option --overwrite (*) Care, rewrites previously generated code.
```cli
php bin/console mwsimple:generate:admincrud --overwrite
```
#### Generate item for the menu.
```cli
php bin/console mwsimple:menu:additem
```

* [Configuration](configuracion_en.md)
* [README](README_EN.md)
