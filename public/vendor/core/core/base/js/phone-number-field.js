/******/ (() => { // webpackBootstrap
/*!********************************************************************!*\
  !*** ./platform/core/base/resources/js/form/phone-number-field.js ***!
  \********************************************************************/
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
var PhoneNumberField = /*#__PURE__*/function () {
  function PhoneNumberField() {
    _classCallCheck(this, PhoneNumberField);
  }
  return _createClass(PhoneNumberField, [{
    key: "init",
    value: function init() {
      $(document).find('.js-phone-number-mask').each(function (index, element) {
        window.intlTelInput(element, {
          // allowDropdown: false,
          // autoHideDialCode: false,
          // autoPlaceholder: "off",
          // dropdownContainer: document.body,
          // excludeCountries: ["us"],
          // formatOnDisplay: false,
          geoIpLookup: function geoIpLookup(callback) {
            $httpClient.make().withCredentials(false).get('https://ipinfo.io').then(function (_ref) {
              var data = _ref.data;
              callback(data && data.country ? data.country : '');
            });
          },
          // hiddenInput: "full_number",
          initialCountry: 'auto',
          // localizedCountries: { 'de': 'Deutschland' },
          // nationalMode: false,
          // onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
          // placeholderNumberType: "MOBILE",
          // preferredCountries: ['cn', 'jp'],
          // separateDialCode: true,
          utilsScript: '/vendor/core/core/base/libraries/intl-tel-input/js/utils.js'
        });
      });
    }
  }]);
}();
$(function () {
  new PhoneNumberField().init();
  document.addEventListener('payment-form-reloaded', function () {
    new PhoneNumberField().init();
  });
});
/******/ })()
;