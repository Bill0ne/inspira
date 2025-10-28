/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/core/setting/resources/js/components/LicenseForm.vue?vue&type=script&lang=js":
/*!***************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/core/setting/resources/js/components/LicenseForm.vue?vue&type=script&lang=js ***!
  \***************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
function _regenerator() { /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/babel/babel/blob/main/packages/babel-helpers/LICENSE */ var e, t, r = "function" == typeof Symbol ? Symbol : {}, n = r.iterator || "@@iterator", o = r.toStringTag || "@@toStringTag"; function i(r, n, o, i) { var c = n && n.prototype instanceof Generator ? n : Generator, u = Object.create(c.prototype); return _regeneratorDefine2(u, "_invoke", function (r, n, o) { var i, c, u, f = 0, p = o || [], y = !1, G = { p: 0, n: 0, v: e, a: d, f: d.bind(e, 4), d: function d(t, r) { return i = t, c = 0, u = e, G.n = r, a; } }; function d(r, n) { for (c = r, u = n, t = 0; !y && f && !o && t < p.length; t++) { var o, i = p[t], d = G.p, l = i[2]; r > 3 ? (o = l === n) && (u = i[(c = i[4]) ? 5 : (c = 3, 3)], i[4] = i[5] = e) : i[0] <= d && ((o = r < 2 && d < i[1]) ? (c = 0, G.v = n, G.n = i[1]) : d < l && (o = r < 3 || i[0] > n || n > l) && (i[4] = r, i[5] = n, G.n = l, c = 0)); } if (o || r > 1) return a; throw y = !0, n; } return function (o, p, l) { if (f > 1) throw TypeError("Generator is already running"); for (y && 1 === p && d(p, l), c = p, u = l; (t = c < 2 ? e : u) || !y;) { i || (c ? c < 3 ? (c > 1 && (G.n = -1), d(c, u)) : G.n = u : G.v = u); try { if (f = 2, i) { if (c || (o = "next"), t = i[o]) { if (!(t = t.call(i, u))) throw TypeError("iterator result is not an object"); if (!t.done) return t; u = t.value, c < 2 && (c = 0); } else 1 === c && (t = i["return"]) && t.call(i), c < 2 && (u = TypeError("The iterator does not provide a '" + o + "' method"), c = 1); i = e; } else if ((t = (y = G.n < 0) ? u : r.call(n, G)) !== a) break; } catch (t) { i = e, c = 1, u = t; } finally { f = 1; } } return { value: t, done: y }; }; }(r, o, i), !0), u; } var a = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} t = Object.getPrototypeOf; var c = [][n] ? t(t([][n]())) : (_regeneratorDefine2(t = {}, n, function () { return this; }), t), u = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(c); function f(e) { return Object.setPrototypeOf ? Object.setPrototypeOf(e, GeneratorFunctionPrototype) : (e.__proto__ = GeneratorFunctionPrototype, _regeneratorDefine2(e, o, "GeneratorFunction")), e.prototype = Object.create(u), e; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, _regeneratorDefine2(u, "constructor", GeneratorFunctionPrototype), _regeneratorDefine2(GeneratorFunctionPrototype, "constructor", GeneratorFunction), GeneratorFunction.displayName = "GeneratorFunction", _regeneratorDefine2(GeneratorFunctionPrototype, o, "GeneratorFunction"), _regeneratorDefine2(u), _regeneratorDefine2(u, o, "Generator"), _regeneratorDefine2(u, n, function () { return this; }), _regeneratorDefine2(u, "toString", function () { return "[object Generator]"; }), (_regenerator = function _regenerator() { return { w: i, m: f }; })(); }
function _regeneratorDefine2(e, r, n, t) { var i = Object.defineProperty; try { i({}, "", {}); } catch (e) { i = 0; } _regeneratorDefine2 = function _regeneratorDefine(e, r, n, t) { function o(r, n) { _regeneratorDefine2(e, r, function (e) { return this._invoke(r, n, e); }); } r ? i ? i(e, r, { value: n, enumerable: !t, configurable: !t, writable: !t }) : e[r] = n : (o("next", 0), o("throw", 1), o("return", 2)); }, _regeneratorDefine2(e, r, n, t); }
function asyncGeneratorStep(n, t, e, r, o, a, c) { try { var i = n[a](c), u = i.value; } catch (n) { return void e(n); } i.done ? t(u) : Promise.resolve(u).then(r, o); }
function _asyncToGenerator(n) { return function () { var t = this, e = arguments; return new Promise(function (r, o) { var a = n.apply(t, e); function _next(n) { asyncGeneratorStep(a, r, o, _next, _throw, "next", n); } function _throw(n) { asyncGeneratorStep(a, r, o, _next, _throw, "throw", n); } _next(void 0); }); }; }
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  props: {
    id: {
      type: String,
      "default": function _default() {
        return null;
      },
      required: true
    },
    verifyUrl: {
      type: String,
      "default": function _default() {
        return null;
      },
      required: true
    },
    activateLicenseUrl: {
      type: String,
      "default": function _default() {
        return null;
      },
      required: true
    },
    deactivateLicenseUrl: {
      type: String,
      "default": function _default() {
        return null;
      },
      required: true
    },
    resetLicenseUrl: {
      type: String,
      "default": function _default() {
        return null;
      },
      required: true
    }
  },
  data: function data() {
    return {
      initialized: null,
      loading: true,
      verified: false,
      license: null
    };
  },
  mounted: function mounted() {
    this.verifyLicense();
  },
  methods: {
    verifyLicense: function verifyLicense() {
      var _this = this;
      return _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee() {
        var shouldVerifyLicense, cachedLicense, cachedVerified, _t;
        return _regenerator().w(function (_context) {
          while (1) switch (_context.p = _context.n) {
            case 0:
              // Check if we should make the license verification request
              shouldVerifyLicense = function shouldVerifyLicense() {
                var lastVerifyTime = localStorage.getItem('license_verification_time');
                var isVerified = localStorage.getItem('license_is_verified') === 'true';
                if (!lastVerifyTime) {
                  return true;
                }
                var now = Date.now();
                var lastTime = parseInt(lastVerifyTime);

                // If verified, check once every 3 days
                if (isVerified) {
                  var threeDaysInMs = 3 * 24 * 60 * 60 * 1000;
                  return now - lastTime > threeDaysInMs;
                } else {
                  // If not verified, check once per day
                  var oneDayInMs = 24 * 60 * 60 * 1000;
                  return now - lastTime > oneDayInMs;
                }
              }; // Try to get cached license data
              cachedLicense = localStorage.getItem('license_data');
              cachedVerified = localStorage.getItem('license_is_verified') === 'true';
              if (!(cachedLicense && !shouldVerifyLicense())) {
                _context.n = 3;
                break;
              }
              _context.p = 1;
              _this.verified = cachedVerified;
              _this.license = JSON.parse(cachedLicense);
              _this.initialized = true;
              _this.loading = false;
              return _context.a(2, Promise.resolve());
            case 2:
              _context.p = 2;
              _t = _context.v;
            case 3:
              return _context.a(2, $httpClient.makeWithoutErrorHandler().get(_this.verifyUrl).then(function (_ref) {
                var data = _ref.data;
                _this.verified = true;
                _this.license = data.data;

                // Store the verification result
                localStorage.setItem('license_verification_time', Date.now().toString());
                localStorage.setItem('license_is_verified', 'true');
                localStorage.setItem('license_data', JSON.stringify(data.data));
              })["catch"](function (data) {
                if (data.response && data.response.status === 400) {
                  Botble.showError(data.response.data.message);
                }

                // Store that we attempted verification but it failed
                localStorage.setItem('license_verification_time', Date.now().toString());
                localStorage.setItem('license_is_verified', 'false');
                localStorage.removeItem('license_data');
              })["finally"](function () {
                _this.initialized = true;
                _this.loading = false;
              }));
          }
        }, _callee, null, [[1, 2]]);
      }))();
    },
    onSubmit: function onSubmit() {
      var _this2 = this;
      return _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee2() {
        var formData;
        return _regenerator().w(function (_context2) {
          while (1) switch (_context2.n) {
            case 0:
              formData = new FormData(_this2.$refs.formRef);
              return _context2.a(2, _this2.doActivateLicense(formData));
          }
        }, _callee2);
      }))();
    },
    resetLicense: function resetLicense() {
      var _this3 = this;
      return _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee3() {
        var formData;
        return _regenerator().w(function (_context3) {
          while (1) switch (_context3.n) {
            case 0:
              formData = new FormData(_this3.$refs.formRef);
              return _context3.a(2, _this3.doResetLicense(formData));
          }
        }, _callee3);
      }))();
    },
    deactivateLicense: function deactivateLicense() {
      var _this4 = this;
      return _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee4() {
        return _regenerator().w(function (_context4) {
          while (1) switch (_context4.n) {
            case 0:
              _this4.loading = true;
              return _context4.a(2, $httpClient.make().post(_this4.deactivateLicenseUrl).then(function () {
                _this4.verified = false;

                // Update localStorage to reflect deactivation
                localStorage.setItem('license_verification_time', Date.now().toString());
                localStorage.setItem('license_is_verified', 'false');
                localStorage.removeItem('license_data');
              })["finally"](function () {
                _this4.loading = false;
              }));
          }
        }, _callee4);
      }))();
    },
    doActivateLicense: function doActivateLicense(formData) {
      var _this5 = this;
      return _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee5() {
        return _regenerator().w(function (_context5) {
          while (1) switch (_context5.n) {
            case 0:
              _this5.loading = true;
              return _context5.a(2, $httpClient.make().postForm(_this5.activateLicenseUrl, formData).then(function (_ref2) {
                var data = _ref2.data;
                _this5.verified = true;
                _this5.license = data.data;
                Botble.showSuccess(data.message);

                // Update localStorage to reflect activation
                localStorage.setItem('license_verification_time', Date.now().toString());
                localStorage.setItem('license_is_verified', 'true');
                localStorage.setItem('license_data', JSON.stringify(data.data));
              })["finally"](function () {
                _this5.loading = false;
              }));
          }
        }, _callee5);
      }))();
    },
    doResetLicense: function doResetLicense(formData) {
      var _this6 = this;
      return _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee6() {
        return _regenerator().w(function (_context6) {
          while (1) switch (_context6.n) {
            case 0:
              _this6.loading = true;
              return _context6.a(2, $httpClient.make().postForm(_this6.resetLicenseUrl, formData).then(function (_ref3) {
                var data = _ref3.data;
                _this6.verified = false;
                Botble.showSuccess(data.message);

                // Update localStorage to reflect reset
                localStorage.setItem('license_verification_time', Date.now().toString());
                localStorage.setItem('license_is_verified', 'false');
                localStorage.removeItem('license_data');
              })["finally"](function () {
                _this6.loading = false;
              }));
          }
        }, _callee6);
      }))();
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/core/setting/resources/js/components/LicenseForm.vue?vue&type=template&id=5322fe77":
/*!*******************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/core/setting/resources/js/components/LicenseForm.vue?vue&type=template&id=5322fe77 ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "vue");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);

function render(_ctx, _cache, $props, $setup, $data, $options) {
  return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("form", {
    id: "license-form",
    ref: "formRef",
    onSubmit: _cache[0] || (_cache[0] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.withModifiers)(function () {
      return $options.onSubmit && $options.onSubmit.apply($options, arguments);
    }, ["prevent"]))
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.renderSlot)(_ctx.$slots, "default", (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeProps)((0,vue__WEBPACK_IMPORTED_MODULE_0__.guardReactiveProps)({
    initialized: $data.initialized,
    loading: $data.loading,
    verified: $data.verified,
    license: $data.license,
    deactivateLicense: $options.deactivateLicense,
    resetLicense: $options.resetLicense
  })))], 544 /* NEED_HYDRATION, NEED_PATCH */);
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

/***/ "./platform/core/setting/resources/js/components/LicenseForm.vue":
/*!***********************************************************************!*\
  !*** ./platform/core/setting/resources/js/components/LicenseForm.vue ***!
  \***********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _LicenseForm_vue_vue_type_template_id_5322fe77__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./LicenseForm.vue?vue&type=template&id=5322fe77 */ "./platform/core/setting/resources/js/components/LicenseForm.vue?vue&type=template&id=5322fe77");
/* harmony import */ var _LicenseForm_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./LicenseForm.vue?vue&type=script&lang=js */ "./platform/core/setting/resources/js/components/LicenseForm.vue?vue&type=script&lang=js");
/* harmony import */ var _node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;
const __exports__ = /*#__PURE__*/(0,_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__["default"])(_LicenseForm_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_LicenseForm_vue_vue_type_template_id_5322fe77__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"platform/core/setting/resources/js/components/LicenseForm.vue"]])
/* hot reload */
if (false) // removed by dead control flow
{}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./platform/core/setting/resources/js/components/LicenseForm.vue?vue&type=script&lang=js":
/*!***********************************************************************************************!*\
  !*** ./platform/core/setting/resources/js/components/LicenseForm.vue?vue&type=script&lang=js ***!
  \***********************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_LicenseForm_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_LicenseForm_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./LicenseForm.vue?vue&type=script&lang=js */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/core/setting/resources/js/components/LicenseForm.vue?vue&type=script&lang=js");
 

/***/ }),

/***/ "./platform/core/setting/resources/js/components/LicenseForm.vue?vue&type=template&id=5322fe77":
/*!*****************************************************************************************************!*\
  !*** ./platform/core/setting/resources/js/components/LicenseForm.vue?vue&type=template&id=5322fe77 ***!
  \*****************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_LicenseForm_vue_vue_type_template_id_5322fe77__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_LicenseForm_vue_vue_type_template_id_5322fe77__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!../../../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./LicenseForm.vue?vue&type=template&id=5322fe77 */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/core/setting/resources/js/components/LicenseForm.vue?vue&type=template&id=5322fe77");


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
/*!*****************************************************************!*\
  !*** ./platform/core/setting/resources/js/license-component.js ***!
  \*****************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _components_LicenseForm_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./components/LicenseForm.vue */ "./platform/core/setting/resources/js/components/LicenseForm.vue");

if (typeof vueApp !== 'undefined') {
  vueApp.booting(function (vue) {
    vue.component('v-license-form', _components_LicenseForm_vue__WEBPACK_IMPORTED_MODULE_0__["default"]);
  });
}
})();

/******/ })()
;