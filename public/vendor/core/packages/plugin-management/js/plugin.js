/******/ (() => { // webpackBootstrap
/*!********************************************************************!*\
  !*** ./platform/packages/plugin-management/resources/js/plugin.js ***!
  \********************************************************************/
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _regenerator() { /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/babel/babel/blob/main/packages/babel-helpers/LICENSE */ var e, t, r = "function" == typeof Symbol ? Symbol : {}, n = r.iterator || "@@iterator", o = r.toStringTag || "@@toStringTag"; function i(r, n, o, i) { var c = n && n.prototype instanceof Generator ? n : Generator, u = Object.create(c.prototype); return _regeneratorDefine2(u, "_invoke", function (r, n, o) { var i, c, u, f = 0, p = o || [], y = !1, G = { p: 0, n: 0, v: e, a: d, f: d.bind(e, 4), d: function d(t, r) { return i = t, c = 0, u = e, G.n = r, a; } }; function d(r, n) { for (c = r, u = n, t = 0; !y && f && !o && t < p.length; t++) { var o, i = p[t], d = G.p, l = i[2]; r > 3 ? (o = l === n) && (u = i[(c = i[4]) ? 5 : (c = 3, 3)], i[4] = i[5] = e) : i[0] <= d && ((o = r < 2 && d < i[1]) ? (c = 0, G.v = n, G.n = i[1]) : d < l && (o = r < 3 || i[0] > n || n > l) && (i[4] = r, i[5] = n, G.n = l, c = 0)); } if (o || r > 1) return a; throw y = !0, n; } return function (o, p, l) { if (f > 1) throw TypeError("Generator is already running"); for (y && 1 === p && d(p, l), c = p, u = l; (t = c < 2 ? e : u) || !y;) { i || (c ? c < 3 ? (c > 1 && (G.n = -1), d(c, u)) : G.n = u : G.v = u); try { if (f = 2, i) { if (c || (o = "next"), t = i[o]) { if (!(t = t.call(i, u))) throw TypeError("iterator result is not an object"); if (!t.done) return t; u = t.value, c < 2 && (c = 0); } else 1 === c && (t = i["return"]) && t.call(i), c < 2 && (u = TypeError("The iterator does not provide a '" + o + "' method"), c = 1); i = e; } else if ((t = (y = G.n < 0) ? u : r.call(n, G)) !== a) break; } catch (t) { i = e, c = 1, u = t; } finally { f = 1; } } return { value: t, done: y }; }; }(r, o, i), !0), u; } var a = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} t = Object.getPrototypeOf; var c = [][n] ? t(t([][n]())) : (_regeneratorDefine2(t = {}, n, function () { return this; }), t), u = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(c); function f(e) { return Object.setPrototypeOf ? Object.setPrototypeOf(e, GeneratorFunctionPrototype) : (e.__proto__ = GeneratorFunctionPrototype, _regeneratorDefine2(e, o, "GeneratorFunction")), e.prototype = Object.create(u), e; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, _regeneratorDefine2(u, "constructor", GeneratorFunctionPrototype), _regeneratorDefine2(GeneratorFunctionPrototype, "constructor", GeneratorFunction), GeneratorFunction.displayName = "GeneratorFunction", _regeneratorDefine2(GeneratorFunctionPrototype, o, "GeneratorFunction"), _regeneratorDefine2(u), _regeneratorDefine2(u, o, "Generator"), _regeneratorDefine2(u, n, function () { return this; }), _regeneratorDefine2(u, "toString", function () { return "[object Generator]"; }), (_regenerator = function _regenerator() { return { w: i, m: f }; })(); }
function _regeneratorDefine2(e, r, n, t) { var i = Object.defineProperty; try { i({}, "", {}); } catch (e) { i = 0; } _regeneratorDefine2 = function _regeneratorDefine(e, r, n, t) { function o(r, n) { _regeneratorDefine2(e, r, function (e) { return this._invoke(r, n, e); }); } r ? i ? i(e, r, { value: n, enumerable: !t, configurable: !t, writable: !t }) : e[r] = n : (o("next", 0), o("throw", 1), o("return", 2)); }, _regeneratorDefine2(e, r, n, t); }
function asyncGeneratorStep(n, t, e, r, o, a, c) { try { var i = n[a](c), u = i.value; } catch (n) { return void e(n); } i.done ? t(u) : Promise.resolve(u).then(r, o); }
function _asyncToGenerator(n) { return function () { var t = this, e = arguments; return new Promise(function (r, o) { var a = n.apply(t, e); function _next(n) { asyncGeneratorStep(a, r, o, _next, _throw, "next", n); } function _throw(n) { asyncGeneratorStep(a, r, o, _next, _throw, "throw", n); } _next(void 0); }); }; }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
var PluginManagement = /*#__PURE__*/function () {
  function PluginManagement() {
    _classCallCheck(this, PluginManagement);
  }
  return _createClass(PluginManagement, [{
    key: "init",
    value: function init() {
      var _this = this;
      $(document).on('click', '.btn-trigger-remove-plugin', function (event) {
        event.preventDefault();
        $('#confirm-remove-plugin-button').data('url', $(event.currentTarget).data('url'));
        $('#remove-plugin-modal').modal('show');
      });
      $(document).on('click', '#confirm-remove-plugin-button', function (event) {
        event.preventDefault();
        var _self = $(event.currentTarget);
        $httpClient.make().withButtonLoading(_self)["delete"](_self.data('url')).then(function (_ref) {
          var data = _ref.data;
          Botble.showSuccess(data.message);
          window.location.reload();
        })["finally"](function () {
          return $('#remove-plugin-modal').modal('hide');
        });
      });
      $(document).on('click', '.btn-trigger-update-plugin', function (event) {
        event.preventDefault();
        var currentTarget = $(event.currentTarget);
        var url = currentTarget.data('update-url');
        currentTarget.prop('disabled', true);
        $httpClient.make().withButtonLoading(currentTarget).post(url).then(function (_ref2) {
          var data = _ref2.data;
          Botble.showSuccess(data.message);
          localStorage.removeItem('plugin_update_check_time');
          localStorage.removeItem('plugin_update_data');
          setTimeout(function () {
            return window.location.reload();
          }, 2000);
        })["finally"](function () {
          return currentTarget.prop('disabled', false);
        });
      });
      $(document).on('click', '.btn-trigger-change-status', /*#__PURE__*/function () {
        var _ref3 = _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee(event) {
          var _self, pluginName, changeStatusUrl;
          return _regenerator().w(function (_context) {
            while (1) switch (_context.n) {
              case 0:
                event.preventDefault();
                _self = $(event.currentTarget);
                pluginName = _self.data('plugin');
                changeStatusUrl = _self.data('change-status-url');
                if (!(_self.data('status') === 1)) {
                  _context.n = 2;
                  break;
                }
                Botble.showButtonLoading(_self);
                _context.n = 1;
                return _this.activateOrDeactivatePlugin(changeStatusUrl);
              case 1:
                Botble.hideButtonLoading(_self);
                return _context.a(2);
              case 2:
                $httpClient.makeWithoutErrorHandler().withButtonLoading(_self).post(_self.data('check-requirement-url')).then(function () {
                  return _this.activateOrDeactivatePlugin(changeStatusUrl);
                })["catch"](function (e) {
                  var _e$response$data = e.response.data,
                    data = _e$response$data.data,
                    message = _e$response$data.message;
                  if (data && data.existing_plugins_on_marketplace) {
                    var $modal = $('#confirm-install-plugin-modal');
                    $modal.find('.modal-body #requirement-message').html(message);
                    $modal.find('input[name="plugin_name"]').val(pluginName);
                    $modal.find('input[name="ids"]').val(data.existing_plugins_on_marketplace);
                    $modal.modal('show');
                    return;
                  }
                  Botble.showError(message);
                });
              case 3:
                return _context.a(2);
            }
          }, _callee);
        }));
        return function (_x) {
          return _ref3.apply(this, arguments);
        };
      }());
      if ($('button[data-check-update]').length) {
        this.checkUpdate();
      }
      this.handleFilters();
    }
  }, {
    key: "handleFilters",
    value: function handleFilters() {
      var search = $('[data-bb-toggle="change-search"]').val().toLowerCase();
      var status = $('[data-bb-toggle="change-filter-plugin-status"]:checked').val();
      $('button[data-bb-toggle="change-filter-plugin-status"]').each(function (index, element) {
        var status = $(element).data('value') || $(element).val();
        var $visiblePluginItems = status === 'all' ? $('.plugin-item:visible') : $(".plugin-item[data-status=\"".concat(status, "\"]:visible"));
        $("[data-bb-toggle=\"plugins-count\"][data-status=\"".concat(status, "\"]")).text($visiblePluginItems.length);
      });
      var applyFilters = function applyFilters() {
        var $pluginItems = $('.plugin-item');
        $pluginItems.each(function (index, element) {
          var $element = $(element);
          var name = $element.data('name').toLowerCase();
          var description = $element.data('description').toLowerCase();
          var author = $element.data('author').toLowerCase();
          var nameMatch = name.includes(search);
          var authorMatch = author.includes(search);
          var descriptionMatch = description.includes(search);
          var statusMatch = status === 'all' || $element.data('status') === status || status === 'updates-available' && $element.data('available-for-updates');
          if ((nameMatch || descriptionMatch || authorMatch) && statusMatch) {
            $element.show();
          } else {
            $element.hide();
          }
        });
        var $visiblePluginItems = $('.plugin-item:visible');
        if ($visiblePluginItems.length === 0) {
          $('.empty').show();
        } else {
          $('.empty').hide();
        }
      };
      $(document).on('keyup', '[data-bb-toggle="change-search"]', function (event) {
        event.preventDefault();
        search = $(event.currentTarget).val().toLowerCase();
        applyFilters();
      });
      $(document).on('change', 'input[data-bb-toggle="change-filter-plugin-status"]', function (event) {
        status = $(event.currentTarget).val();
        applyFilters();
      });
      $(document).on('click', 'button[data-bb-toggle="change-filter-plugin-status"]', function (event) {
        var newValue = $(event.target).data('value');
        $('[data-bb-toggle="status-filter-label"]').text($(event.target).text());
        $('.dropdown-item').removeClass('active');
        $(event.target).addClass('active');
        status = newValue;
        applyFilters();
      });
    }
  }, {
    key: "checkUpdate",
    value: function checkUpdate() {
      var _this2 = this;
      // Check if we should make the update check request
      var shouldCheckUpdate = function shouldCheckUpdate() {
        var lastCheckTime = localStorage.getItem('plugin_update_check_time');
        if (!lastCheckTime) {
          return true;
        }

        // Call once every 15 minutes (900000 ms)
        var fifteenMinutesInMs = 15 * 60 * 1000;
        return Date.now() - parseInt(lastCheckTime) > fifteenMinutesInMs;
      };

      // Try to get cached update data
      var cachedUpdateData = localStorage.getItem('plugin_update_data');
      if (cachedUpdateData && !shouldCheckUpdate()) {
        try {
          var data = JSON.parse(cachedUpdateData);
          this.processUpdateData(data);
          return;
        } catch (e) {
          // If there's an error parsing the cached data, proceed with the request
        }
      }
      if (!shouldCheckUpdate()) {
        return;
      }
      $httpClient.make().post($('button[data-check-update]').data('check-update-url')).then(function (_ref4) {
        var data = _ref4.data;
        // Store the current time as the last check time
        localStorage.setItem('plugin_update_check_time', Date.now().toString());
        if (!data.data) {
          localStorage.removeItem('plugin_update_data');
          return;
        }

        // Store the update data
        localStorage.setItem('plugin_update_data', JSON.stringify(data.data));
        _this2.processUpdateData(data.data);
      })["catch"](function () {
        // Even on error, we've made the request, so store the time
        localStorage.setItem('plugin_update_check_time', Date.now().toString());
      });
    }
  }, {
    key: "processUpdateData",
    value: function processUpdateData(data) {
      if (!data) {
        return;
      }
      Object.keys(data).forEach(function (key) {
        var plugin = data[key];
        var $button = $("button[data-check-update=\"".concat(plugin.name, "\"]"));
        var url = $button.data('update-url').replace('__id__', plugin.id);
        $button.data('update-url', url).show();
        var $parent = $button.closest('.plugin-item');
        $parent.data('available-for-updates', true).trigger('change');
        $('[data-bb-toggle="plugins-count"][data-status="updates-available"]').text(Object.keys(data).length);
      });
    }
  }, {
    key: "activateOrDeactivatePlugin",
    value: function () {
      var _activateOrDeactivatePlugin = _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee2(url) {
        var reload,
          _args2 = arguments;
        return _regenerator().w(function (_context2) {
          while (1) switch (_context2.n) {
            case 0:
              reload = _args2.length > 1 && _args2[1] !== undefined ? _args2[1] : true;
              return _context2.a(2, $httpClient.make().put(url).then(function (_ref5) {
                var data = _ref5.data;
                Botble.showSuccess(data.message);
                if (reload) {
                  window.location.reload();
                }
              }));
          }
        }, _callee2);
      }));
      function activateOrDeactivatePlugin(_x2) {
        return _activateOrDeactivatePlugin.apply(this, arguments);
      }
      return activateOrDeactivatePlugin;
    }()
  }]);
}();
$(function () {
  new PluginManagement().init();
});
/******/ })()
;