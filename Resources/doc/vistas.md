## Layout
### Bloques en layout.html.twig
#### A continuación se detallan los bloques posibles a reescribir que contiene el layout
```twig
    {% block title %}{% endblock %}
    {% block stylesheets %}{% endblock %}
    {% block scripts_head %}{% endblock %}
    {% block favicon %}{% endblock %}
    {% block menu %}{% endblock %}
    {% block page %}{% endblock %}
    {% block javascript %}{% endblock %}
```

## Lista
### Bloques en lista index.html.twig
#### título: Definir el titulo de la ventana
```twig
    {% block title %}{% endblock %}
```
#### página: Permite reescribir todo el contenido del index.html.twig
```twig
    {% block page %}{% endblock %}
```
#### botones arriba: Permite reescribir el bloque de botones de la barra superior (crear, exportar, filtro)
```twig
    {% block buttons %}{% endblock %}
```
#### acciones: Permite reescribir el bloque que contiene las acciones del listado (ver, edtar)
```twig
    {% block actions %}{% endblock %}
```
#### botones abajo: Permite reescribir el bloque de botones de abajo (crear)
```twig
    {% block buttonsbelow %}{% endblock %}
```

* [DOC](documentacion.md)
* [README](https://github.com/MWSimple/AdminCrudBundle/blob/version30/README.md)
