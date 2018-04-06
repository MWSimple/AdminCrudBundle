## Controller

### Variables. Example $this->em
```php
    // Configuration file.
    protected $config = [];
    protected $configArray = [];
    // Entity.
    protected $entity;
    // Entity Manager.
    protected $em;
    // Query Builder.
    protected $queryBuilder;
    // Form.
    protected $form;
    // Allows you to configure options in the form an array must be set
    protected $optionsForm = null;
```
### Functions
```php
    indexAction(Request $request)
    createQuery($repository)
    exportCsvAction($format, Request $request = null, $arraySourceIterator = false)
    filter($config, Request $request)
    createAction(Request $request)
    newAction()
    queryEntity($id)
    showAction($id)
    editAction($id)
    updateAction(Request $request, $id)
    deleteAction(Request $request, $id)
    getAutocompleteFormsMwsAction(Request $request, $options, $qb = null)

    protected function addNewConfig(){} //The method is used to add new config
    protected function createNewEntity(){$this->entity = new $this->configArray['entity']();} //The method is used to instantiate entity in newAction() and createAction(Request $request).
    protected function prePersistEntity(){} //The method is then used to validate form before flush entity in createAction(Request $request).
    protected function preHandleRequestEntity(){}
    protected function preFormIsValid(){} //It is executed before validating the form in updateAction(Request $request, $id).
    protected function preUpdateEntity(){}
    protected function preRemoveEntity(){}
    protected function validateForm(){return true;} //Return true default. Execute before form->isValid() entity createAction(Request $request) and updateAction(Request $request, $id).
```
#### Override generate the path to redirect after saving the entity in createAction and updateAction:
```php
    /* Execute after success flush entity in createAction and updateAction */
    protected function urlSuccess()
    {
        if ($this->configArray['saveAndAdd']) {
            $urlSuccess = $this->form->get('saveAndAdd')->isClicked()
            ? $this->generateUrl($this->configArray['new'])
            : $this->generateUrl($this->configArray['show'], array('id' => $this->entity->getId()));
        } else {
            $urlSuccess = $this->generateUrl($this->configArray['show'], array('id' => $this->entity->getId()));
        }

        return $urlSuccess;
    }
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
