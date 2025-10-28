/******/ (() => { // webpackBootstrap
/*!*************************************************!*\
  !*** ./platform/core/base/resources/js/tags.js ***!
  \*************************************************/
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _slicedToArray(r, e) { return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t["return"] && (u = t["return"](), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function _arrayWithHoles(r) { if (Array.isArray(r)) return r; }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
var TagsManager = /*#__PURE__*/function () {
  function TagsManager() {
    _classCallCheck(this, TagsManager);
  }
  return _createClass(TagsManager, [{
    key: "init",
    value: function init() {
      $(document).find('.tags').each(function (index, element) {
        if ($(element).hasClass('tagify')) {
          return;
        }
        var tagify = new Tagify(element, {
          keepInvalidTags: $(element).data('keep-invalid-tags') !== undefined ? $(element).data('keep-invalid-tags') : true,
          enforceWhitelist: $(element).data('enforce-whitelist') !== undefined ? $(element).data('enforce-whitelist') : false,
          delimiters: $(element).data('delimiters') !== undefined ? $(element).data('delimiters') : ',',
          whitelist: element.value ? element.value.trim().split(/\s*,\s*/) : [],
          userInput: $(element).data('user-input') !== undefined ? $(element).data('user-input') : true
        });
        if ($(element).data('url')) {
          tagify.on('input', function (e) {
            tagify.settings.whitelist.length = 0; // reset current whitelist
            tagify.loading(true).dropdown.hide.call(tagify); // show the loader animation

            $httpClient.make().get($(element).data('url')).then(function (_ref) {
              var data = _ref.data;
              tagify.settings.whitelist = data;
              tagify.loading(false).dropdown.show.call(tagify, e.detail.value);
            });
          });
        }
      });
      document.querySelectorAll('.list-tagify').forEach(function (element) {
        if (!element.dataset.list || $(element).hasClass('tagify')) {
          return;
        }
        var list = JSON.parse(element.dataset.list);
        var whiteList = [];
        for (var _i = 0, _Object$entries = Object.entries(list); _i < _Object$entries.length; _i++) {
          var _Object$entries$_i = _slicedToArray(_Object$entries[_i], 2),
            key = _Object$entries$_i[0],
            value = _Object$entries$_i[1];
          whiteList.push({
            value: key,
            name: value
          });
        }
        var listChosen = String(element.value).split(',');
        var arrayChosen = whiteList.filter(function (obj) {
          if (listChosen.includes(String(obj.value))) {
            return {
              value: obj.id,
              name: obj.name
            };
          }
        });
        var tagTemplate = function tagTemplate(tagData) {
          return "\n                <tag title=\"".concat(tagData.title || tagData.name, "\"\n                        contenteditable='false'\n                        spellcheck='false'\n                        tabIndex=\"-1\"\n                        class=\"").concat(this.settings.classNames.tag, " ").concat(tagData["class"] ? tagData["class"] : '', "\"\n                        ").concat(this.getAttributes(tagData), ">\n                    <x title='' class='tagify__tag__removeBtn' role='button' aria-label='remove tag'></x>\n                    <div class=\"d-flex align-items-center\">\n                        <span class='tagify__tag-text'>").concat(tagData.name, "</span>\n                    </div>\n                </tag>\n            ");
        };
        var suggestionTemplate = function suggestionTemplate(tagData) {
          return "\n                <div ".concat(this.getAttributes(tagData), "\n                    class=\"tagify__dropdown__item d-flex align-items-center ").concat(tagData["class"] ? tagData["class"] : '', "\"\n                    tabindex=\"0\"\n                    role=\"option\">\n\n                    <div class=\"d-flex flex-column\">\n                        <strong>").concat(tagData.name, "</strong>\n                    </div>\n                </div>\n            ");
        };
        var tagify = new Tagify(element, {
          tagTextProp: 'name',
          enforceWhitelist: true,
          skipInvalid: true,
          // do not temporarily add invalid tags
          dropdown: {
            closeOnSelect: false,
            enabled: 0,
            classname: 'users-list',
            searchKeys: ['value', 'name']
          },
          templates: {
            tag: tagTemplate,
            dropdownItem: suggestionTemplate
          },
          whitelist: whiteList,
          originalInputValueFormat: function originalInputValueFormat(valuesArr) {
            return valuesArr.map(function (item) {
              return item.value;
            }).join(',');
          }
        });
        tagify.loadOriginalValues(arrayChosen);
      });
    }
  }]);
}();
$(function () {
  new TagsManager().init();
});
/******/ })()
;