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

### New attr boostrap inline
#### If you need the checkbox or radio are located inline
```php
->add('checkbox', ChoiceType::class, array(
    'label_attr' => array(
        'class' => '-inline',
    ),
))
```

### Form type: mwspeso - Use Type Number add $
#### Example field form
```php
->add('numero', \MWSimple\Bundle\AdminCrudBundle\Form\Type\PesoType::class)
```

### Use Text editor.

#### Remember to install assets
```cli
php bin/console assets:install
```
#### Field CKEditorType::class [Documentation](http://symfony.com/doc/master/bundles/IvoryCKEditorBundle/index.html). Example
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

##Is included in the forms: jQuery plugin to validate form fields with Bootstrap 3+

[GitHub](https://github.com/nghuuphuoc/bootstrapvalidator)

##If Embed a Collection of Forms

[Documentation](http://symfony.com/doc/current/cookbook/form/form_collections.html)

###Using methods: addForm() and removeForm(), included in:
###Create Buttons: addLink() and deleteLink(), for uses with addForm() included in: collection.js

```twig
<script src="{{ asset('bundles/mwsimpleadmincrud/js/addForm.js') }}"></script>
<script src="{{ asset('bundles/mwsimpleadmincrud/js/collection.js') }}"></script>
```

* [Security](seguridad_en.md)
* [README](README_EN.md)
