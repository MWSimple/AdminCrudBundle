$(document).ready(function() {
    if ($(".mws_checkbox").size()){
        $('input[type="checkbox"].mws_checkbox').checkbox({
            buttonStyle: 'btn btn-danger',
            buttonStyleChecked: 'btn btn-success',
            checkedClass: 'glyphicon glyphicon-check',
            uncheckedClass: 'glyphicon glyphicon-unchecked'
        });
    }
    //Select2 class default
    $(".mws_select2").select2();
    //FORM VALIDATOR
    $('form').bootstrapValidator();
});