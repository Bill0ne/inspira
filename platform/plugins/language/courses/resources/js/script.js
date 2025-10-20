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
    $(document).on('click', '.view-participants', function () {
        const button = $(this);
        const sessionId = button.data('id');
        const sessionName = button.data('name');

        // Show modal immediately
        $('#participantsModalLabel').text('Teilnehmer für -> ' + sessionName);
        $('#participantsModalContent').html('<div class="text-center py-4"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
        $('#participantsModal').modal('show');

        // Load participants
        $.ajax({
            url: `/admin/course-sessions/${sessionId}/participants`,
            type: 'GET',
            success: function (res) {
                $('#participantsModalContent').html(res.html);
            },
            error: function () {
                $('#participantsModalContent').html('<p class="text-danger text-center">Failed to load participants.</p>');
            }
        });
    });

    const $courseSelect = $('#course_admin_id');
    const $sessionSelect = $('#session_admin_id');

    $courseSelect.on('change', function () {
        const courseId = $(this).val();

        if (!courseId) {
            $sessionSelect.html('<option value="">Select a course first</option>');
            return;
        }

        const url = `/admin/courses/${courseId}/sessions`;

        $.ajax({
            url: url,
            type: 'GET',
            beforeSend: function () {
                $sessionSelect.html('<option>Loading sessions...</option>');
            },
            success: function (response) {
                if (response.data && response.data.length) {
                    let options = '<option value="">Select Session</option>';
                    response.data.forEach(function (session) {
                        options += `<option value="${session.id}">${session.text}</option>`;
                    });
                    $sessionSelect.html(options);
                } else {
                    $sessionSelect.html('<option value="">No sessions found</option>');
                }
            },
            error: function () {
                $sessionSelect.html('<option value="">Error loading sessions</option>');
            }
        });
    });

});
