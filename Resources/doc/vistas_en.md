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
#### title
```twig
    {% block title %}{% endblock %}
```
#### page
```twig
    {% block page %}{% endblock %}
```
#### buttons
```twig
    {% block buttons %}{% endblock %}
```
#### table head
```twig
    {% block table_head %}{% endblock %}
```
#### table body
```twig
    {% block table_body %}{% endblock %}
```
#### actions
```twig
    {% block actions %}{% endblock %}
```
#### buttons below
```twig
    {% block buttonsbelow %}{% endblock %}
```
### Preview images in list index.html.twig use imagine_filter
```twig
    <img src="{{ vich_uploader_asset(entity, field.file)|imagine_filter('my_thumb_list') }}">
```

* [Forms](forms_en.md)
* [README](https://github.com/MWSimple/AdminCrudBundle/blob/version30/README.md)
