$(function () {
    const $scope = $('[name="scope"]');
    const $targetType = $('[name="target_type"]');
    const $targetWrapper = $('.target-ids-wrapper');
    const $targetField = $targetWrapper.find('select[name^="target_ids"]');

    function toggleField($field) {
        const target = $($field.data('target'));
        const value = $field.val();
        if (!value || value === 'all_products') {
            target.hide().find('select').val(null).trigger('change');
        } else {
            target.show();
        }
    }

    function loadTargetOptions(scope, type, selectedIds = []) {

        $targetField.empty().append('<option value="">Loading...</option>');

        $.ajax({
            url: $targetType.data('url'),
            type: 'GET',
            data: { scope, target_type: type },
            success: function (response) {

                $targetField.empty();

                if ($.isEmptyObject(response)) {
                    $targetField.append('<option value="">No data found</option>');
                    reinitSelect($targetField);
                    return;
                }

                $.each(response, function (id, name) {
                    const option = new Option(name, id, false, false);
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
            error: function () {
                $targetField.empty().append('<option value="">Error loading data</option>');
                reinitSelect($targetField);
            },
        });
    }

    const initialScope = $scope.val();
    const initialTargetType = $targetType.val();
    let initialTargetIds = $targetWrapper.data('selected') || [];

    if (typeof initialTargetIds === 'string') {
        initialTargetIds = initialTargetIds.split(',').map(id => id.trim());
    }

    if (initialScope && initialTargetType) {
        loadTargetOptions(initialScope, initialTargetType, initialTargetIds);
    }

    $scope.each(function () {
        const $field = $(this);
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
                placeholder: 'Select options...',
            });
        } catch (e) {
        }
    }

    $targetType.on('change', function () {
        const scope = $scope.val();
        const type = $(this).val();
        if (!$targetField.length || !scope || !type) return;
        loadTargetOptions(scope, type, []);
    });
});
