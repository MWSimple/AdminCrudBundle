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
        var inputAjax = $(this);
        var inputButtonAjax = inputAjax.next().children('button');
        $.ajax({
            type: "POST",
            url: JsOptions.url,
            data: {repository: $(this).data('repository'), datafieldname: $(this).data('fieldname'), dataid: $(this).data('id')},
            success: function (res) {
                if (res == true) {
                    tpShowMessage('ajax-'+$(this).data('id'), 'success', JsOptions.messageSuccess);
                } else {
                    if (inputButtonAjax.hasClass('btn-success')) {
                        inputAjax.prop("checked", false);
                        inputButtonAjax.removeClass("btn-success");
                        inputButtonAjax.addClass("btn-danger");
                        inputButtonAjax.children('span.glyphicon-check').css("display", "none");
                        inputButtonAjax.children('span.glyphicon-unchecked').css("display", "");
                    } else if (inputButtonAjax.hasClass('btn-danger')) {
                        inputAjax.prop("checked", true);
                        inputButtonAjax.removeClass("btn-danger");
                        inputButtonAjax.addClass("btn-success");
                        inputButtonAjax.children('span.glyphicon-check').css("display", "");
                        inputButtonAjax.children('span.glyphicon-unchecked').css("display", "none");
                    }
                    tpShowMessage('ajax-'+$(this).data('id'), 'danger', JsOptions.messageError);
                }
            },
            error: function (data) {
                tpShowMessage('ajax-'+$(this).data('id'), 'danger', JsOptions.messageError);
            }
        });
    });
});