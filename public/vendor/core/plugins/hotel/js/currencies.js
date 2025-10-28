/******/ (() => { // webpackBootstrap
/*!******************************************************************!*\
  !*** ./platform/plugins/hotel/resources/assets/js/currencies.js ***!
  \******************************************************************/
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
var Currencies = /*#__PURE__*/function () {
  function Currencies() {
    _classCallCheck(this, Currencies);
    this.template = $('#currency_template').html();
    this.totalItem = 0;
    this.deletedItems = [];
    this.initData();
    this.handleForm();
  }
  return _createClass(Currencies, [{
    key: "initData",
    value: function initData() {
      var _self = this;
      var data = $.parseJSON($('#currencies').html());
      $.each(data, function (index, item) {
        var template = _self.template.replace(/__id__/gi, item.id).replace(/__position__/gi, item.order).replace(/__isPrefixSymbolChecked__/gi, item.is_prefix_symbol == 1 ? 'selected' : '').replace(/__notIsPrefixSymbolChecked__/gi, item.is_prefix_symbol == 0 ? 'selected' : '').replace(/__isDefaultChecked__/gi, item.is_default == 1 ? 'checked' : '').replace(/__title__/gi, item.title).replace(/__decimals__/gi, item.decimals).replace(/__exchangeRate__/gi, item.exchange_rate).replace(/__symbol__/gi, item.symbol);
        $('.swatches-container .swatches-list').append(template);
        _self.totalItem++;
      });
    }
  }, {
    key: "addNewAttribute",
    value: function addNewAttribute() {
      var _self = this;
      var template = _self.template.replace(/__id__/gi, 0).replace(/__position__/gi, _self.totalItem).replace(/__isPrefixSymbolChecked__/gi, '').replace(/__notIsPrefixSymbolChecked__/gi, '').replace(/__isDefaultChecked__/gi, _self.totalItem == 0 ? 'checked' : '').replace(/__title__/gi, '').replace(/__decimals__/gi, 0).replace(/__exchangeRate__/gi, 1).replace(/__symbol__/gi, '');
      $('.swatches-container .swatches-list').append(template);
      _self.totalItem++;
    }
  }, {
    key: "exportData",
    value: function exportData() {
      var data = [];
      $('.swatches-container .swatches-list li').each(function (index, item) {
        var $current = $(item);
        data.push({
          id: $current.data('id'),
          is_default: $current.find('[data-type=is_default] input[type=radio]').is(':checked') ? 1 : 0,
          order: $current.index(),
          title: $current.find('[data-type=title] input').val(),
          symbol: $current.find('[data-type=symbol] input').val(),
          decimals: $current.find('[data-type=decimals] input').val(),
          exchange_rate: $current.find('[data-type=exchange_rate] input').val(),
          is_prefix_symbol: $current.find('[data-type=is_prefix_symbol] select').val()
        });
      });
      return data;
    }
  }, {
    key: "handleForm",
    value: function handleForm() {
      var _self = this;
      $('.swatches-container .swatches-list').sortable();
      $('body').on('submit', '.main-setting-form', function () {
        var data = _self.exportData();
        $('#currencies').val(JSON.stringify(data));
        $('#deleted_currencies').val(JSON.stringify(_self.deletedItems));
      }).on('click', '.js-add-new-attribute', function (event) {
        event.preventDefault();
        _self.addNewAttribute();
      }).on('click', '.swatches-container .swatches-list li .remove-item a', function (event) {
        event.preventDefault();
        var $item = $(event.currentTarget).closest('li');
        _self.deletedItems.push($item.data('id'));
        $item.remove();
      });
    }
  }]);
}();
$(window).on('load', function () {
  new Currencies();
});
/******/ })()
;