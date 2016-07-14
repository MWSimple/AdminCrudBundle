## Formularios

### Clase de boostrap col
#### Ejemplo para configurar la clase col de boostap en el formulario
```php
->add('text', 'text', array(
    'attr' => array(
        'col' => 'col-lg-6 col-md-6 col-sm-6',
    ),
))
```
#### Si es tipo Select2entityType ver mas abajo como configurar correctamente.

### Campo tipo: mwspeso - Agrega el signo $ para un tipo numerico
#### Ejemplo de uso
```php
->add('numero', \MWSimple\Bundle\AdminCrudBundle\Form\Type\PesoType::class)
```

### Utilizar Editor de texto.

#### Recordar instalar assets
```cli
php bin/console assets:install
```
#### Campo CKEditorType::class [Documentación](http://symfony.com/doc/master/bundles/IvoryCKEditorBundle/index.html). Ejemplo
```php
use Ivory\CKEditorBundle\Form\Type\CKEditorType;

$builder
    ->add('field', CKEditorType::class, array(
        'config' => array(
            'uiColor' => '#ffffff',
            //...
        ),
    ))
;
```

### Utilizar DualList.
#### Campo tipo DualListType::class [Documentación](http://bootsnipp.com/snippets/featured/bootstrap-dual-list). Ejemplo
```php
use MWSimple\Bundle\AdminCrudBundle\Form\Type\DualListType;

$builder
    ->add('field', DualListType::class, array(
        'class'    => 'AppDemoBundle:Entity',
        'property' => 'name',
        'multiple' => true,
        'required' => false,
        'expanded' => true,
    ))
;
```

### Usar Select2.
#### En las Entities se requiere tener el mótodo toString()
```php
public function __toString()
{
    return (string)$this->getId();
}
```
#### Si la entidad tiene relaciones en el Controlador se genera un método autocomplete. Se puede personalizar
```php
public function getAutocompleteEntity(Request $request)
{
    $options = array(
        ...
        'field' => "id", #cambio del campo a utilizar para la búsqueda
    );
    ...
}
```
#### Personalizar la query agregando join, la variable $qb reescribe la original
```php
public function getAutocompleteEntity(Request $request)
{
    $options = array(
        'repository' => "AppBundle:Example",
        'field'      => "id",
    );
    //querybuilder $qb
    $em = $this->getDoctrine()->getManager();

    $qb = $em->getRepository($options['repository'])->createQueryBuilder('a');
    $qb
        ->where("a.".$options['field']." LIKE :term")
        ->orderBy("a.".$options['field'], "ASC")
    ;
    //set querybuilder $qb
    $response = parent::getAutocompleteFormsMwsAction($options, $qb);

    return $response;
}
```
#### Type Form. Configuración correcta para el type select2 utilizando boostrap, usar 'class' y 'col'.
```php
    $builder
        ->add('field', \MWSimple\Bundle\AdminCrudBundle\Form\Type\Select2entityType::class, array(
            'attr' => array(
                'class' => "col-lg-12 col-md-12",
                'col'   => "col-lg-8 col-md-8",
            )
        )
    ;
```

##Si incluis en los formularios: El plugin de jQuery valida los campos del  formulario en Bootstrap 3+

[GitHub](https://github.com/nghuuphuoc/bootstrapvalidator)

##Embeber Formularios con Collection

[Documentacion](http://symfony.com/doc/current/cookbook/form/form_collections.html)

###Se pueden usar los metodos: addForm() y removeForm(), incluidos en: addForm.js
###Crea los botones: addLink() y deleteLink(), para usar con addForm.js incluidos en: collection.js

```twig
<script src="{{ asset('bundles/mwsimpleadmincrud/js/addForm.js') }}"></script>
<script src="{{ asset('bundles/mwsimpleadmincrud/js/collection.js') }}"></script>
```

* [SEGURIDAD](seguridad.md)
* [README](https://github.com/MWSimple/AdminCrudBundle/blob/version30/README.md)
