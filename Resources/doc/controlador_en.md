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
    prePersistEntity() //The method is then used to validate form before flush entity in createAction(Request $request).
    preHandleRequestEntity()
    preUpdateEntity()
    preRemoveEntity()
```
#### Override the query the list use:
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
#### Override the instance of the entity in newAction and createAction:
```php
    /**
     * @return object
     */
    protected function createNewEntity()
    {
        $this->entity = new $this->configArray['entity']();
        $this->entity->setEnabled(true);
    }
```
#### Execute before persist the entity in createAction:
```php
    protected function prePersistEntity()
    {
        //$this->entity->setEnabled(true);
    }
```
#### Execute before handleRequest the entity in updateAction:
```php
    protected function preHandleRequestEntity()
    {
        //$passwordOld = $this->entity->getPassword();
    }
```
#### Execute before flush the entity in updateAction:
```php
    protected function preUpdateEntity()
    {
        //$this->entity->setEnabled(true);
    }
```
#### Execute before remove the entity in deleteAction:
```php
    protected function preRemoveEntity()
    {
        //$this->entity->getChild()->setParent(null);
    }
```

* [Views](vistas_en.md)
* [README](README_EN.md)
