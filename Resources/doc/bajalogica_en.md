## Logical erasing

### Configuration

File `config.yml`

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

File `services.yml`

```yaml
services:
    mwsimple.request_listener:
        class: MWSimple\Bundle\AdminCrudBundle\EventListener\RequestListener
        arguments: ['@doctrine.orm.entity_manager']
        tags:
            - { name: kernel.event_listener, event: kernel.request, method: onKernelRequest }
```

File is generate: Bundle/Resources/config/Post.yml (Example Entity is Post)

```yaml
# ...
logical_erasing: true
# ...
```

File entity Post.php (Example Entity is Post)

```php
// ...
use MWSimple\Bundle\AdminCrudBundle\Entity\LogicalErasingInterface;
// ...
class Post implements LogicalErasingInterface
// ...
```

* [Uploads files](subirarchivos_en.md)
* [README](README_EN.md)
