$(document).ready(function () {

    // Handle clear logs button click
    $(document).on('click', '.action-item.clear_logs', function (e) {
        e.preventDefault();

        Botble.showConfirm({
            title: 'Clear all logs',
            message: 'Are you sure you want to clear all logs? This action cannot be undone.',
            onConfirm: () => {
                $.ajax({
                    url: route('ical.logs.clear'),
                    type: 'POST',
                    success: function (res) {
                        if (res.error) {
                            Botble.showError(res.message);
                        } else {
                            Botble.showSuccess(res.message);
                            window.location.reload();
                        }
                    },
                    error: function (res) {
                        Botble.handleError(res);
                    }
                });
            }
        });
    });

    // Handle sync calendar button click
    $(document).on('click', '.sync-calendar', function (e) {
        e.preventDefault();

        const button = $(this);
        const roomId = button.data('room-id');

        button.prop('disabled', true).addClass('button-loading');

        $.ajax({
            url: route('ical.sync'),
            type: 'POST',
            data: {
                room_id: roomId,
            },
            success: function (res) {
                if (res.error) {
                    Botble.showError(res.message);
                } else {
                    Botble.showSuccess(res.message);
                }
                button.prop('disabled', false).removeClass('button-loading');
            },
            error: function (res) {
                Botble.handleError(res);
                button.prop('disabled', false).removeClass('button-loading');
            }
        });
    });

    // Handle sync all calendars button click
    $(document).on('click', '.sync-all-calendars', function (e) {
        e.preventDefault();

        const button = $(this);

        button.prop('disabled', true).addClass('button-loading');

        $.ajax({
            url: route('ical.sync.all'),
            type: 'POST',
            success: function (res) {
                if (res.error) {
                    Botble.showError(res.message);
                } else {
                    Botble.showSuccess(res.message);
                }
                button.prop('disabled', false).removeClass('button-loading');
            },
            error: function (res) {
                Botble.handleError(res);
                button.prop('disabled', false).removeClass('button-loading');
            }
        });
    });

    // Handle delete calendar button click
    $(document).on('click', '.delete-calendar', function (e) {
        e.preventDefault();

        const button = $(this);
        const url = button.data('url');

        Botble.showConfirm({
            title: 'Delete calendar',
            message: 'Are you sure you want to delete this calendar?',
            onConfirm: () => {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    success: function (res) {
                        if (res.error) {
                            Botble.showError(res.message);
                        } else {
                            Botble.showSuccess(res.message);
                            button.closest('tr').remove();
                        }
                    },
                    error: function (res) {
                        Botble.handleError(res);
                    }
                });
            }
        });
    });
});
