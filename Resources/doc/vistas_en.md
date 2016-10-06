## Layout
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

## List
### Block override Index list
```twig
    {% block title %}{% endblock %}

    {% block page %}{% endblock %}

    {% block buttons %}{% endblock %}

    {% block table_head %}{% endblock %}

    {% block table_body %}{% endblock %}

    {% block actions %}{% endblock %}

    {% block buttonsbelow %}{% endblock %}
```
### Preview images in list index.html.twig use imagine_filter
```twig
    <img src="{{ vich_uploader_asset(entity, field.file)|imagine_filter('my_thumb_list') }}">
```

## Show
### Blocks according show.html.twig list field types
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

* [Forms](forms_en.md)
* [README](README_EN.md)
