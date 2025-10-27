/******/ (() => { // webpackBootstrap
/*!**********************************************************!*\
  !*** ./platform/plugins/contact/resources/js/contact.js ***!
  \**********************************************************/
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
var ContactPluginManagement = /*#__PURE__*/function () {
  function ContactPluginManagement() {
    _classCallCheck(this, ContactPluginManagement);
  }
  return _createClass(ContactPluginManagement, [{
    key: "init",
    value: function init() {
      $(document).on('click', '.answer-trigger-button', function (event) {
        event.preventDefault();
        event.stopPropagation();
        var answerWrapper = $('.answer-wrapper');
        if (answerWrapper.is(':visible')) {
          answerWrapper.fadeOut();
        } else {
          answerWrapper.fadeIn();
        }
        window.EDITOR = new EditorManagement().init();
      });
      $(document).on('click', '.answer-send-button', function (event) {
        event.preventDefault();
        event.stopPropagation();
        var _self = $(event.currentTarget);
        Botble.showButtonLoading(_self);
        var message = $('#message').val();
        if (typeof tinymce != 'undefined') {
          message = tinymce.get('message').getContent();
        }
        $httpClient.make().post(_self.data('url'), {
          message: message
        }).then(function (_ref) {
          var data = _ref.data;
          $('.answer-wrapper').fadeOut();
          if (typeof tinymce != 'undefined') {
            tinymce.get('message').setContent('');
          } else {
            $('#message').val('');
            var domEditableElement = document.querySelector('.answer-wrapper .ck-editor__editable');
            if (domEditableElement) {
              var editorInstance = domEditableElement.ckeditorInstance;
              if (editorInstance) {
                editorInstance.setData('');
              }
            }
          }
          Botble.showSuccess(data.message);
          $('#reply-wrapper').load(window.location.href + ' #reply-wrapper > *');
        })["finally"](function () {
          Botble.hideButtonLoading($(event.currentTarget));
        });
      });
    }
  }]);
}();
$(function () {
  new ContactPluginManagement().init();
});
/******/ })()
;