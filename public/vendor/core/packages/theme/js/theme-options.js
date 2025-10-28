/******/ (() => { // webpackBootstrap
/*!***************************************************************!*\
  !*** ./platform/packages/theme/resources/js/theme-options.js ***!
  \***************************************************************/
$(function () {
  if ($(document).find('.colorpicker-input').length > 0) {
    $(document).find('.colorpicker-input').colorpicker();
  }
  if ($(document).find('.iconpicker-input').length > 0) {
    $(document).find('.iconpicker-input').iconpicker({
      selected: true,
      hideOnSelect: true
    });
  }
  $(document).on('submit', '.theme-option form', function (event) {
    event.preventDefault();
    var $form = $(event.currentTarget);
    var $button = $form.find('button[type="submit"]');
    if (typeof tinymce != 'undefined') {
      for (var instance in tinymce.editors) {
        if (tinymce.editors[instance].getContent) {
          $('#' + instance).html(tinymce.editors[instance].getContent());
        }
      }
    }
    Botble.showButtonLoading($button);
    $httpClient.make().post($form.prop('action'), new FormData($form[0])).then(function (_ref) {
      var data = _ref.data;
      Botble.showSuccess(data.message);
      $form.removeClass('dirty');
    })["finally"](function () {
      Botble.hideButtonLoading($button);
    });
  });
  $('.theme-option button[data-bs-toggle="pill"]').on('shown.bs.tab', function () {
    Botble.initResources();
    if (typeof EditorManagement != 'undefined') {
      window.EDITOR = new EditorManagement().init();
      window.EditorManagement = window.EditorManagement || EditorManagement;
    }
  });
});
/******/ })()
;