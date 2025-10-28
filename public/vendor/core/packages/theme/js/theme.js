/******/ (() => { // webpackBootstrap
/*!*******************************************************!*\
  !*** ./platform/packages/theme/resources/js/theme.js ***!
  \*******************************************************/
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
var ThemeManagement = /*#__PURE__*/function () {
  function ThemeManagement() {
    _classCallCheck(this, ThemeManagement);
  }
  return _createClass(ThemeManagement, [{
    key: "init",
    value: function init() {
      $(document).on('click', '.btn-trigger-active-theme', function (event) {
        event.preventDefault();
        var _self = $(event.currentTarget);
        Botble.showButtonLoading(_self);
        $httpClient.make().post(_self.data('url')).then(function (_ref) {
          var data = _ref.data;
          Botble.showSuccess(data.message);
          window.location.reload();
        })["finally"](function () {
          Botble.hideButtonLoading(_self);
        });
      });
      $(document).on('click', '.btn-trigger-remove-theme', function (event) {
        event.preventDefault();
        var _self = $(event.currentTarget);
        $('#confirm-remove-theme-button').data('theme', _self.data('theme')).data('url', _self.data('url'));
        $('#remove-theme-modal').modal('show');
      });
      $(document).on('click', '#confirm-remove-theme-button', function (event) {
        event.preventDefault();
        var _self = $(event.currentTarget);
        Botble.showButtonLoading(_self);
        $httpClient.make().post(_self.data('url')).then(function (_ref2) {
          var data = _ref2.data;
          Botble.showSuccess(data.message);
          window.location.reload();
        })["finally"](function () {
          Botble.hideButtonLoading(_self);
          $('#remove-theme-modal').modal('hide');
        });
      });
    }
  }]);
}();
$(function () {
  new ThemeManagement().init();
});
/******/ })()
;