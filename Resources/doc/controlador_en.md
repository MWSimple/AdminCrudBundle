## Controller

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
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository($repository)
            ->createQueryBuilder('a')
            ->orderBy('a.id', 'DESC')
        ;

        return $queryBuilder;
    }
```

* [Views](vistas_en.md)
* [README](README_EN.md)
