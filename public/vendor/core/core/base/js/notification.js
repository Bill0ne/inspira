/******/ (() => { // webpackBootstrap
/*!*********************************************************!*\
  !*** ./platform/core/base/resources/js/notification.js ***!
  \*********************************************************/
$(function () {
  var $notification = $(document).find('#notification-sidebar');
  var updateNotificationsCount = function updateNotificationsCount() {
    $httpClient.make().get($notification.data('count-url')).then(function (_ref) {
      var data = _ref.data;
      $(document).find('.badge.notification-count').text(data.data);
    });
  };
  var updateNotificationsContent = function updateNotificationsContent(url) {
    $httpClient.make().get(url || $notification.data('url')).then(function (_ref2) {
      var data = _ref2.data;
      $notification.find('.notification-content').html(data.data);
    });
  };
  var closeNotification = function closeNotification() {
    $notification.offcanvas('hide');
  };
  $notification.on('hide.bs.offcanvas', function () {
    $('.offcanvas-backdrop').remove();
  });
  $(document).on('click', '.offcanvas-backdrop', function () {
    $(this).remove();
    closeNotification();
  });
  $notification.on('show.bs.offcanvas', function () {
    updateNotificationsContent();
    $('body').after("<div class=\"offcanvas-backdrop\"></div>");
  }).on('click', '.mark-all-notifications-as-read', function (e) {
    e.preventDefault();
    $httpClient.make().put($(this).data('url')).then(function (_ref3) {
      var data = _ref3.data;
      $notification.find('.notification-content').html(data.data);
    })["finally"](function () {
      updateNotificationsCount();
      updateNotificationsContent();
    });
  }).on('click', '.clear-notifications', function () {
    $httpClient.make()["delete"]($(this).data('url')).then(function () {})["finally"](function () {
      updateNotificationsCount();
      closeNotification();
    });
  }).on('click', '.list-group-item .btn-delete-notification', function () {
    var _this = this;
    $httpClient.make()["delete"]($(this).data('url')).then(function () {
      var $this = $(_this).closest('.list-group-item');
      $this.hide('slow', function () {
        $this.remove();
        updateNotificationsContent();
      });
    })["finally"](function () {
      updateNotificationsCount();
    });
  }).on('click', 'nav .btn-previous', function () {
    updateNotificationsContent($(this).data('url'));
  }).on('click', 'nav .btn-next', function () {
    updateNotificationsContent($(this).data('url'));
  });
});
/******/ })()
;