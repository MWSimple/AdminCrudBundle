## Config

### File is generate: Bundle/Resources/config/Post.yml (Example Entity is Post)
```yaml
entityName: 'Post' #Entity name
entity: 'Sistema\CPCEBundle\Entity\Post' #Path
repository: 'SistemaCPCEBundle:Post' #Repository
index: 'admin_post' #Routes for routing
new: 'admin_post_new'
edit: 'admin_post_edit'
show: 'admin_post_show'
create: 'admin_post_create'
update: 'admin_post_update'
delete: 'admin_post_delete'
export: 'admin_post_export' #Use sonata export
sessionFilter: 'TrabajoControllerFilter' #Session for the filter according to entity
saveAndAdd: true #If true then add the button to save and add another
validator: false #If true added by JS validation on forms (*No maintenance)
fieldsindex: #Fields displayed in the index list
    a.id:
        label: 'Code' #Label
        name: 'Id' #Field name
        type: 'integer' #Types: 'string', 'datetime', 'datetimetz', 'date', 'time', 'boolean', 'ONE_TO_MANY', 'MANY_TO_MANY', 'vich_file'
        export: true #Exports the field
    a.image:
        label: 'Image' #Label
        name: 'Image' #Field name
        type: 'vich_file' #Type for preview image
        file: 'imageFile' #Field @Vich\UploadableField of the entity
        export: false #Setear false
fieldsshow: #Fields displayed in the show Entity
    a.id:
        label: 'Codigo' #Label
        name: 'Id' #Field name
        type: 'integer' #Types: 'datetime', 'datetimetz', 'date', 'time', 'boolean', 'ONE_TO_MANY', 'MANY_TO_MANY', 'string'
        class: 'col-lg-8 col-md-6 col-sm-12' #or other class. Default is col-12
        #closerow: true #this close row for the separators of col (http://getbootstrap.com/)
        separator: '<br>' #optional tag html by ONE_TO_MANY || MANY_TO_MANY
```
#### Default not escape ```twig {{ value|raw }} ```

* [Views](vistas_en.md)
* [README](README_EN.md)
