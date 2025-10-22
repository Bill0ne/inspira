$(function () {
    function toggleField($checkbox) {
        const target = $($checkbox.data('target'));
        const field = $checkbox.attr('name');

        if (field === 'unlimited_seats') {
            // Hide seats when unlimited seats = ON
            if ($checkbox.is(':checked')) {
                target.hide().find('input').val('');
            } else {
                target.show();
            }
        }

        if (field === 'is_recurring') {
            // Show recurring fields when recurring = ON
            if ($checkbox.is(':checked')) {
                target.show();
            } else {
                target.hide().find('input, select').val('');
            }
        }
    }

    $('[data-toggle="toggle-field"]').each(function () {
        const $checkbox = $(this);

        // Initial load
        toggleField($checkbox);

        // On change
        $checkbox.on('change', function () {
            toggleField($checkbox);
        });
    });
});
