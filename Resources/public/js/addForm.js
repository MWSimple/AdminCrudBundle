function addForm(collection,target) {
    // Get the data-prototype explained earlier
    var prototype = collection.data('prototype');
    // get the new index
    var index = collection.data('index');

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    var newForm = prototype.replace(/__name__/g, index);

    // increase the index with one for the next item
    collection.data('index', index + 1);
    target.append(newForm);
    //recorro los input del newForm para validarlos
    collection.find(':input').each(function() {
        if (this.type !== 'hidden') {
            var $option = this.name;
            // Add new field
            $('form').bootstrapValidator('addField', $option);
        };
    });
    //render checkbox
    if ($(".mws_checkbox").size()){
        $('input[type="checkbox"].mws_checkbox').checkbox({
            buttonStyle: 'btn btn-base',
            buttonStyleChecked: 'btn btn-success',
            checkedClass: 'glyphicon glyphicon-check',
            uncheckedClass: 'glyphicon glyphicon-unchecked'
        });
    }
    return index;
}

function removeForm(form) {
    //recorro los input del form para no validarlos
    form.find(':input').each(function() {
        var $option = this.name;
        // Remove field
        $('form').bootstrapValidator('removeField', $option);
    });
    form.remove();
}