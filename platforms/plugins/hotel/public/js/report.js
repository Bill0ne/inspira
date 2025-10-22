/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/plugins/hotel/resources/assets/js/components/RevenueChart.vue?vue&type=script&lang=js":
/*!************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/plugins/hotel/resources/assets/js/components/RevenueChart.vue?vue&type=script&lang=js ***!
  \************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
function _regenerator() { /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/babel/babel/blob/main/packages/babel-helpers/LICENSE */ var e, t, r = "function" == typeof Symbol ? Symbol : {}, n = r.iterator || "@@iterator", o = r.toStringTag || "@@toStringTag"; function i(r, n, o, i) { var c = n && n.prototype instanceof Generator ? n : Generator, u = Object.create(c.prototype); return _regeneratorDefine2(u, "_invoke", function (r, n, o) { var i, c, u, f = 0, p = o || [], y = !1, G = { p: 0, n: 0, v: e, a: d, f: d.bind(e, 4), d: function d(t, r) { return i = t, c = 0, u = e, G.n = r, a; } }; function d(r, n) { for (c = r, u = n, t = 0; !y && f && !o && t < p.length; t++) { var o, i = p[t], d = G.p, l = i[2]; r > 3 ? (o = l === n) && (u = i[(c = i[4]) ? 5 : (c = 3, 3)], i[4] = i[5] = e) : i[0] <= d && ((o = r < 2 && d < i[1]) ? (c = 0, G.v = n, G.n = i[1]) : d < l && (o = r < 3 || i[0] > n || n > l) && (i[4] = r, i[5] = n, G.n = l, c = 0)); } if (o || r > 1) return a; throw y = !0, n; } return function (o, p, l) { if (f > 1) throw TypeError("Generator is already running"); for (y && 1 === p && d(p, l), c = p, u = l; (t = c < 2 ? e : u) || !y;) { i || (c ? c < 3 ? (c > 1 && (G.n = -1), d(c, u)) : G.n = u : G.v = u); try { if (f = 2, i) { if (c || (o = "next"), t = i[o]) { if (!(t = t.call(i, u))) throw TypeError("iterator result is not an object"); if (!t.done) return t; u = t.value, c < 2 && (c = 0); } else 1 === c && (t = i["return"]) && t.call(i), c < 2 && (u = TypeError("The iterator does not provide a '" + o + "' method"), c = 1); i = e; } else if ((t = (y = G.n < 0) ? u : r.call(n, G)) !== a) break; } catch (t) { i = e, c = 1, u = t; } finally { f = 1; } } return { value: t, done: y }; }; }(r, o, i), !0), u; } var a = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} t = Object.getPrototypeOf; var c = [][n] ? t(t([][n]())) : (_regeneratorDefine2(t = {}, n, function () { return this; }), t), u = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(c); function f(e) { return Object.setPrototypeOf ? Object.setPrototypeOf(e, GeneratorFunctionPrototype) : (e.__proto__ = GeneratorFunctionPrototype, _regeneratorDefine2(e, o, "GeneratorFunction")), e.prototype = Object.create(u), e; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, _regeneratorDefine2(u, "constructor", GeneratorFunctionPrototype), _regeneratorDefine2(GeneratorFunctionPrototype, "constructor", GeneratorFunction), GeneratorFunction.displayName = "GeneratorFunction", _regeneratorDefine2(GeneratorFunctionPrototype, o, "GeneratorFunction"), _regeneratorDefine2(u), _regeneratorDefine2(u, o, "Generator"), _regeneratorDefine2(u, n, function () { return this; }), _regeneratorDefine2(u, "toString", function () { return "[object Generator]"; }), (_regenerator = function _regenerator() { return { w: i, m: f }; })(); }
function _regeneratorDefine2(e, r, n, t) { var i = Object.defineProperty; try { i({}, "", {}); } catch (e) { i = 0; } _regeneratorDefine2 = function _regeneratorDefine(e, r, n, t) { function o(r, n) { _regeneratorDefine2(e, r, function (e) { return this._invoke(r, n, e); }); } r ? i ? i(e, r, { value: n, enumerable: !t, configurable: !t, writable: !t }) : e[r] = n : (o("next", 0), o("throw", 1), o("return", 2)); }, _regeneratorDefine2(e, r, n, t); }
function asyncGeneratorStep(n, t, e, r, o, a, c) { try { var i = n[a](c), u = i.value; } catch (n) { return void e(n); } i.done ? t(u) : Promise.resolve(u).then(r, o); }
function _asyncToGenerator(n) { return function () { var t = this, e = arguments; return new Promise(function (r, o) { var a = n.apply(t, e); function _next(n) { asyncGeneratorStep(a, r, o, _next, _throw, "next", n); } function _throw(n) { asyncGeneratorStep(a, r, o, _next, _throw, "throw", n); } _next(void 0); }); }; }
var _Vue = Vue,
  nextTick = _Vue.nextTick;
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  props: {
    data: {
      type: Array,
      "default": function _default() {
        return [];
      },
      required: true
    }
  },
  data: function data() {
    return {
      chartData: this.data,
      chartInstance: null
    };
  },
  mounted: function mounted() {
    var _this = this;
    this.renderChart();
    $event.on('revenue-chart:reload', function (data) {
      _this.chartData = data;
      _this.renderChart();
    });
  },
  methods: {
    renderChart: function renderChart() {
      var _this2 = this;
      return _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee() {
        var series, colors, labels, total;
        return _regenerator().w(function (_context) {
          while (1) switch (_context.n) {
            case 0:
              if (_this2.chartData.length) {
                _context.n = 1;
                break;
              }
              return _context.a(2);
            case 1:
              series = [];
              colors = [];
              labels = [];
              total = 0;
              _this2.chartData.map(function (x) {
                total += parseFloat(x.value);
                labels.push(x.label);
                colors.push(x.color);
              });
              if (total === 0) {
                _this2.chartData.map(function () {
                  series.push(0);
                });
              } else {
                _this2.chartData.map(function (x) {
                  series.push(100 / total * parseFloat(x.value));
                });
              }
              if (_this2.chartInstance === null) {
                _this2.chartInstance = new ApexCharts(_this2.$refs.chartRef, {
                  series: series,
                  colors: colors,
                  chart: {
                    height: '250',
                    type: 'donut'
                  },
                  chartOptions: {
                    labels: labels
                  },
                  plotOptions: {
                    pie: {
                      donut: {
                        size: '71%',
                        polygons: {
                          strokeWidth: 0
                        }
                      },
                      expandOnClick: true
                    }
                  },
                  states: {
                    hover: {
                      filter: {
                        type: 'darken',
                        value: 0.9
                      }
                    }
                  },
                  dataLabels: {
                    enabled: false
                  },
                  legend: {
                    show: false
                  },
                  tooltip: {
                    enabled: false
                  }
                });
                _this2.chartInstance.render();
              } else {
                _this2.chartInstance.updateOptions({
                  series: series,
                  colors: colors,
                  chartOptions: {
                    labels: labels
                  }
                });
              }
              if (jQuery && jQuery().tooltip) {
                $('[data-bs-toggle="tooltip"]').tooltip({
                  placement: 'top',
                  boundary: 'window'
                });
              }
            case 2:
              return _context.a(2);
          }
        }, _callee);
      }))();
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/plugins/hotel/resources/assets/js/components/SalesReportsChart.vue?vue&type=script&lang=js":
/*!*****************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/plugins/hotel/resources/assets/js/components/SalesReportsChart.vue?vue&type=script&lang=js ***!
  \*****************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  props: {
    url: {
      type: String,
      "default": null,
      required: true
    },
    date_from: {
      type: String,
      "default": null,
      required: true
    },
    date_to: {
      type: String,
      "default": null,
      required: true
    },
    format: {
      type: String,
      "default": 'dd/MM/yy',
      required: false
    },
    filters: {
      type: Array,
      "default": function _default() {
        return [];
      },
      required: false
    },
    filterDefault: {
      type: String,
      "default": '',
      required: false
    }
  },
  data: function data() {
    return {
      isLoading: true,
      earningSales: [],
      colors: ['#fcb800', '#80bc00'],
      chart: null,
      filtering: '',
      chartFromDate: null,
      chartToDate: null
    };
  },
  mounted: function mounted() {
    var _this = this;
    this.setFiltering();
    this.chartFromDate = this.date_from;
    this.chartToDate = this.date_to;
    this.renderChart();
    $event.on('sales-report-chart:reload', function (data) {
      _this.chartFromDate = data.date_from;
      _this.chartToDate = data.date_to;
      _this.renderChart();
    });
  },
  methods: {
    setFiltering: function setFiltering() {
      var f = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
      if (!f) {
        f = this.filterDefault;
      }
      if (this.filters.length) {
        var filter = this.filters.find(function (x) {
          return x.key == f;
        });
        if (filter) {
          this.filtering = filter.text;
        } else {
          this.filtering = f;
        }
      }
    },
    renderChart: function renderChart() {
      var _this2 = this;
      if (this.url) {
        axios.get(this.url + '?date_from=' + this.chartFromDate + '&date_to=' + this.chartToDate).then(function (res) {
          if (res.data.error) {
            Botble.showError(res.data.message);
          } else {
            _this2.earningSales = res.data.data.earningSales;
            var series = res.data.data.series;
            var colors = res.data.data.colors;
            var categories = res.data.data.dates;
            if (_this2.chart === null) {
              _this2.chart = new ApexCharts(_this2.$el.querySelector('.sales-reports-chart'), {
                series: series,
                chart: {
                  height: 350,
                  type: 'area',
                  toolbar: {
                    show: false
                  }
                },
                dataLabels: {
                  enabled: false
                },
                stroke: {
                  curve: 'smooth'
                },
                colors: colors,
                xaxis: {
                  type: 'datetime',
                  categories: categories
                },
                tooltip: {
                  x: {
                    format: _this2.format
                  }
                },
                noData: {
                  text: BotbleVariables.languages.tables.no_data
                }
              });
              _this2.chart.render();
            } else {
              _this2.chart.updateOptions({
                series: series,
                colors: colors,
                xaxis: {
                  type: 'datetime',
                  categories: categories
                }
              });
            }
          }
        });
      }
    },
    clickFilter: function clickFilter(filter, event) {
      var _this3 = this;
      event.preventDefault();
      this.setFiltering('...');
      var that = this;
      axios.get(that.url + '?date_from=' + this.chartFromDate + '&date_to=' + this.chartToDate, {
        params: {
          filter: filter
        }
      }).then(function (res) {
        if (res.data.error) {
          Botble.showError(res.data.message);
        } else {
          that.earningSales = res.data.data.earningSales;
          var options = {
            xaxis: {
              type: 'datetime',
              categories: res.data.data.dates
            },
            series: res.data.data.series
          };
          if (res.data.data.colors) {
            options.colors = res.data.data.colors;
          }
          _this3.chart.updateOptions(options);
        }
        _this3.setFiltering(filter);
      });
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/plugins/hotel/resources/assets/js/components/RevenueChart.vue?vue&type=template&id=25593fdf":
/*!****************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/plugins/hotel/resources/assets/js/components/RevenueChart.vue?vue&type=template&id=25593fdf ***!
  \****************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "vue");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);

var _hoisted_1 = {
  ref: "chartRef",
  "class": "revenue-chart"
};
function render(_ctx, _cache, $props, $setup, $data, $options) {
  return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", null, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_1, null, 512 /* NEED_PATCH */)]);
}

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/plugins/hotel/resources/assets/js/components/SalesReportsChart.vue?vue&type=template&id=5c82e8c4":
/*!*********************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/plugins/hotel/resources/assets/js/components/SalesReportsChart.vue?vue&type=template&id=5c82e8c4 ***!
  \*********************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "vue");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);

var _hoisted_1 = {
  key: 0,
  "class": "btn-group d-block text-end"
};
var _hoisted_2 = {
  "class": "btn btn-sm btn-secondary",
  href: "javascript:",
  "data-bs-toggle": "dropdown",
  "aria-expanded": "false"
};
var _hoisted_3 = {
  "class": "dropdown-menu float-end"
};
var _hoisted_4 = ["onClick"];
var _hoisted_5 = {
  key: 1,
  "class": "row px-3"
};
var _hoisted_6 = {
  "class": "col-12"
};
var _hoisted_7 = {
  "class": "list-unstyled"
};
function render(_ctx, _cache, $props, $setup, $data, $options) {
  return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", null, [$props.filters.length ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_1, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("a", _hoisted_2, [_cache[0] || (_cache[0] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
    "class": "fa fa-filter",
    "aria-hidden": "true"
  }, null, -1 /* CACHED */)), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("span", null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(_ctx.filtering), 1 /* TEXT */), _cache[1] || (_cache[1] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
    "class": "fa fa-angle-down"
  }, null, -1 /* CACHED */))]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("ul", _hoisted_3, [((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)($props.filters, function (filter) {
    return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("li", {
      key: filter.key
    }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("a", {
      href: "#",
      onClick: function onClick($event) {
        return $options.clickFilter(filter.key, $event);
      }
    }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(filter.text), 9 /* TEXT, PROPS */, _hoisted_4)]);
  }), 128 /* KEYED_FRAGMENT */))])])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), _cache[2] || (_cache[2] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
    "class": "sales-reports-chart"
  }, null, -1 /* CACHED */)), _ctx.earningSales.length ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_5, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_6, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("ul", _hoisted_7, [((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)(_ctx.earningSales, function (earningSale) {
    return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("li", {
      key: earningSale.text
    }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
      "class": "icon ti ti-circle-filled",
      style: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeStyle)({
        color: earningSale.color
      })
    }, null, 4 /* STYLE */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)(" " + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(earningSale.text), 1 /* TEXT */)]);
  }), 128 /* KEYED_FRAGMENT */))])])])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), _cache[3] || (_cache[3] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
    "class": "loading"
  }, null, -1 /* CACHED */))]);
}

/***/ }),

/***/ "./node_modules/vue-loader/dist/exportHelper.js":
/*!******************************************************!*\
  !*** ./node_modules/vue-loader/dist/exportHelper.js ***!
  \******************************************************/
/***/ ((__unused_webpack_module, exports) => {


Object.defineProperty(exports, "__esModule", ({ value: true }));
// runtime helper for setting properties on components
// in a tree-shakable way
exports["default"] = (sfc, props) => {
    const target = sfc.__vccOpts || sfc;
    for (const [key, val] of props) {
        target[key] = val;
    }
    return target;
};


/***/ }),

/***/ "./platform/plugins/hotel/resources/assets/js/components/RevenueChart.vue":
/*!********************************************************************************!*\
  !*** ./platform/plugins/hotel/resources/assets/js/components/RevenueChart.vue ***!
  \********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _RevenueChart_vue_vue_type_template_id_25593fdf__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./RevenueChart.vue?vue&type=template&id=25593fdf */ "./platform/plugins/hotel/resources/assets/js/components/RevenueChart.vue?vue&type=template&id=25593fdf");
/* harmony import */ var _RevenueChart_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./RevenueChart.vue?vue&type=script&lang=js */ "./platform/plugins/hotel/resources/assets/js/components/RevenueChart.vue?vue&type=script&lang=js");
/* harmony import */ var _node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../../node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;
const __exports__ = /*#__PURE__*/(0,_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__["default"])(_RevenueChart_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_RevenueChart_vue_vue_type_template_id_25593fdf__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"platform/plugins/hotel/resources/assets/js/components/RevenueChart.vue"]])
/* hot reload */
if (false) // removed by dead control flow
{}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./platform/plugins/hotel/resources/assets/js/components/RevenueChart.vue?vue&type=script&lang=js":
/*!********************************************************************************************************!*\
  !*** ./platform/plugins/hotel/resources/assets/js/components/RevenueChart.vue?vue&type=script&lang=js ***!
  \********************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_RevenueChart_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_RevenueChart_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./RevenueChart.vue?vue&type=script&lang=js */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/plugins/hotel/resources/assets/js/components/RevenueChart.vue?vue&type=script&lang=js");
 

/***/ }),

/***/ "./platform/plugins/hotel/resources/assets/js/components/RevenueChart.vue?vue&type=template&id=25593fdf":
/*!**************************************************************************************************************!*\
  !*** ./platform/plugins/hotel/resources/assets/js/components/RevenueChart.vue?vue&type=template&id=25593fdf ***!
  \**************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_RevenueChart_vue_vue_type_template_id_25593fdf__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_RevenueChart_vue_vue_type_template_id_25593fdf__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!../../../../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./RevenueChart.vue?vue&type=template&id=25593fdf */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/plugins/hotel/resources/assets/js/components/RevenueChart.vue?vue&type=template&id=25593fdf");


/***/ }),

/***/ "./platform/plugins/hotel/resources/assets/js/components/SalesReportsChart.vue":
/*!*************************************************************************************!*\
  !*** ./platform/plugins/hotel/resources/assets/js/components/SalesReportsChart.vue ***!
  \*************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _SalesReportsChart_vue_vue_type_template_id_5c82e8c4__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SalesReportsChart.vue?vue&type=template&id=5c82e8c4 */ "./platform/plugins/hotel/resources/assets/js/components/SalesReportsChart.vue?vue&type=template&id=5c82e8c4");
/* harmony import */ var _SalesReportsChart_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SalesReportsChart.vue?vue&type=script&lang=js */ "./platform/plugins/hotel/resources/assets/js/components/SalesReportsChart.vue?vue&type=script&lang=js");
/* harmony import */ var _node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../../node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;
const __exports__ = /*#__PURE__*/(0,_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__["default"])(_SalesReportsChart_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_SalesReportsChart_vue_vue_type_template_id_5c82e8c4__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"platform/plugins/hotel/resources/assets/js/components/SalesReportsChart.vue"]])
/* hot reload */
if (false) // removed by dead control flow
{}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./platform/plugins/hotel/resources/assets/js/components/SalesReportsChart.vue?vue&type=script&lang=js":
/*!*************************************************************************************************************!*\
  !*** ./platform/plugins/hotel/resources/assets/js/components/SalesReportsChart.vue?vue&type=script&lang=js ***!
  \*************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_SalesReportsChart_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_SalesReportsChart_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./SalesReportsChart.vue?vue&type=script&lang=js */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/plugins/hotel/resources/assets/js/components/SalesReportsChart.vue?vue&type=script&lang=js");
 

/***/ }),

/***/ "./platform/plugins/hotel/resources/assets/js/components/SalesReportsChart.vue?vue&type=template&id=5c82e8c4":
/*!*******************************************************************************************************************!*\
  !*** ./platform/plugins/hotel/resources/assets/js/components/SalesReportsChart.vue?vue&type=template&id=5c82e8c4 ***!
  \*******************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_SalesReportsChart_vue_vue_type_template_id_5c82e8c4__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_SalesReportsChart_vue_vue_type_template_id_5c82e8c4__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!../../../../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./SalesReportsChart.vue?vue&type=template&id=5c82e8c4 */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/plugins/hotel/resources/assets/js/components/SalesReportsChart.vue?vue&type=template&id=5c82e8c4");


/***/ }),

/***/ "vue":
/*!**********************!*\
  !*** external "Vue" ***!
  \**********************/
/***/ ((module) => {

module.exports = Vue;

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!**************************************************************!*\
  !*** ./platform/plugins/hotel/resources/assets/js/report.js ***!
  \**************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _components_SalesReportsChart__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./components/SalesReportsChart */ "./platform/plugins/hotel/resources/assets/js/components/SalesReportsChart.vue");
/* harmony import */ var _components_RevenueChart__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./components/RevenueChart */ "./platform/plugins/hotel/resources/assets/js/components/RevenueChart.vue");
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }


if (typeof vueApp !== 'undefined') {
  vueApp.booting(function (vue) {
    vue.component('sales-reports-chart', _components_SalesReportsChart__WEBPACK_IMPORTED_MODULE_0__["default"]);
    vue.component('revenue-chart', _components_RevenueChart__WEBPACK_IMPORTED_MODULE_1__["default"]);
  });
}
$(function () {
  if (!window.moment || !jQuery().daterangepicker) {
    return;
  }
  moment.locale($('html').attr('lang'));
  var $dateRange = $(document).find('.date-range-picker');
  var dateFormat = $dateRange.data('format') || 'YYYY-MM-DD';
  var startDate = $dateRange.data('start-date') || moment().subtract(29, 'days');
  var today = moment();
  var endDate = moment().endOf('month');
  if (endDate > today) {
    endDate = today;
  }
  var rangesTrans = BotbleVariables.languages.reports;
  var ranges = _defineProperty(_defineProperty(_defineProperty(_defineProperty(_defineProperty(_defineProperty({}, rangesTrans.today, [today, today]), rangesTrans.this_week, [moment().startOf('week'), today]), rangesTrans.last_7_days, [moment().subtract(6, 'days'), today]), rangesTrans.last_30_days, [moment().subtract(29, 'days'), today]), rangesTrans.this_month, [moment().startOf('month'), endDate]), rangesTrans.this_year, [moment().startOf('year'), moment().endOf('year')]);
  $dateRange.daterangepicker({
    ranges: ranges,
    alwaysShowCalendars: true,
    startDate: startDate,
    endDate: endDate,
    maxDate: endDate,
    opens: 'left',
    drops: 'auto',
    locale: {
      format: dateFormat
    },
    autoUpdateInput: false
  }, function (start, end, label) {
    $.ajax({
      url: $dateRange.data('href'),
      data: {
        date_from: start.format('YYYY-MM-DD'),
        date_to: end.format('YYYY-MM-DD'),
        predefined_range: label
      },
      type: 'GET',
      success: function success(data) {
        if (data.error) {
          Botble.showError(data.message);
        } else {
          if (!$('#report-stats-content').length) {
            var newUrl = new URL(window.location.href);
            newUrl.searchParams.set('date_from', start.format('YYYY-MM-DD'));
            newUrl.searchParams.set('date_to', end.format('YYYY-MM-DD'));
            history.pushState({
              urlPath: newUrl.href
            }, '', newUrl.href);
            window.location.reload();
          } else {
            $('.widget-item').each(function (key, widget) {
              var widgetEl = $(widget).prop('id');
              if (!widgetEl) {
                return;
              }
              $("#".concat(widgetEl)).replaceWith($(data.data).find("#".concat(widgetEl)));
            });
          }
          if (window.LaravelDataTables) {
            Object.keys(window.LaravelDataTables).map(function (key) {
              var table = window.LaravelDataTables[key];
              var url = new URL(table.ajax.url());
              url.searchParams.set('date_from', start.format('YYYY-MM-DD'));
              url.searchParams.set('date_to', end.format('YYYY-MM-DD'));
              table.ajax.url(url.href).load();
            });
          }
        }
      },
      error: function error(data) {
        Botble.handleError(data);
      }
    });
  });
  $dateRange.on('apply.daterangepicker', function (ev, picker) {
    var $this = $(this);
    var formatValue = $this.data('format-value');
    if (!formatValue) {
      formatValue = '__from__ - __to__';
    }
    var value = formatValue.replace('__from__', picker.startDate.format(dateFormat)).replace('__to__', picker.endDate.format(dateFormat));
    $this.find('span').text(value);
  });
});
})();

/******/ })()
;