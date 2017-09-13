$("#generatepdf").click(function( event ) {
  event.preventDefault();
  findData(this.href, returnPdf);
});

function findData(href, callback) {
  $.ajax({
    type: "POST",
    url: href,
    success: function (res) {
      callback(res);
    },
    error: function (data) {
        toastr.error("Error");
    }
  });
}

function returnPdf(res) {
  pdfMake.createPdf(res.data).download(res.filename);
}