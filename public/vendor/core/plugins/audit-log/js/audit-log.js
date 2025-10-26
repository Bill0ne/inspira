/******/ (() => { // webpackBootstrap
/*!**************************************************************!*\
  !*** ./platform/plugins/audit-log/resources/js/audit-log.js ***!
  \**************************************************************/
$(function () {
  if (typeof BDashboard !== 'undefined') {
    BDashboard.loadWidget($('#widget_audit_logs').find('.widget-content'), $('#widget_audit_logs').data('url'));
  }
  $(document).on('click', '.empty-activities-logs-button', function (event) {
    event.preventDefault();
    $('#modal-confirm-delete-records').modal('show');
    $('#modal-confirm-delete-records').on('click', '.button-delete-records', function (event) {
      event.preventDefault();
      var _self = $(event.currentTarget);
      $httpClient.make().withButtonLoading(_self).get(_self.data('url')).then(function (_ref) {
        var data = _ref.data;
        _self.closest('.modal').modal('hide');
        $('#botble-audit-log-tables-audit-log-table').DataTable().draw();
        return Botble.showSuccess(data.message);
      });
    });
  });
});
/******/ })()
;