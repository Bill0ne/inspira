/******/ (() => { // webpackBootstrap
/*!*********************************************************!*\
  !*** ./platform/plugins/courses/resources/js/script.js ***!
  \*********************************************************/
$(function () {
  function toggleField($checkbox) {
    var target = $($checkbox.data('target'));
    var field = $checkbox.attr('name');
    if (field === 'unlimited_seats') {
      if ($checkbox.is(':checked')) {
        target.hide().find('input').val('');
      } else {
        target.show();
      }
    }
    if (field === 'is_recurring') {
      if ($checkbox.is(':checked')) {
        target.show();
      } else {
        target.hide().find('input, select').val('');
      }
    }
  }
  $('[data-toggle="toggle-field"]').each(function () {
    var $checkbox = $(this);
    toggleField($checkbox);
    $checkbox.on('change', function () {
      toggleField($checkbox);
    });
  });
  $(document).on('click', '.view-participants-btn', function () {
    var sessionId = $(this).data('session-id');
    var modalContent = $('#participantsModalContent');
    modalContent.html('<i class="fa fa-spinner fa-spin"></i> Loading...');
    var url = "/admin/course-sessions/".concat(sessionId, "/bookings");
    $.ajax({
      url: url,
      method: 'GET',
      success: function success(html) {
        modalContent.html(html);
      },
      error: function error() {
        modalContent.html('<p>Error loading participants.</p>');
      }
    });
    $('#participantsModal').modal('show');
  });
  var $courseSelect = $('#admin_course_id');
  var $sessionSelect = $('#admin_session_id');
  var selectedSessionId = $sessionSelect.data('selected');
  $courseSelect.on('change', function () {
    var courseId = $(this).val();
    if (!courseId) {
      $sessionSelect.html('<option value="">Select a course first</option>');
      return;
    }
    var url = "/admin/courses/".concat(courseId, "/sessions");
    $.ajax({
      url: url,
      type: 'GET',
      beforeSend: function beforeSend() {
        $sessionSelect.html('<option>Loading sessions...</option>');
      },
      success: function success(response) {
        if (response.data && response.data.length) {
          var options = '<option value="">Select Session</option>';
          response.data.forEach(function (session) {
            var selectedAttr = session.id == selectedSessionId ? 'selected' : '';
            options += "<option value=\"".concat(session.id, "\" ").concat(selectedAttr, ">").concat(session.text, "</option>");
          });
          $sessionSelect.html(options);
        } else {
          $sessionSelect.html('<option value="">No sessions found</option>');
        }
      },
      error: function error() {
        $sessionSelect.html('<option value="">Error loading sessions</option>');
      }
    });
  });
  var initialCourseId = $courseSelect.val();
  if (initialCourseId) {
    $courseSelect.trigger('change');
  }
});
/******/ })()
;