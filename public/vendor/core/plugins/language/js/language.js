/******/ (() => { // webpackBootstrap
/*!************************************************************!*\
  !*** ./platform/plugins/language/resources/js/language.js ***!
  \************************************************************/
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
var LanguageManagement = /*#__PURE__*/function () {
  function LanguageManagement() {
    _classCallCheck(this, LanguageManagement);
  }
  return _createClass(LanguageManagement, [{
    key: "init",
    value: function init() {
      var _this = this;
      Botble.select($('.select-search-language'), {
        templateResult: LanguageManagement.formatState,
        templateSelection: LanguageManagement.formatState
      });
      var languageTable = $('.table-language');
      $(document).on('change', '#language_id', function (event) {
        var language = $(event.currentTarget).find('option:selected').data('language');
        if (typeof language != 'undefined' && language.length > 0) {
          $('#lang_name').val(language[2]).trigger('change');
          $('#lang_locale').val(language[0]).trigger('change');
          $('#lang_code').val(language[1]).trigger('change');
          $("input[name=lang_rtl][value=\"".concat(language[3] === 'rtl' ? 1 : 0, "\"]")).prop('checked', true);
          $('#flag_list').val(language[4]).trigger('change');
          $('#btn-language-submit-edit').prop('id', 'btn-language-submit').text($('#btn-language-submit').data('add-language-text'));
        }
      });
      $(document).on('click', '#btn-language-submit', function (event) {
        event.preventDefault();
        var name = $('#lang_name').val();
        var locale = $('#lang_locale').val();
        var code = $('#lang_code').val();
        var flag = $('#flag_list').val();
        var order = $('#lang_order').val();
        var isRTL = $('input[name=lang_rtl]:checked').val();
        LanguageManagement.createOrUpdateLanguage(0, name, locale, code, flag, order, isRTL, 0);
      });
      $(document).on('click', '#btn-language-submit-edit', function (event) {
        event.preventDefault();
        var id = $('#lang_id').val();
        var name = $('#lang_name').val();
        var locale = $('#lang_locale').val();
        var code = $('#lang_code').val();
        var flag = $('#flag_list').val();
        var order = $('#lang_order').val();
        var isRTL = $('input[name=lang_rtl]:checked').val();
        var $button = $(event.currentTarget);
        LanguageManagement.createOrUpdateLanguage(id, name, locale, code, flag, order, isRTL, 1, $button);
      });
      languageTable.on('click', '.deleteDialog', function (event) {
        event.preventDefault();
        $('.delete-crud-entry').data('section', $(event.currentTarget).data('section'));
        $('.modal-confirm-delete').modal('show');
      });
      $('.delete-crud-entry').on('click', function (event) {
        event.preventDefault();
        $('.modal-confirm-delete').modal('hide');
        var deleteURL = $(event.currentTarget).data('section');
        Botble.showButtonLoading($(_this));
        $httpClient.make()["delete"](deleteURL).then(function (_ref) {
          var data = _ref.data;
          if (data.data) {
            languageTable.find("i[data-id=".concat(data.data, "]")).unwrap();
            $('.tooltip').remove();
          }
          languageTable.find("button[data-section=\"".concat(deleteURL, "\"]")).closest('tr').remove();
          Botble.showSuccess(data.message);
        })["finally"](function () {
          Botble.hideButtonLoading($(_this));
        });
      });
      languageTable.on('click', '.set-language-default', function (event) {
        event.preventDefault();
        var _self = $(event.currentTarget);
        $httpClient.make().get(_self.data('section')).then(function (_ref2) {
          var data = _ref2.data;
          var icon = languageTable.find('td > svg');
          icon.closest('td svg').removeClass('text-yellow');
          icon.replaceWith("<a href=\"javascript:void(0);\" data-section=\"".concat(route('languages.set.default'), "?lang_id=").concat(icon.data('id'), "\" class=\"set-language-default text-decoration-none\" data-bs-toggle=\"tooltip\" data-bs-original-title=\"Choose ").concat(icon.data('name'), " as default language\">").concat(icon.closest('td').html(), "</a>"));
          _self.find('svg').unwrap().addClass('text-yellow');
          $('.tooltip').remove();
          Botble.showSuccess(data.message);
        });
      });
      languageTable.on('click', '.edit-language-button', function (event) {
        event.preventDefault();
        var _self = $(event.currentTarget);
        $httpClient.make().get(_self.data('url')).then(function (_ref3) {
          var data = _ref3.data;
          var language = data.data;
          $('#lang_id').val(language.lang_id);
          $('#lang_name').val(language.lang_name);
          $('#lang_locale').val(language.lang_locale).trigger('change');
          $('#lang_code').val(language.lang_code).trigger('change');
          $('#flag_list').val(language.lang_flag).trigger('change');
          $("input[name=lang_rtl][value=\"".concat(language.lang_is_rtl ? 1 : 0, "\"]")).prop('checked', true);
          $('#lang_order').val(language.lang_order);
          $('#btn-language-submit').prop('id', 'btn-language-submit-edit').text($('#btn-language-submit-edit').data('update-language-text'));
        });
      });
      $(document).on('submit', 'form.language-settings-form', function (event) {
        event.preventDefault();
        var form = $(event.currentTarget);
        var button = form.find('button[type=submit]');
        Botble.showButtonLoading(button);
        $httpClient.make().postForm(form.prop('action'), new FormData(form[0])).then(function (_ref4) {
          var data = _ref4.data;
          Botble.showSuccess(data.message);
          form.removeClass('dirty');
        })["finally"](function () {
          Botble.hideButtonLoading(button);
        });
      });
    }
  }], [{
    key: "formatState",
    value: function formatState(state) {
      if (!state.id || state.element.value.toLowerCase().includes('...')) {
        return state.text;
      }
      return $("<div>\n                <span class=\"dropdown-item-indicator\">\n                    <img src=\"".concat($('#language_flag_path').val()).concat(state.element.value.toLowerCase(), ".svg\" class=\"flag\" style=\"height: 16px;\" alt=\"").concat(state.text, "\">\n                </span>\n                <span>").concat(state.text, "</span>\n            </div\n        "));
    }
  }, {
    key: "createOrUpdateLanguage",
    value: function createOrUpdateLanguage(id, name, locale, code, flag, order, isRTL, edit) {
      var button = arguments.length > 8 && arguments[8] !== undefined ? arguments[8] : null;
      var $buttonSubmit = $('#btn-language-submit');
      if (button) {
        $buttonSubmit = button;
      }
      var url = $buttonSubmit.data('store-url');
      if (edit) {
        url = $('#btn-language-submit-edit').data('update-url') + "?lang_code=".concat(code);
      }
      Botble.showButtonLoading($buttonSubmit, true);
      $httpClient.make().post(url, {
        lang_id: id.toString(),
        lang_name: name,
        lang_locale: locale,
        lang_code: code,
        lang_flag: flag,
        lang_order: order,
        lang_is_rtl: isRTL
      }).then(function (_ref5) {
        var data = _ref5.data;
        if (edit) {
          $('.table-language').find('tr[data-id=' + id + ']').replaceWith(data.data);
        } else {
          $('.table-language').append(data.data);
        }
        Botble.showSuccess(data.message);
      })["finally"](function () {
        $('#language_id').val('').trigger('change');
        $('#lang_name').val('');
        $('#lang_locale').val('').trigger('change');
        $('#lang_code').val('').trigger('change');
        $('input[name=lang_rtl][value="0"]').prop('checked', true);
        $('#flag_list').val('').trigger('change');
        $('#btn-language-submit-edit').prop('id', 'btn-language-submit').text($('#btn-language-submit').data('add-language-text'));
        Botble.hideButtonLoading($buttonSubmit);
      });
    }
  }]);
}();
$(function () {
  new LanguageManagement().init();
});
/******/ })()
;