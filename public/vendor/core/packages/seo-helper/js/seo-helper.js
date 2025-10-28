/******/ (() => { // webpackBootstrap
/*!*****************************************************************!*\
  !*** ./platform/packages/seo-helper/resources/js/seo-helper.js ***!
  \*****************************************************************/
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
var SEOHelperManagement = /*#__PURE__*/function () {
  function SEOHelperManagement() {
    _classCallCheck(this, SEOHelperManagement);
    this.$document = $(document);
  }
  return _createClass(SEOHelperManagement, [{
    key: "handleMetaBox",
    value: function handleMetaBox() {
      var permalink = this.$document.find('#sample-permalink a');
      if (permalink.length) {
        $('.page-url-seo p').text(permalink.prop('href').replace('?preview=true', ''));
      }
      this.$document.on('click', '.btn-trigger-show-seo-detail', function (event) {
        event.preventDefault();
        $('.seo-edit-section').toggleClass('hidden');
      });
      this.$document.on('keyup', 'input[name=name]', function (event) {
        SEOHelperManagement.updateSEOTitle($(event.currentTarget).val());
      });
      this.$document.on('keyup', 'input[name=title]', function (event) {
        SEOHelperManagement.updateSEOTitle($(event.currentTarget).val());
      });
      this.$document.on('keyup', 'textarea[name=description]', function (event) {
        SEOHelperManagement.updateSEODescription($(event.currentTarget).val());
      });
      this.$document.on('keyup', '#seo_title', function (event) {
        if ($(event.currentTarget).val()) {
          $('.page-title-seo').text($(event.currentTarget).val());
          $('.default-seo-description').addClass('hidden');
          $('.existed-seo-meta').removeClass('hidden');
        } else {
          var $inputName = $('input[name=name]');
          if ($inputName.val()) {
            $('.page-title-seo').text($inputName.val());
          } else {
            $('.page-title-seo').text($('input[name=title]').val());
          }
        }
      });
      this.$document.on('keyup', '#seo_description', function (event) {
        if ($(event.currentTarget).val()) {
          $('.page-description-seo').text($(event.currentTarget).val());
        } else {
          $('.page-description-seo').text($('textarea[name=description]').val());
        }
      });
    }
  }], [{
    key: "updateSEOTitle",
    value: function updateSEOTitle(value) {
      if (value) {
        if (!$('#seo_title').val()) {
          $('.page-title-seo').text(value);
        }
        $('.default-seo-description').addClass('hidden');
        $('.existed-seo-meta').removeClass('hidden');
      } else {
        $('.default-seo-description').removeClass('hidden');
        $('.existed-seo-meta').addClass('hidden');
      }
    }
  }, {
    key: "updateSEODescription",
    value: function updateSEODescription(value) {
      if (value) {
        if (!$('#seo_description').val()) {
          $('.page-description-seo').text(value);
        }
      }
    }
  }]);
}();
$(function () {
  new SEOHelperManagement().handleMetaBox();
});
/******/ })()
;