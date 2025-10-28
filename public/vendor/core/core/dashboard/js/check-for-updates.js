/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/core/dashboard/resources/js/components/CheckForUpdates.vue?vue&type=script&lang=js":
/*!*********************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/core/dashboard/resources/js/components/CheckForUpdates.vue?vue&type=script&lang=js ***!
  \*********************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  props: {
    checkUpdateUrl: {
      type: String,
      "default": function _default() {
        return null;
      },
      required: true
    }
  },
  data: function data() {
    return {
      hasNewVersion: false,
      message: null
    };
  },
  mounted: function mounted() {
    this.checkUpdate();
  },
  methods: {
    checkUpdate: function checkUpdate() {
      var _this = this;
      // Check if we should make the update check request
      var shouldCheckUpdate = function shouldCheckUpdate() {
        var lastCheckTime = localStorage.getItem('system_update_check_time');
        if (!lastCheckTime) {
          return true;
        }

        // Call once every 15 minutes (900000 ms)
        var fifteenMinutesInMs = 15 * 60 * 1000;
        return Date.now() - parseInt(lastCheckTime) > fifteenMinutesInMs;
      };

      // Try to get cached update data
      var cachedHasNewVersion = localStorage.getItem('system_update_has_new_version') === 'true';
      var cachedMessage = localStorage.getItem('system_update_message');
      if (cachedHasNewVersion && cachedMessage && !shouldCheckUpdate()) {
        this.hasNewVersion = cachedHasNewVersion;
        this.message = cachedMessage;
        return;
      }
      if (!shouldCheckUpdate()) {
        return;
      }
      axios.get(this.checkUpdateUrl).then(function (_ref) {
        var data = _ref.data;
        // Store the current time as the last check time
        localStorage.setItem('system_update_check_time', Date.now().toString());
        if (!data.error && data.data.has_new_version) {
          _this.hasNewVersion = true;
          _this.message = data.message;

          // Store the update status
          localStorage.setItem('system_update_has_new_version', 'true');
          localStorage.setItem('system_update_message', data.message);
        } else {
          // Clear any previous update status
          localStorage.setItem('system_update_has_new_version', 'false');
          localStorage.removeItem('system_update_message');
        }
      })["catch"](function () {
        // Even on error, we've made the request, so store the time
        localStorage.setItem('system_update_check_time', Date.now().toString());
      });
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/core/dashboard/resources/js/components/CheckForUpdates.vue?vue&type=template&id=e8615b02":
/*!*************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/core/dashboard/resources/js/components/CheckForUpdates.vue?vue&type=template&id=e8615b02 ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "vue");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);

function render(_ctx, _cache, $props, $setup, $data, $options) {
  return (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderSlot)(_ctx.$slots, "default", (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeProps)((0,vue__WEBPACK_IMPORTED_MODULE_0__.guardReactiveProps)({
    hasNewVersion: $data.hasNewVersion,
    message: $data.message
  })));
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

/***/ "./platform/core/dashboard/resources/js/components/CheckForUpdates.vue":
/*!*****************************************************************************!*\
  !*** ./platform/core/dashboard/resources/js/components/CheckForUpdates.vue ***!
  \*****************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _CheckForUpdates_vue_vue_type_template_id_e8615b02__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./CheckForUpdates.vue?vue&type=template&id=e8615b02 */ "./platform/core/dashboard/resources/js/components/CheckForUpdates.vue?vue&type=template&id=e8615b02");
/* harmony import */ var _CheckForUpdates_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./CheckForUpdates.vue?vue&type=script&lang=js */ "./platform/core/dashboard/resources/js/components/CheckForUpdates.vue?vue&type=script&lang=js");
/* harmony import */ var _node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;
const __exports__ = /*#__PURE__*/(0,_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__["default"])(_CheckForUpdates_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_CheckForUpdates_vue_vue_type_template_id_e8615b02__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"platform/core/dashboard/resources/js/components/CheckForUpdates.vue"]])
/* hot reload */
if (false) // removed by dead control flow
{}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./platform/core/dashboard/resources/js/components/CheckForUpdates.vue?vue&type=script&lang=js":
/*!*****************************************************************************************************!*\
  !*** ./platform/core/dashboard/resources/js/components/CheckForUpdates.vue?vue&type=script&lang=js ***!
  \*****************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_CheckForUpdates_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_CheckForUpdates_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./CheckForUpdates.vue?vue&type=script&lang=js */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/core/dashboard/resources/js/components/CheckForUpdates.vue?vue&type=script&lang=js");
 

/***/ }),

/***/ "./platform/core/dashboard/resources/js/components/CheckForUpdates.vue?vue&type=template&id=e8615b02":
/*!***********************************************************************************************************!*\
  !*** ./platform/core/dashboard/resources/js/components/CheckForUpdates.vue?vue&type=template&id=e8615b02 ***!
  \***********************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_CheckForUpdates_vue_vue_type_template_id_e8615b02__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_CheckForUpdates_vue_vue_type_template_id_e8615b02__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!../../../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./CheckForUpdates.vue?vue&type=template&id=e8615b02 */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/core/dashboard/resources/js/components/CheckForUpdates.vue?vue&type=template&id=e8615b02");


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
/*!*******************************************************************!*\
  !*** ./platform/core/dashboard/resources/js/check-for-updates.js ***!
  \*******************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _components_CheckForUpdates_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./components/CheckForUpdates.vue */ "./platform/core/dashboard/resources/js/components/CheckForUpdates.vue");

if (typeof vueApp !== 'undefined') {
  vueApp.booting(function (vue) {
    vue.component('v-check-for-updates', _components_CheckForUpdates_vue__WEBPACK_IMPORTED_MODULE_0__["default"]);
  });
}
})();

/******/ })()
;