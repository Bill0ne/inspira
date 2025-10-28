/******/ (() => { // webpackBootstrap
/*!********************************************************************!*\
  !*** ./platform/plugins/price-configurator/resources/js/script.js ***!
  \********************************************************************/
$(function () {
  var $scope = $('[name="scope"]');
  var $targetType = $('[name="target_type"]');
  var $targetWrapper = $('.target-ids-wrapper');
  var $targetField = $targetWrapper.find('select[name^="target_ids"]');
  function toggleField($field) {
    var target = $($field.data('target'));
    var value = $field.val();
    if (!value || value === 'all_products') {
      target.hide().find('select').val(null).trigger('change');
    } else {
      target.show();
    }
  }
  function loadTargetOptions(scope, type) {
    var selectedIds = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : [];
    $targetField.empty().append('<option value="">Loading...</option>');
    $.ajax({
      url: $targetType.data('url'),
      type: 'GET',
      data: {
        scope: scope,
        target_type: type
      },
      success: function success(response) {
        $targetField.empty();
        if ($.isEmptyObject(response)) {
          $targetField.append('<option value="">No data found</option>');
          reinitSelect($targetField);
          return;
        }
        $.each(response, function (id, name) {
          var option = new Option(name, id, false, false);
          $targetField.append(option);
        });
        reinitSelect($targetField);
        if (selectedIds) {
          if (!Array.isArray(selectedIds)) {
            selectedIds = [selectedIds];
          }
          selectedIds = selectedIds.map(String);
          console.log('Preselecting IDs:', selectedIds);
          $targetField.val(selectedIds).trigger('change');
        }
      },
      error: function error() {
        $targetField.empty().append('<option value="">Error loading data</option>');
        reinitSelect($targetField);
      }
    });
  }
  var initialScope = $scope.val();
  var initialTargetType = $targetType.val();
  var initialTargetIds = $targetWrapper.data('selected') || [];
  if (typeof initialTargetIds === 'string') {
    initialTargetIds = initialTargetIds.split(',').map(function (id) {
      return id.trim();
    });
  }
  if (initialScope && initialTargetType) {
    loadTargetOptions(initialScope, initialTargetType, initialTargetIds);
  }
  $scope.each(function () {
    var $field = $(this);
    toggleField($field);
    $field.on('change', function () {
      toggleField($field);
    });
  });
  function reinitSelect($select) {
    try {
      if ($select.data('select2')) $select.select2('destroy');
      $select.select2({
        width: '100%',
        allowClear: true,
        placeholder: 'Select options...'
      });
    } catch (e) {}
  }
  $targetType.on('change', function () {
    var scope = $scope.val();
    var type = $(this).val();
    if (!$targetField.length || !scope || !type) return;
    loadTargetOptions(scope, type, []);
  });
});
/******/ })()
;