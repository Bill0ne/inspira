/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/*!**********************************************************************!*\
  !*** ./platform/packages/shortcode/resources/js/shortcode-fields.js ***!
  \**********************************************************************/


$(function () {
  $(document).on('change', '.shortcode-tabs-quantity-select', function () {
    var $this = $(this);
    var quantity = parseInt($this.val()) || 1;
    var key = $this.data('key');
    var $section = $this.closest('.shortcode-tabs-field-wrapper');
    if (!$section.length) {
      $section = $this.closest('.shortcode-admin-config');
    }
    $section.find('.shortcode-template').first().clone().removeClass('shortcode-template');
    for (var index = 1; index <= $this.data('max'); index++) {
      var $el = key ? $section.find("[data-tab-id=".concat(key, "_").concat(index, "]")) : $section.find("[data-tab-id=".concat(index, "]"));
      if (index <= quantity) {
        if (!$el.is(':visible')) {
          $el.slideDown();
          $el.find('[data-name]').map(function (i, e) {
            return $(e).prop('name', $(e).data('name'));
          });
        }
      } else {
        $el.slideUp();
        $el.find('[name]').map(function (i, e) {
          $(e).data('name', $(e).prop('name'));
          $(e).removeProp('name');
        });
      }
    }
  });
});
/******/ })()
;