## ACL

[Documentacion](http://symfony.com/doc/2.3/cookbook/security/acl.html)
[Se recomienda implementar FOSUserBundle](https://github.com/FriendsOfSymfony/FOSUserBundle)
### Configuracion

Configura los nombres de parametros por defecto para la query y templates en tu `config.yml`

```yaml
services:
#ACL manager
    mws_acl_manager:
        class: MWSimple\Bundle\AdminCrudBundle\Services\ACLManager
        arguments:
            - "@service_container"
#Listener delete ACL PostRemove
    mws_acl_listener:
        class: MWSimple\Bundle\AdminCrudBundle\EventListener\ACLListener
        arguments:
            - "@service_container"
        tags:
            - { name: doctrine.event_listener, event: preRemove }

mw_simple_admin_crud:
#...
    acl:
        use: true #default false
        exclude_role: ROLE_SUPER_ADMIN #no incluye role para control, default false
#entities que usas
        entities:
            - Acme\DemoBundle\Entity\Post
            - Acme\DemoBundle\Entity\Post2
```

* [Baja lógica](bajalogica.md)
* [README](README.md)
