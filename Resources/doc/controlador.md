## Controlador

### Variables. Ejemplo $this->em
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
    // Permite configurar opciones en el formularios debe setearse un array
    protected $optionsForm = null;
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

    protected function addNewConfig(){} //Se utiliza para agregar nueva config
    protected function createNewEntity(){$this->entity = new $this->configArray['entity']();} //Se utiliza al instanciar la entidad en los metodos newAction() y createAction(Request $request).
    protected function prePersistEntity(){} //Se utiliza luego de validar formulario y antes del flush entidad en el metodo createAction(Request $request).
    protected function preHandleRequestEntity(){}
    protected function preFormIsValid(){} //Se ejecuta antes de validar el formulario en updateAction(Request $request, $id).
    protected function preUpdateEntity(){}
    protected function preRemoveEntity(){}
    protected function validateForm(){return true;} //Retorna el valor predeterminado verdadero. Se ejecuta antes de form->isValid() en createAction(Request $request) y updateAction(Request $request, $id).
```
#### Sobreescribir para generar la ruta a redireccionar luego de guardar la entity en createAction y updateAction:
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
#### Sobreescribir la instancia de la entidad en newAction y createAction:
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
