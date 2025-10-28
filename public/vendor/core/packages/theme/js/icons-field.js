/******/ (() => { // webpackBootstrap
/*!*************************************************************!*\
  !*** ./platform/packages/theme/resources/js/icons-field.js ***!
  \*************************************************************/
$(function () {
  'use strict';

  var initIconsField = function initIconsField() {
    var icons = window.themeIcons || [];
    if (!icons) {
      return;
    }
    $(document).find('.icon-select').each(function (index, element) {
      var $this = $(element);
      if ($this.data('check-initialized') && $this.hasClass('select2-hidden-accessible')) {
        return;
      }
      var value = $this.children('option:selected').val();
      value = value ? value : 0;
      var options = '<option value="0">' + $this.data('empty-value') + '</option>';
      icons.forEach(function (value) {
        options += '<option value="' + value + '">' + value + '</option>';
      });
      $this.html(options);
      $this.val(value);
      var templateCallback = function templateCallback(state) {
        if (!state.id) {
          return state.text;
        }
        return $("<span><i class=\"".concat(state.id, "\"></i></span> ").concat(state.text, "</span>"));
      };
      Botble.select(element, {
        templateResult: function templateResult(state) {
          return templateCallback(state);
        },
        templateSelection: function templateSelection(state) {
          return templateCallback(state);
        },
        placeholder: $this.data('empty-value')
      });
    });
  };
  initIconsField();
  document.addEventListener('core-init-resources', function () {
    initIconsField();
  });
});
/******/ })()
;