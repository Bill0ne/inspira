/******/ (() => { // webpackBootstrap
/*!*******************************************************************!*\
  !*** ./platform/plugins/language/resources/js/language-global.js ***!
  \*******************************************************************/
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
var LanguageGlobalManagement = /*#__PURE__*/function () {
  function LanguageGlobalManagement() {
    _classCallCheck(this, LanguageGlobalManagement);
  }
  return _createClass(LanguageGlobalManagement, [{
    key: "init",
    value: function init() {
      var languageChoiceSelect = $('#post_lang_choice');
      languageChoiceSelect.data('prev', languageChoiceSelect.val());
      $(document).on('change', '#post_lang_choice', function (event) {
        $('.change_to_language_text').text($(event.currentTarget).find('option:selected').text());
        $('#confirm-change-language-modal').modal('show');
      });
      $(document).on('click', '#confirm-change-language-modal .btn-warning.float-start', function (event) {
        event.preventDefault();
        languageChoiceSelect = $('#post_lang_choice');
        languageChoiceSelect.val(languageChoiceSelect.data('prev')).trigger('change');
        $('#confirm-change-language-modal').modal('hide');
      });
      $(document).on('click', '#confirm-change-language-button', function (event) {
        event.preventDefault();
        var _self = $(event.currentTarget);
        var flagPath = $('#language_flag_path').val();
        Botble.showButtonLoading(_self);
        languageChoiceSelect = $('#post_lang_choice');
        $httpClient.make().post($('div[data-change-language-route]').data('change-language-route'), {
          lang_meta_current_language: languageChoiceSelect.val(),
          reference_id: $('#reference_id').val(),
          reference_type: $('#reference_type').val(),
          lang_meta_created_from: $('#lang_meta_created_from').val()
        }).then(function (_ref) {
          var data = _ref.data;
          $('#select-post-language img').replaceWith("<img src=\"".concat(flagPath).concat(languageChoiceSelect.find('option:selected').data('flag'), ".svg\" class=\"flag\" style=\"height: 24px\" title=\"").concat(languageChoiceSelect.find('option:selected').text(), "\" alt=\"").concat(languageChoiceSelect.find('option:selected').text(), "\" />"));
          if (!data.error) {
            $('.current_language_text').text(languageChoiceSelect.find('option:selected').text());
            var html = '';
            $.each(data.data, function (index, el) {
              var flag = "<img src=\"".concat(flagPath).concat(el.lang_flag, ".svg\" class=\"flag\" style=\"height: 16px\" title=\"").concat(el.lang_name, "\" alt=\"").concat(el.lang_name, "\">");
              if (el.reference_id) {
                html += "<a href=\"".concat($('#route_edit').val(), "\" class=\"gap-2 d-flex align-items-center text-decoration-none\">").concat(flag, "\n                                        <span>\n                                            ").concat(el.lang_name, "\n                                            <svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\">\n                                                <path stroke=\"none\" d=\"M0 0h24v24H0z\" fill=\"none\"></path>\n                                                <path d=\"M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1\"></path>\n                                                <path d=\"M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z\"></path>\n                                                <path d=\"M16 5l3 3\"></path>\n                                            </svg>\n                                    </span>\n                                </a>");
              } else {
                html += "<a href=\"".concat($('#route_create').val(), "?ref_from=").concat($("#content_id").val(), "&ref_lang=").concat(index, "\" class=\"gap-2 d-flex align-items-center text-decoration-none\">").concat(flag, "\n                                    <span>\n                                        ").concat(el.lang_name, "\n                                        <svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\">\n                                            <path stroke=\"none\" d=\"M0 0h24v24H0z\" fill=\"none\"></path>\n                                            <path d=\"M12 5l0 14\"></path>\n                                            <path d=\"M5 12l14 0\"></path>\n                                        </svg>\n                                    </span>\n                                </a>");
              }
            });
            $('#list-others-language').html(html);
            $('#confirm-change-language-modal').modal('hide');
            languageChoiceSelect.data('prev', languageChoiceSelect.val()).trigger('change');
          }
        })["finally"](function () {
          return Botble.hideButtonLoading(_self);
        });
      });
      $(document).on('click', '.change-data-language-item', function (event) {
        event.preventDefault();
        window.location.href = $(event.currentTarget).find('span[data-href]').data('href');
      });
    }
  }]);
}();
$(function () {
  new LanguageGlobalManagement().init();
  $httpClient.setup(function (request) {
    request.axios.interceptors.request.use(function (config) {
      var refFrom = $('meta[name="ref_from"]').attr('content');
      var refLang = $('meta[name="ref_lang"]').attr('content');
      if (!refFrom && !refLang) {
        return config;
      }
      if (config.data instanceof FormData) {
        config.data.set('ref_from', refFrom);
        config.data.set('ref_lang', refLang);
      } else if (_typeof(config.data) === 'object') {
        config.data.ref_from = refFrom;
        config.data.ref_lang = refLang;
      }
      return config;
    });
  });
});
/******/ })()
;