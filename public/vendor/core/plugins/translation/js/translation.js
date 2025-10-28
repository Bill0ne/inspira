/******/ (() => { // webpackBootstrap
/*!******************************************************************!*\
  !*** ./platform/plugins/translation/resources/js/translation.js ***!
  \******************************************************************/
$(function () {
  $(document).on('click', '.button-import-groups, .button-re-import', function (event) {
    event.preventDefault();
    var $button = $(event.currentTarget);
    $httpClient.make().withButtonLoading($button).postForm($button.data('url')).then(function (_ref) {
      var data = _ref.data;
      Botble.showSuccess(data.message);
      if ($button.closest('.modal').length) {
        $button.closest('.modal').modal('hide');
        var $table = $('.translations-table .table');
        if ($table.length) {
          $table.DataTable().ajax.url(window.location.href).load();
        } else {
          setTimeout(function () {
            window.location.reload();
          }, 1000);
        }
      } else {
        setTimeout(function () {
          window.location.reload();
        }, 1000);
      }
    });
  });
});
/******/ })()
;