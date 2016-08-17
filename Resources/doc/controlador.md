## Controlador

### Variables. Ejemplo $this->em
```php
    /* Configuration file. */
    protected $config = array();
    protected $configArray = array();
    /* Entity. */
    protected $entity;
    /* Entity Manager. */
    protected $em;
```
### Funciones
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

    createNewEntity() //Se utiliza al instanciar la entidad en los metodos newAction() y createAction(Request $request).
    persistEntity() //Se utiliza luego de validar formulario y antes del flush entidad en el metodo createAction(Request $request).
```
#### Sobreescribir la query del listado utilizar:
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
#### Sobreescribir la instancia de la entidad:
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
#### Ejecutar antes de persistir la entidad en createAction:
```php
    protected function prePersistEntity()
    {
        //$this->entity->setEnabled(true);
    }
```
#### Ejecutar antes de handleRequest de la entidad en updateAction:
```php
    protected function preHandleRequestEntity()
    {
        //$passwordOld = $this->entity->getPassword();
    }
```
#### Ejecutar antes de flush de la entidad en updateAction:
```php
    protected function preUpdateEntity()
    {
        //$this->entity->setEnabled(true);
    }
```
#### Ejecutar antes de remover la entidad en deleteAction:
```php
    protected function preRemoveEntity()
    {
        //$this->entity->getChild()->setParent(null);
    }
```

* [Vistas](vistas.md)
* [README](https://github.com/MWSimple/AdminCrudBundle/blob/version30/README.md)
