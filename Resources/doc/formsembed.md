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

###En las vistas New y Edit agregar:

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
