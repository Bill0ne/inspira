/******/ (() => { // webpackBootstrap
/*!***********************************************************!*\
  !*** ./platform/core/dashboard/resources/js/dashboard.js ***!
  \***********************************************************/
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
var callbackWidgets = {};
var BDashboard = /*#__PURE__*/function () {
  function BDashboard() {
    _classCallCheck(this, BDashboard);
  }
  return _createClass(BDashboard, [{
    key: "init",
    value: function init() {
      $('[data-bb-toggle="widgets-list"]').on('click', '.page-link', function (e) {
        e.preventDefault();
        var $this = $(e.currentTarget);
        var href = $this.prop('href');
        if (href) {
          BDashboard.loadWidget($this.closest('.widget-item').find('.widget-content'), href);
        }
      });
      $(document).on('click', '.card-actions .dropdown.predefined_range .dropdown-item', function (e) {
        e.preventDefault();
        var $this = $(e.currentTarget);
        $this.closest('.dropdown').find('.dropdown-toggle').text($this.data('label'));
        $this.closest('.dropdown').find('.dropdown-item').removeClass('active');
        $this.addClass('active');
        BDashboard.loadWidget($this.closest('.widget-item').find('.widget-content'), $this.closest('.widget-item').data('url'), {
          changed_predefined_range: 1
        });
      });
    }
  }], [{
    key: "loadWidget",
    value: function loadWidget($widget, url, data, callback) {
      var widget = $widget.closest('.widget-item');
      var widgetId = widget.prop('id');
      var loading = widget.find('.card');
      if (typeof callback !== 'undefined') {
        callbackWidgets[widgetId] = callback;
      }
      var $collapseExpand = widget.find('a.collapse-expand');
      if ($collapseExpand.length && $collapseExpand.hasClass('collapse')) {
        return;
      }
      Botble.showLoading(loading);
      if (typeof data === 'undefined' || data == null) {
        data = {};
      }
      var predefinedRange = widget.find('.dropdown.predefined_range .dropdown-item.active');
      if (predefinedRange.length) {
        data.predefined_range = predefinedRange.data('key');
      }
      $httpClient.makeWithoutErrorHandler().get(url, data).then(function (_ref) {
        var data = _ref.data;
        $widget.html(data.data);
        if (typeof callback !== 'undefined') {
          callback();
        } else if (callbackWidgets[widgetId]) {
          callbackWidgets[widgetId]();
        }
        BDashboard.initSortable();
      })["catch"](function (_ref2) {
        var _response$data;
        var response = _ref2.response,
          message = _ref2.message;
        var content = response === null || response === void 0 || (_response$data = response.data) === null || _response$data === void 0 ? void 0 : _response$data.data;
        if (!content && message) {
          content = "<div class=\"empty\"><p class=\"empty-subtitle text-muted\">".concat(message, "</p></div>");
        }
        $widget.html(content);
      })["finally"](function () {
        Botble.hideLoading(loading);
      });
    }
  }, {
    key: "initSortable",
    value: function initSortable() {
      var $widgetsList = $('[data-bb-toggle="widgets-list"]');
      if ($widgetsList.length > 0) {
        Sortable.create($widgetsList[0], {
          group: 'widgets',
          sort: true,
          delay: 0,
          disabled: false,
          store: null,
          animation: 150,
          handle: '.card-header',
          ghostClass: 'sortable-ghost',
          chosenClass: 'sortable-chosen',
          dataIdAttr: 'data-id',
          forceFallback: false,
          fallbackClass: 'sortable-fallback',
          fallbackOnBody: false,
          scroll: true,
          scrollSensitivity: 30,
          scrollSpeed: 10,
          onUpdate: function onUpdate() {
            var items = [];
            $.each($('.widget-item'), function (index, widget) {
              items.push($(widget).prop('id'));
            });
            $httpClient.makeWithoutErrorHandler().post($widgetsList.data('url'), {
              items: items
            }).then(function (_ref3) {
              var data = _ref3.data;
              Botble.showSuccess(data.message);
            });
          }
        });
      }
    }
  }]);
}();
$(function () {
  new BDashboard().init();
  window.BDashboard = BDashboard;
  $(document).on('submit', '[data-bb-toggle="widgets-management-modal"] form', function (event) {
    event.preventDefault();
    var form = $(event.currentTarget);
    var modal = $(this).closest('.modal');
    $httpClient.make().withButtonLoading(form.find('button[type="submit"]')).postForm(form.prop('action'), new FormData(form[0])).then(function (_ref4) {
      var data = _ref4.data;
      Botble.showSuccess(data.message);
      setTimeout(function () {
        window.location.reload();
      }, 1000);
    })["finally"](function () {
      modal.modal('hide');
    });
  }).on('change', '[data-bb-toggle="widgets-management-item"]', function (event) {
    var $this = $(event.currentTarget);
    if ($this.prop('checked')) {
      $this.closest('td').removeClass('text-decoration-line-through text-muted');
    } else {
      $this.closest('td').addClass('text-decoration-line-through text-muted');
    }
  });
});
/******/ })()
;