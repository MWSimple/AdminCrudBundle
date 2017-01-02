## Diseño
### Bloques en layout.html.twig
#### A continuación se detallan los bloques posibles a reescribir que contiene el layout
```twig
    {% block title %}{% endblock %}
    {% block stylesheets %}{% endblock %}
    {% block head_javascript %}{% endblock %}
    {% block favicon %}{% endblock %}
    {% block menu %}{% endblock %}
    {% block page %}{% endblock %}
    {% block javascript %}{% endblock %}
```
#### Variables, de ser necesario cargar la configuracion en la vista con la funcion admincrud_config() desde otro bundle se puede setear su: Configuration - treeBuilder - root como se describe a continuacion:
```twig
{% set parameterRoot = "demo_bundle" %}
```

## Lista
### Bloques en lista index.html.twig
```twig
    {# título: Definir el titulo de la ventana #}
    {% block title %}{% endblock %}

    {# página: Permite reescribir todo el contenido del index.html.twig #}
    {% block page %}{% endblock %}

    {# botones arriba: Permite reescribir el bloque de botones de la barra superior (crear, exportar, filtro) #}
    {% block buttons %}{% endblock %}

    {# tabla head: Permite reescribir el bloque head de tabla #}
    {% block table_head %}{% endblock %}

    {# tabla body: Permite reescribir el bloque body de tabla #}
    {% block table_body %}{% endblock %}

    {# acciones: Permite reescribir el bloque que contiene las acciones del listado (ver, editar) #}
    {% block actions %}{% endblock %}

    {# botones abajo: Permite reescribir el bloque de botones de abajo (crear) #}
    {% block buttonsbelow %}{% endblock %}
```
### Vista previa de imagenes en lista index.html.twig uso imagine_filter
```twig
    <img src="{{ vich_uploader_asset(entity, field.file)|imagine_filter('my_thumb_list') }}">
```

## Ver
### Bloques en lista show.html.twig según tipos de campos
```twig
    {% block datetime %}{% endblock %}
    {% block boolean %}{% endblock %}
    {% block ONE_TO_MANY_MANY_TO_MANY %}{% endblock %}
    {% block text %}{% endblock %}
    {% block money %}{% endblock %}
    {% block vich_file %}{% endblock %}
    {% block vich_file_many %}{% endblock %}
    {% block default %}{% endblock %}
```
###
```twig
    {% block page %}{% endblock %}
    {% block panelheading %}{% endblock %}
```

* [Formularios](forms.md)
* [README](https://github.com/MWSimple/AdminCrudBundle/blob/version30/README.md)
