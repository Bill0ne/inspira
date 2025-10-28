/******/ (() => { // webpackBootstrap
/*!*********************************************************!*\
  !*** ./platform/packages/widget/resources/js/widget.js ***!
  \*********************************************************/
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
var WidgetManagement = /*#__PURE__*/function () {
  function WidgetManagement() {
    _classCallCheck(this, WidgetManagement);
  }
  return _createClass(WidgetManagement, [{
    key: "init",
    value: function init() {
      var listWidgets = [{
        name: 'wrap-widgets',
        pull: 'clone',
        put: false
      }];
      $.each($('.sidebar-item'), function () {
        listWidgets.push({
          name: 'wrap-widgets',
          pull: true,
          put: true
        });
      });
      var saveWidget = function saveWidget(parentElement) {
        if (parentElement.length > 0) {
          var items = [];
          $.each(parentElement.find('li[data-id]'), function (index, widget) {
            items.push($(widget).find('form').serialize());
          });
          $httpClient.make().post(BWidget.routes.save_widgets_sidebar, {
            items: items,
            sidebar_id: parentElement.data('id')
          }).then(function (_ref) {
            var data = _ref.data;
            parentElement.find('ul').html(data.data);
            Botble.callScroll($('.list-page-select-widget'));
            Botble.initResources();
            Botble.initMediaIntegrate();
            Botble.initTreeCheckboxes();
            Botble.showSuccess(data.message);
          })["finally"](function () {
            parentElement.find('.widget-save i').remove();
          });
        }
      };
      listWidgets.forEach(function (groupOpts, i) {
        Sortable.create(document.getElementById('wrap-widget-' + (i + 1)), {
          sort: i !== 0,
          group: groupOpts,
          delay: 0,
          // time in milliseconds to define when the sorting should start
          disabled: false,
          // Disables the sortable if set to true.
          store: null,
          // @see Store
          animation: 150,
          // ms, animation speed moving items when sorting, `0` — without animation
          handle: '.card-header',
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

          // Changed sorting within list
          onUpdate: function onUpdate(evt) {
            if (evt.from !== evt.to) {
              saveWidget($(evt.from).closest('.sidebar-item'));
            }
            saveWidget($(evt.item).closest('.sidebar-item'));
          },
          onAdd: function onAdd(evt) {
            if (evt.from !== evt.to) {
              saveWidget($(evt.from).closest('.sidebar-item'));
            }
            saveWidget($(evt.item).closest('.sidebar-item'));
          }
        });
      });
      $('#wrap-widgets').on('click', '.widget-control-delete', function (event) {
        event.preventDefault();
        var _self = $(event.currentTarget);
        var widget = _self.closest('li');
        Botble.showButtonLoading(_self);
        $httpClient.make()["delete"](BWidget.routes["delete"], {
          widget_id: widget.data('id'),
          position: widget.data('position'),
          sidebar_id: _self.closest('.sidebar-item').data('id')
        }).then(function (_ref2) {
          var data = _ref2.data;
          Botble.showSuccess(data.message);
          _self.closest('.sidebar-item').find('ul').html(data.data);
        })["finally"](function () {
          Botble.showButtonLoading(widget.find('.widget-control-delete'));
        });
      }).on('click', '.widget-item .card-header', function (event) {
        var _self = $(event.currentTarget);
        _self.closest('.widget-item').find('.widget-content').slideToggle(300);
        if (_self.find('.ti').hasClass('ti-chevron-up')) {
          setTimeout(function () {
            _self.closest('.card').toggleClass('card-no-border-bottom-radius');
          }, 300);
        } else {
          _self.closest('.card').toggleClass('card-no-border-bottom-radius');
        }
        _self.find('.ti').toggleClass('ti-chevron-down').toggleClass('ti-chevron-up');
      }).on('click', '.sidebar-item .card-header .button-sidebar.btn-action', function (event) {
        var _self = $(event.currentTarget);
        _self.closest('.card').find('.card-body').slideToggle(300);
        _self.find('.ti').toggleClass('ti-chevron-down').toggleClass('ti-chevron-up');
      }).on('click', '.widget-save', function (event) {
        event.preventDefault();
        var _self = $(event.currentTarget);
        Botble.showButtonLoading(_self);
        saveWidget(_self.closest('.sidebar-item'));
      });
    }
  }]);
}();
$(function () {
  new WidgetManagement().init();
});
/******/ })()
;