## Config

### File is generate: Bundle/Resources/config/Post.yml (Example Entity is Post)
#### Not to include an action on the crud comment index.
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
        type: 'integer' #Types: 'string', 'datetime', 'datetimetz', 'date', 'time', 'boolean', 'ONE_TO_MANY', 'MANY_TO_MANY', 'vich_file', 'money'
        export: true #Exports the field
    a.image:
        ...
        type: 'vich_file' #Type for preview image
        file: 'imageFile' #Field @Vich\UploadableField of the entity
        export: false #Setear false
    a.money:
        ...
        type: 'money'
        currency_style: 'currency'
        currency_type: 'double'
        ...
fieldsshow: #Fields displayed in the show Entity
    a.id:
        label: 'Codigo' #Label
        name: 'Id' #Field name
        type: 'integer' #Types: 'string', 'datetime', 'datetimetz', 'date', 'time', 'boolean', 'ONE_TO_MANY', 'MANY_TO_MANY', 'vich_file', 'vich_file_many, 'money'
        class: 'col-lg-8 col-md-6 col-sm-12' #or other class. Default is col-12
        #closerow: true #this close row for the separators of col (http://getbootstrap.com/)
        separator: '<br>' #optional tag html by ONE_TO_MANY || MANY_TO_MANY
    a.image:
        ...
        type: 'vich_file' #Type for preview image
        file: 'imageFile' #Field @Vich\UploadableField of the entity
        ...
    a.images:
        ...
        type: 'vich_file_many' #Type for the preview image collection
        file: 'imageFile' #Field @Vich\UploadableField of the related entity
        file_name: 'imageName' #The field where the name or path is saved image
        # file_class: 'col-md-1' #The default is 'col-md-1'
        # imagine_filter: 'my_thumb_list' #Optionally filter can be used for example 'my_thumb_list' listing
        ...
    a.money:
        ...
        type: 'money'
        currency_style: 'currency'
        currency_type: 'double'
        ...
```
#### Default not escape ```twig {{ value|raw }} ```

#### Documentation for the money field is used localizedcurrency: [TwigExtensions](http://twig.sensiolabs.org/doc/extensions/intl.html) .

* [Controller](controlador_en.md)
* [README](README_EN.md)
