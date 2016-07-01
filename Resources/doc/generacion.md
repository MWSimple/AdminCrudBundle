## Uso

### Crear entity (si no existe)

```cli
php bin/console generate:doctrine:entity
```

### Generar CRUD Controller

```cli
php bin/console mwsimple:generate:admincrud
```
#### Si necesita regenerar el crud pasar la opción --overwrite (*) Cuidado, reescribe el código generado anteriormente.
```cli
php bin/console mwsimple:generate:admincrud --overwrite
```

* [Configuración](configuracion.md)
* [README](https://github.com/MWSimple/AdminCrudBundle/blob/version30/README.md)
