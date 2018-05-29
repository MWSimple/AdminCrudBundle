## Baja lógica

### Configuración

Archivo `config.yml`

```yaml
# Doctrine Configuration
doctrine:
    # ...
    orm:
        # ...
        filters:
            # ...
            logical_erasing_filter: MWSimple\Bundle\AdminCrudBundle\Doctrine\LogicalErasingFilter
```

Archivo `services.yml`

```yaml
services:
    mwsimple.request_listener:
        class: MWSimple\Bundle\AdminCrudBundle\EventListener\RequestListener
        arguments: ['@doctrine.orm.entity_manager']
        tags:
            - { name: kernel.event_listener, event: kernel.request, method: onKernelRequest }
```

Archivo de configuración generado: Bundle/Resources/config/Post.yml (Por ejemplo si la Entity fuera Post)

```yaml
# ...
logical_erasing: true
# ...
```

Archivo Bundle/Entity/Post.php (Por ejemplo si la Entity fuera Post)

```php
// ...
use MWSimple\Bundle\AdminCrudBundle\Entity\LogicalErasingInterface;
// ...
class Post implements LogicalErasingInterface
    // Construct.
    public function __construct()
    {
        $this->logicalErasing = false;
    }
    // ...
    /**
     * @ORM\Column(name="logical_erasing", type="boolean")
     */
    private $logicalErasing;
    // ...
```

* [Subir archivos](subirarchivos.md)
* [README](https://github.com/MWSimple/AdminCrudBundle/blob/version30/README.md)
