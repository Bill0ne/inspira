/******/ (() => { // webpackBootstrap
/*!*****************************************************!*\
  !*** ./platform/packages/slug/resources/js/slug.js ***!
  \*****************************************************/
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
var PermalinkField = /*#__PURE__*/_createClass(function PermalinkField() {
  _classCallCheck(this, PermalinkField);
  var $slugBox = $(document).find('.slug-field-wrapper');
  $(document).on('blur', ".js-base-form input[name=".concat($slugBox.data('field-name'), "]"), function (e) {
    $slugBox = $(document).find('.slug-field-wrapper');
    if ($slugBox.find('input[name="slug"]').is('[readonly]')) {
      return;
    }
    var value = $(e.currentTarget).val();
    if (value !== null && value !== '' && !$slugBox.find('input[name="slug"]').val()) {
      createSlug(value, 0);
    }
  });
  var timeoutId;
  $(document).on('keyup', 'input[name="slug"]', function (event) {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(function () {
      var input = $(event.currentTarget);
      $slugBox = $(document).find('.slug-field-wrapper');
      if ($slugBox.has('.slug-data').length === 0) {
        return;
      }
      var value = input.val();
      if (value !== null && value !== '') {
        createSlug(value, $slugBox.find('.slug-data').data('id') || 0);
      } else {
        input.addClass('is-invalid');
      }
    }, 700);
  });
  $(document).on('click', '[data-bb-toggle="generate-slug"]', function (e) {
    e.preventDefault();
    var $fromField = $(e.currentTarget).closest('.js-base-form').find("input[name=".concat($slugBox.data('field-name'), "]"));
    if ($fromField.val() !== null && $fromField.val() !== '') {
      createSlug($fromField.val(), $slugBox.find('.slug-data').data('id') || 0);
    }
  });
  var toggleInputSlugState = function toggleInputSlugState() {
    var isShow = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
    var $icon = $slugBox.find('.slug-actions a');
    var $spinner = $('<div class="spinner-border spinner-border-sm" role="status"></div>');
    if (isShow) {
      $icon.removeClass('d-none');
      $slugBox.find('.spinner-border').remove();
    } else {
      $icon.addClass('d-none');
      $icon.after($spinner);
    }
  };

  /**
   * @param {string} value
   * @param {number} id
   */
  var createSlug = function createSlug(value, id) {
    $slugBox = $(document).find('.slug-field-wrapper');
    var form = $slugBox.closest('form');
    var $slugId = $slugBox.find('.slug-data');
    if (!$slugBox.length || !$slugId.length || !form.length) {
      return;
    }
    toggleInputSlugState();
    $httpClient.make().post($slugId.data('url'), {
      value: value,
      slug_id: id.toString(),
      model: form.find('input[name="model"]').val(),
      _token: form.find('input[name="_token"]').val()
    }).then(function (_ref) {
      var data = _ref.data;
      toggleInputSlugState(true);
      var url = "".concat($slugId.data('view')).concat(data.toString().replace('/', ''));
      $slugBox.find('input[name="slug"]').val(data);
      form.find('.page-url-seo p').text(url);
      $slugBox.find('.slug-current').val(data);
    });
  };
});
$(function () {
  new PermalinkField();
});
/******/ })()
;