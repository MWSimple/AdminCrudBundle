## Formularios

### Clase de boostrap col
#### Ejemplo para configurar la clase col de boostrap en el formulario
```php
->add('text', 'text', array(
    'attr' => array(
        'col' => 'col-lg-6 col-md-6 col-sm-6',
    ),
))
```
#### Si es tipo Select2entityType ver mas abajo como configurar correctamente.

### Clase de boostrap row
#### Ejemplo para configurar la clase row de boostrap en el formulario
```php
...
use MWSimple\Bundle\AdminCrudBundle\Form\Type\FormrowType;
...
->add('formrow', FormrowType::class, array(
    'mapped' => false
))
```

### Clase de boostrap inline
#### Si necesitamos que los checkbox o radio se ubiquen en linea
```php
->add('checkbox', ChoiceType::class, array(
    'label_attr' => array(
        'class' => 'inline',
    ),
))
```

### Campo tipo: mwspeso - Agrega el signo $ para un tipo numerico
#### Ejemplo de uso
```php
->add('numero', \MWSimple\Bundle\AdminCrudBundle\Form\Type\PesoType::class)
```

### Clase mws-datetime para tipo DateTime
#### Ejemplo de uso
```php
->add('dateTime', DateTimeType::class, [
    'date_widget' => 'single_text',
    'time_widget' => 'text',
    'attr' => [
        'class' => 'mws-datetime',
    ],
])
```

### Utilizar Editor de texto.

#### Recordar instalar assets
```cli
php bin/console ckeditor:install
php bin/console assets:install
```
### You can configure `config.yml`
```yaml
fos_ck_editor:
    autoload: false
    async: true
```
#### Campo CKEditorType::class [Documentación](https://symfony.com/doc/master/bundles/FOSCKEditorBundle/index.html). Ejemplo
```php
use FOS\CKEditorBundle\Form\Type\CKEditorType;

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
        'choice_label' => 'name',
        'multiple' => true,
        'required' => false,
        'expanded' => true,
    ))
;
```

### Usar Select2. [Documentación](http://select2.github.io/)
#### Implementar Select2 en un select normal agregando class mws_select2:
```php
    $builder
        ->add('field', null, [
            'attr' => array(
                'class' => "mws_select2 col-lg-12 col-md-12",
                'col'   => "col-lg-8 col-md-8",
            )
        ])
    ;
```
### Usar Select2 Entity. [Documentación](https://github.com/tetranz/select2entity-bundle)
#### En las Entities se requiere tener el mótodo toString() o se puede definir un metodo en el controller
```php
public function __toString()
{
    return (string)$this->getId();
}
```
#### Opcional Personalizar el metodo en el controller:
```php
public function getAutocompleteEntity(Request $request)
{
    ...
    $response = parent::getAutocompleteFormsMwsAction($request, $options, null, "getId");
    ...
}
```
#### Opcional Personalizar LIKE: start, end, equal, contains:
```php
public function getAutocompleteEntity(Request $request)
{
    ...
    $response = parent::getAutocompleteFormsMwsAction($request, $options, null, null, "equal");
    ...
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
    $response = parent::getAutocompleteFormsMwsAction($request, $options, $qb);

    return $response;
}
```
#### Type Form. Configuración opcional desde la documentación tetranz/select2entity-bundle:
* [Configuración opcional](https://github.com/tetranz/select2entity-bundle#how-to-use)
#### Type Form. Configuración correcta para el type select2 utilizando boostrap, usar 'class' y 'col'.
```php
    $builder
        ->add('field', \Tetranz\Select2EntityBundle\Form\Type\Select2EntityType::class, array(
            //...
            'attr' => array(
                'class' => "col-lg-12 col-md-12",
                'col'   => "col-lg-8 col-md-8",
            )
        )
    ;
```

[GitHub](https://github.com/nghuuphuoc/bootstrapvalidator)

* [Formularios Embebidos](formsembed.md)
* [README](https://github.com/MWSimple/AdminCrudBundle/blob/version30/README.md)
