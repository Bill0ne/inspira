/******/ (() => { // webpackBootstrap
/*!***************************************************************!*\
  !*** ./platform/plugins/contact/resources/js/custom-field.js ***!
  \***************************************************************/
$(function () {
  $('.custom-field-options').sortable({
    cursor: 'move'
  });
  $(document).on('change', '.custom-field-form select[name="type"]', function (e) {
    var $currentTarget = $(e.currentTarget);
    var $customFieldOptionsBox = $currentTarget.closest('form').find('.custom-field-options-box');
    if ($currentTarget.val() === 'dropdown' || $currentTarget.val() === 'radio') {
      $customFieldOptionsBox.show();
    } else {
      $customFieldOptionsBox.hide();
    }
  }).on('click', '[data-bb-toggle="add-option"]', function (e) {
    e.preventDefault();
    var $currentTarget = $(e.currentTarget);
    var $table = $currentTarget.closest('.card').find('.custom-field-options');
    var $tr = $table.find('tr').last().clone();
    var index = $table.find('tr').length;
    $tr.find('[data-bb-toggle="option-label"]').val('').prop('name', "options[".concat(index, "][label]"));
    $tr.find('[data-bb-toggle="option-value"]').val('').prop('name', "options[".concat(index, "][value]"));
    $table.append($tr);
  }).on('click', '[data-bb-toggle="remove-option"]', function (e) {
    e.preventDefault();
    var $currentTarget = $(e.currentTarget);
    var $tr = $currentTarget.closest('tr');
    $tr.remove();
  });
});
/******/ })()
;