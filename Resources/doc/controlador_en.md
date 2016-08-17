## Controller

### Variables. Example $this->em
```php
    /* Configuration file. */
    protected $config = array();
    protected $configArray = array();
    /* Entity. */
    protected $entity;
    /* Entity Manager. */
    protected $em;
```
### Functions
```php
    indexAction(Request $request)
    createQuery($repository)
    exportCsvAction($format)
    filter($config, Request $request)
    createAction(Request $request)
    newAction()
    queryEntity($id)
    showAction($id)
    editAction($id)
    updateAction(Request $request, $id)
    deleteAction(Request $request, $id)
    getAutocompleteFormsMwsAction(Request $request, $options, $qb = null)

    createNewEntity() //The method is used to instantiate entity in newAction() and createAction(Request $request).
    persistEntity() //The method is then used to validate form before flush entity in createAction(Request $request).
```
#### To override the query the list use:
```php
    /**
     * Create query.
     * @param string $repository
     * @return Doctrine\ORM\QueryBuilder $queryBuilder
     */
    protected function createQuery($repository)
    {
        $this->em = $this->getDoctrine()->getManager();
        $queryBuilder = $this->em->getRepository($repository)
            ->createQueryBuilder('a')
            ->orderBy('a.id', 'DESC')
        ;

        return $queryBuilder;
    }
```
#### To override the instance of the entity:
```php
    /**
     * @return object
     */
    protected function createNewEntity()
    {
        $entity = new $this->configArray['entity']();
        $entity->setEnabled(true);

        return $entity;
    }
```

* [Views](vistas_en.md)
* [README](README_EN.md)
