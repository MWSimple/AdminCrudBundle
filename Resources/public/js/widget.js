$(document).ready(function() {
    if ($(".mws_checkbox").size()){
        $('input[type="checkbox"].mws_checkbox').checkbox({
            buttonStyle: 'btn btn-danger',
            buttonStyleChecked: 'btn btn-success',
            checkedClass: 'glyphicon glyphicon-check',
            uncheckedClass: 'glyphicon glyphicon-unchecked'
        });
    }
    //FORM VALIDATOR
    $('form').validator();
});