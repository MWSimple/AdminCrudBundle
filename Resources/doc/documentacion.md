##ACL

[Documentation](http://symfony.com/doc/2.3/cookbook/security/acl.html)
[implement user recommend FOSUserBundle](https://github.com/FriendsOfSymfony/FOSUserBundle)
### Configuration

You can configure `config.yml` default query parameter names and templates

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
        exclude_role: ROLE_SUPER_ADMIN #exclude role the control, default false
#entities use
        entities:
            - Acme\DemoBundle\Entity\Post
            - Acme\DemoBundle\Entity\Post2
```

##Is included in the forms: jQuery plugin to validate form fields with Bootstrap 3+

[GitHub](https://github.com/nghuuphuoc/bootstrapvalidator)

##If Embed a Collection of Forms

[Documentation](http://symfony.com/doc/current/cookbook/form/form_collections.html)

###Using methods: addForm() and removeForm(), included in:

```twig
<script src="{{ asset('bundles/mwsimpleadmincrud/js/addForm.js') }}"></script>
```

*if not using the validation does not work

[README](https://github.com/MWSimple/AdminCrudBundle/blob/version30/README.md)
