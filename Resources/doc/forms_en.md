## Forms

### New attr boostrap col
#### Example field form
```php
->add('text', 'text', array(
    'attr' => array(
        'col' => 'col-lg-6 col-md-6 col-sm-6',
    ),
))
```
#### If Select2entityType see below how to configure properly.

### Form type: mwspeso - Use Type Number add $
#### Example field form
```php
->add('numero', \MWSimple\Bundle\AdminCrudBundle\Form\Type\PesoType::class)
```

### Use Text editor.
#### Campo CKEditorType::class [Documentación](http://symfony.com/doc/master/bundles/IvoryCKEditorBundle/index.html). Example
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

### Use DualList.
#### FormType DualListType::class [Documentation](http://bootsnipp.com/snippets/featured/bootstrap-dual-list). Example
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

### Use Select2.
#### In the Entities are required to have the method toString()
```php
public function __toString()
{
    return (string)$this->getId();
}
```
#### If entity relationship generates select autocomplete. Personalize field
```php
public function getAutocompleteEntity(Request $request)
{
    $options = array(
        ...
        'field' => "id", #change by field id to use for the search
    );
    ...
}
```
#### Personalize query join field, the variable $qb rewrites the original
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
#### Type Form. Correct settings for the type Select2 using bootstrap, use 'class' and 'col'.
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

* [DOC](documentacion.md)
* [README](https://github.com/MWSimple/AdminCrudBundle/blob/version30/README.md)
