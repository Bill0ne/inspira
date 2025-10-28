/******/ (() => { // webpackBootstrap
/*!****************************************************************************!*\
  !*** ./platform/plugins/simple-slider/resources/js/simple-slider-admin.js ***!
  \****************************************************************************/
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
var SimpleSliderAdminManagement = /*#__PURE__*/function () {
  function SimpleSliderAdminManagement() {
    _classCallCheck(this, SimpleSliderAdminManagement);
  }
  return _createClass(SimpleSliderAdminManagement, [{
    key: "init",
    value: function init(tableId) {
      var $table = $(document).find("#".concat(tableId, "_wrapper"));
      $.each($table.find('tbody'), function (index, el) {
        Sortable.create(el, {
          group: el + '_' + index,
          // or { name: "...", pull: [true, false, clone], put: [true, false, array] }
          sort: true,
          // sorting inside list
          delay: 0,
          // time in milliseconds to define when the sorting should start
          disabled: false,
          // Disables the sortable if set to true.
          store: null,
          // @see Store
          animation: 150,
          // ms, animation speed moving items when sorting, `0` — without animation
          handle: 'tr',
          ghostClass: 'sortable-ghost',
          // Class name for the drop placeholder
          chosenClass: 'sortable-chosen',
          // Class name for the chosen item
          dataIdAttr: 'data-id',
          forceFallback: false,
          // ignore the HTML5 DnD behaviour and force the fallback to kick in
          fallbackClass: 'sortable-fallback',
          // Class name for the cloned DOM Element when using forceFallback
          fallbackOnBody: false,
          // Appends the cloned DOM Element into the Document's Body

          scroll: true,
          // or HTMLElement
          scrollSensitivity: 30,
          // px, how near the mouse must be to an edge to start scrolling.
          scrollSpeed: 10,
          // px

          // dragging ended
          onEnd: function onEnd() {
            $(document).find('.btn-save-sort-order').addClass('sort-button-active btn-warning').show();
            var $box = $(el).closest('.card');
            $.each($box.find('tbody tr'), function (index, sort) {
              $(sort).find('.order-column').text(index + 1);
            });
          }
        });
      });
      var $sortButton = $(document).find('.btn-save-sort-order');
      $sortButton.off('click').on('click', function (event) {
        event.preventDefault();
        var _self = $(event.currentTarget);
        var items = [];
        $.each(_self.closest('.card').find('tbody tr'), function (index, sort) {
          items.push(parseInt($(sort).find('td:first-child').text()));
          $(sort).find('.order-column').text(index + 1);
        });
        Botble.showButtonLoading(_self);
        $httpClient.make().post($sortButton.data('url'), {
          items: items
        }).then(function (_ref) {
          var data = _ref.data;
          Botble.showSuccess(data.message);
        })["finally"](function () {
          Botble.hideButtonLoading(_self);
          _self.hide();
        });
      });
    }
  }]);
}();
$(function () {
  document.addEventListener('core-table-init-completed', function (event) {
    new SimpleSliderAdminManagement().init(event.detail.table.prop('id'));
  });
  $(document).on('show.bs.modal', '#simple-slider-item-modal', function (e) {
    var modal = $(e.currentTarget);
    var href = $(e.relatedTarget).prop('href');
    $httpClient.make().withLoading(modal.find('.modal-content')).get(href).then(function (_ref2) {
      var data = _ref2.data;
      modal.find('.modal-header .modal-title').text(data.data.title);
      modal.find('.modal-body').html(data.data.content);
      Botble.initMediaIntegrate();
      Botble.initResources();
    });
  }).on('click', '#simple-slider-item-modal button[type="submit"]', function (e) {
    e.preventDefault();
    var button = $(e.currentTarget);
    var modal = button.closest('.modal');
    var form = modal.find('form');
    $httpClient.make().withLoading(form).withButtonLoading(button).post(form.prop('action'), form.serialize()).then(function (_ref3) {
      var data = _ref3.data;
      Botble.showSuccess(data.message);
      modal.modal('hide');
      $('#botble-simple-slider-tables-simple-slider-item-table').DataTable().draw();
    });
  });
});
/******/ })()
;