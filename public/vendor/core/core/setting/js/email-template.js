/******/ (() => { // webpackBootstrap
/*!**************************************************************!*\
  !*** ./platform/core/setting/resources/js/email-template.js ***!
  \**************************************************************/
$(function () {
  $('[data-bb-toggle="email-status-toggle"]').on('change', function (event) {
    var _self = $(event.currentTarget);
    var key = _self.prop('name');
    var url = _self.data('change-url');
    _self.closest('tr').find('td.template-name > span').toggleClass('text-muted text-decoration-line-through');
    $httpClient.make().post(url, {
      key: key,
      value: _self.prop('checked') ? 1 : 0
    }).then(function (_ref) {
      var data = _ref.data;
      return Botble.showSuccess(data.message);
    });
  });
  $(document).on('click', '[data-bb-toggle="reset-default"]', function (event) {
    event.preventDefault();
    $('#reset-template-to-default-button').data('target', $(event.currentTarget).prop('href'));
    $('#reset-template-to-default-modal').modal('show');
  });
  $(document).on('click', '[data-bb-toggle="twig-variable"]', function (event) {
    event.preventDefault();
    var $this = $(event.currentTarget);
    var doc = $('.CodeMirror')[0].CodeMirror;
    var key = '{{ ' + $this.data('key') + ' }}';

    // If there's a selection, replace the selection.
    if (doc.somethingSelected()) {
      doc.replaceSelection(key);
      return;
    }

    // Otherwise, we insert at the cursor position.
    var cursor = doc.getCursor();
    var pos = {
      line: cursor.line,
      ch: cursor.ch
    };
    doc.replaceRange(key, pos);
  });
  $(document).on('click', '[data-bb-toggle="twig-function"]', function (event) {
    event.preventDefault();
    var $this = $(event.currentTarget);
    $this.closest('.twig-template').find('.CodeMirror');
    var CodeMirror = $this.closest('.twig-template').find('.CodeMirror')[0].CodeMirror;
    var key = $this.data('sample');

    // If there's a selection, replace the selection.
    if (CodeMirror.somethingSelected()) {
      CodeMirror.replaceSelection(key);
      return;
    }

    // Otherwise, we insert at the cursor position.
    var cursor = CodeMirror.getCursor();
    var position = {
      line: cursor.line,
      ch: cursor.ch
    };
    CodeMirror.replaceRange(key, position);
  });
  $(document).on('click', '#reset-template-to-default-button', function (event) {
    event.preventDefault();
    var _self = $(event.currentTarget);
    Botble.showButtonLoading(_self);
    $httpClient.make().post(_self.data('target'), {
      email_subject_key: $('input[name=email_subject_key]').val(),
      module: $('input[name=module]').val(),
      template_file: $('input[name=template_file]').val()
    }).then(function (_ref2) {
      var data = _ref2.data;
      Botble.showSuccess(data.message);
      setTimeout(function () {
        window.location.reload();
      }, 1000);
      $('#reset-template-to-default-modal').modal('hide');
    })["finally"](function () {
      Botble.hideButtonLoading(_self);
    });
  });
});
/******/ })()
;