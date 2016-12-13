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
    target.before(newForm);
    //render checkbox
    if ($(".mws_checkbox").size()){
        $('input[type="checkbox"].mws_checkbox').checkbox({
            buttonStyle: 'btn btn-danger',
            buttonStyleChecked: 'btn btn-success',
            checkedClass: 'glyphicon glyphicon-check',
            uncheckedClass: 'glyphicon glyphicon-unchecked'
        });
    }
    return index;
}

function removeForm(form) {
    form.remove();
}
