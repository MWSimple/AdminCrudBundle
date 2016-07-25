$(document).ready(function() {
    function tpShowMessage(ajaxid, type, message) {
        $("#tecspro-errors").prepend("<div id='" + ajaxid + "' class='alert alert-" + type + "'>" + message + "</div>");
        setTimeout(function(){
          if ($("#" + ajaxid).length > 0) {
            $("#" + ajaxid).remove();
          }
        }, 5000);
    }
    $('input[type="checkbox"].mws_checkbox').click(function() {
        $.ajax({
            type: "POST",
            url: AjaxOptions.url,
            data: {repository: $(this).data('repository'), datafieldname: $(this).data('fieldname'), dataid: $(this).data('id')},
            success: function (array) {
                if (array.resultado) {
                    tpShowMessage('ajax-'+$(this).data('id'), 'success', 'Actualizado');
                } else {
                    tpShowMessage('ajax-'+$(this).data('id'), 'danger', 'Error');
                }
            },
            error: function (data) {
                tpShowMessage('ajax-'+$(this).data('id'), 'danger', 'Error');
            }
        });
    });
});