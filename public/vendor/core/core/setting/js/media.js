/******/ (() => { // webpackBootstrap
/*!*****************************************************!*\
  !*** ./platform/core/setting/resources/js/media.js ***!
  \*****************************************************/
$(function () {
  $('.generate-thumbnails-trigger-button').on('click', function (event) {
    event.preventDefault();
    var currentTarget = $(event.currentTarget);
    var $form = currentTarget.closest('form');
    $httpClient.make().withButtonLoading(currentTarget).postForm($form.prop('action'), new FormData($form[0])).then(function (_ref) {
      var data = _ref.data;
      var $modal = $('#generate-thumbnails-modal');
      $modal.modal('show');
      $modal.data('total-files', data.data.files_count);
    });
  });
  $('#generate-thumbnails-button').on('click', function (event) {
    event.preventDefault();
    var currentTarget = $(event.currentTarget);
    var $modal = currentTarget.closest('.modal');
    var $form = currentTarget.closest('form');
    var totalFiles = $modal.data('total-files');
    var message = null;
    Botble.showButtonLoading(currentTarget);
    function sendRequest() {
      var offset = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 0;
      var limit = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : $modal.data('chunk-limit');
      if (offset > totalFiles) {
        Botble.hideButtonLoading(currentTarget);
        $modal.modal('hide');
        Botble.showSuccess(message);
        return;
      }
      $httpClient.make().post($form.prop('action'), {
        total: totalFiles,
        offset: offset,
        limit: limit
      }).then(function (_ref2) {
        var data = _ref2.data;
        message = data.message;
        if (data.data.next) {
          sendRequest(data.data.next, limit);
        }
      })["finally"](function () {
        if (offset > totalFiles) {
          Botble.hideButtonLoading(currentTarget);
          $modal.modal('hide');
        }
      });
    }
    sendRequest();
  });
  $(document).on('change', '.check-all', function (event) {
    var currentTarget = $(event.currentTarget);
    var set = currentTarget.attr('data-set');
    var checked = currentTarget.prop('checked');
    $(set).each(function (index, el) {
      $(el).prop('checked', checked);
    });
  });
});
/******/ })()
;