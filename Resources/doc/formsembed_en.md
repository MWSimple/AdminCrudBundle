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
            'col' => 'col-lg-12',
        ],
    ])
    //->add('tags2', EmbedType::class, [
    //    'entry_type' => Tag2Type::class,
    //    'allow_add' => true,
    //    'allow_delete' => true,
    //    'by_reference' => false,
    //    'attr' => [
    //        'class' => 'tags2_embed',
    //        'col' => 'col-lg-12',
    //    ],
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
