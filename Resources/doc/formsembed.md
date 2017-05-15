##Embeber Formularios con EmbedType (Opcion 1) es posible agregar varios campos segun su clase.

###En el form agregar EmbedType:

```php
...
use MWSimple\Bundle\AdminCrudBundle\Form\Type\EmbedType;
...
$builder
    ->add('tags', EmbedType::class, [
        'entry_type' => TagType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'by_reference' => false,
        'attr' => [
            'class' => 'tags_embed',
            'col' => 'col-md-12',
            'embed' => 'row',
            'embed_row_col' => 'col-md-12',
            'embed_row_style' => 'border-bottom: thin solid; margin: 10px 0px;',
        ],
    ])
    //para mas de un nivel
    /*->add('tags_segundo', EmbedType::class, [
        'entry_type' => TagType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'by_reference' => false,
        'prototype_name' => '__nameembed__',
        'attr' => [
            'class' => 'tags_segundo_embed__name__',
            'col' => 'col-md-12',
            'embed' => 'row',
            'embed_row_col' => 'col-md-12',
            'embed_row_style' => 'border-bottom: thin solid; margin: 10px 0px;',
        ],
    ])*/
    //->add('tags2', EmbedType::class, [
    //    'entry_type' => Tag2Type::class,
    //    ...
    //])
;
```

###En las vistas New y Edit agregar:

```twig
{% block javascript %}
    {{ parent() }}
    <script src="{{ asset('bundles/mwsimpleadmincrud/js/addForm_not_validator.js') }}"></script>
    <script src="{{ asset('bundles/mwsimpleadmincrud/js/embed.js') }}"></script>
    <script type="text/javascript">
        $('.tags_embed').embed();
        {#$('.tags2_embed').embed();#}

        {# para mas de un nivel#}
        {# $('.tags_embed').embed();

        $('body').on('click', '.add_link_tags_embed', function(e) {
          var index = $(this).closest('.tags_embed').attr('data-index');
          $('.tags_segundo_embed'+index).embed();
        });#}

    </script>
{% endblock %}
```

###Si necesita boton para borrar el elemento agregar en el formulario usado en 'entry_type' en este caso TagType:

```php
...
use MWSimple\Bundle\AdminCrudBundle\Form\Type\ButtonDeleteType;
...
$builder
    ->add('ButtonDelete', ButtonDeleteType::class, [
        'mapped' => false,
        'attr' => [
            'col' => 'col-md-1',
        ],
    ])
;
```

##Embeber Formularios con Collection (Opcion 2) (deprecado)

[Documentacion](http://symfony.com/doc/current/cookbook/form/form_collections.html)

###Se pueden usar los metodos: addForm() y removeForm(), incluidos en: addForm.js
###Crea los botones: addLink() y deleteLink(), para usar con addForm.js incluidos en: collection.js

```twig
<script src="{{ asset('bundles/mwsimpleadmincrud/js/addForm.js') }}"></script>
<script src="{{ asset('bundles/mwsimpleadmincrud/js/collection.js') }}"></script>
```
####Si no utiliza el validador por js utilizar el siguiente addForm
```twig
<script src="{{ asset('bundles/mwsimpleadmincrud/js/addForm_not_validator.js') }}"></script>
<script src="{{ asset('bundles/mwsimpleadmincrud/js/collection.js') }}"></script>
```

##Embeber Formularios con Collection (Opcion 3) utilizando [ninsuo/symfony-collection](https://github.com/ninsuo/symfony-collection)

###Ejemplo
...

* [Seguridad](seguridad.md)
* [README](https://github.com/MWSimple/AdminCrudBundle/blob/version30/README.md)
