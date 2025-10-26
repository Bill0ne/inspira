/******/ (() => { // webpackBootstrap
/*!************************************************!*\
  !*** ./platform/core/acl/resources/js/role.js ***!
  \************************************************/
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
var Role = /*#__PURE__*/function () {
  function Role() {
    _classCallCheck(this, Role);
  }
  return _createClass(Role, [{
    key: "init",
    value: function init() {
      var $checkboxes = $('.has-children');
      if ($checkboxes.length) {
        $checkboxes.map(function (index, value) {
          $(value).treeview({
            collapsed: true,
            animated: "medium",
            control: "#sidetreecontrol",
            persist: "location"
          });
        });
      }
      $('#allTreeChecked:checkbox').on('click', function (event) {
        event.stopPropagation();
        var _self = $(event.currentTarget);
        var checked = _self.is(':checked');
        if ($('#checkboxes-permisstions').length) {
          var parent_uls = $('#checkboxes-permisstions').find(':checkbox').prop('checked', checked);
          parent_uls.each(function () {
            var parent_ul = $(this),
              parent_state = parent_ul.find(':checkbox').length == parent_ul.find(':checked').length;
            parent_ul.siblings(':checkbox').prop('checked', parent_state);
          });
        }
      });
      $('#checkboxes-permisstions :checkbox').on('click', function (event) {
        event.stopPropagation();
        var _self = $(event.currentTarget);
        var checked = _self.is(':checked'),
          parent_li = _self.closest('li'),
          parent_uls = parent_li.parents('ul');
        parent_li.find(':checkbox').prop('checked', checked);
        parent_uls.each(function () {
          var parent_ul = $(this),
            parent_state = parent_ul.find(':checkbox').length == parent_ul.find(':checked').length;
          parent_ul.siblings(':checkbox').prop('checked', parent_state);
        });
      });
    }
  }]);
}();
$(function () {
  new Role().init();
});
/******/ })()
;