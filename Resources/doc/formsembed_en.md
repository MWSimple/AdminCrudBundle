## Embedding Forms with EmbedType (Option 1) it is possible to add several fields according to your class.

### In the form add EmbedType:

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
    /*->add('tags_second', EmbedType::class, [
        'entry_type' => TagType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'by_reference' => false,
        'prototype_name' => '__nameembed__',
        'attr' => [
            'class' => 'tags_second_embed__name__',
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

### In the New and Edit views add:

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
        $('.tags_second_embed'+index).embed();
      });#}

    </script>
{% endblock %}
```

### If you need to delete the add item in the form used in 'entry_type' in this case TagType:

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

##Embed a Collection of Forms (Option 2) (deprecated)

[Documentation](http://symfony.com/doc/current/cookbook/form/form_collections.html)

###Using methods: addForm() and removeForm(), included in:
###Create Buttons: addLink() and deleteLink(), for uses with addForm() included in: collection.js

```twig
<script src="{{ asset('bundles/mwsimpleadmincrud/js/addForm.js') }}"></script>
<script src="{{ asset('bundles/mwsimpleadmincrud/js/collection.js') }}"></script>
```
####If not using validator js
```twig
<script src="{{ asset('bundles/mwsimpleadmincrud/js/addForm_not_validator.js') }}"></script>
<script src="{{ asset('bundles/mwsimpleadmincrud/js/collection.js') }}"></script>
```

##Embed a Collection of Forms (Option 3) use [ninsuo/symfony-collection](https://github.com/ninsuo/symfony-collection)

###Example
...

* [Security](seguridad_en.md)
* [README](README_EN.md)
