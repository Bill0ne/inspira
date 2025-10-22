/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/plugins/hotel/resources/assets/js/components/CalendarModalComponent.vue?vue&type=script&lang=js":
/*!**********************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/plugins/hotel/resources/assets/js/components/CalendarModalComponent.vue?vue&type=script&lang=js ***!
  \**********************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  props: {
    getRoomAvailabilityUrl: {
      type: String,
      "default": ''
    }
  },
  data: function data() {
    return {
      form: {
        id: '',
        value: '',
        value_type: 'fixed',
        start_date: '',
        end_date: '',
        enable_person: 0,
        min_guests: 0,
        max_guests: 0,
        active: 0,
        number_of_rooms: 1
      },
      formDefault: {
        id: '',
        value: '',
        start_date: '',
        end_date: '',
        enable_person: 0,
        min_guests: 0,
        max_guests: 0,
        active: 0,
        number_of_rooms: 1
      },
      onSubmit: false,
      calendar: null
    };
  },
  methods: {
    show: function show(form) {
      $('#modal-calendar').modal('show');
      this.onSubmit = false;
      if (typeof form != 'undefined') {
        this.form = Object.assign({}, form);
        if (form.start_date) {
          $('.modal-title').text(moment(form.start_date).format('MM/DD/YYYY'));
        }
      }
    },
    hide: function hide() {
      $('#modal-calendar').modal('hide');
      this.form = Object.assign({}, this.formDefault);
    },
    saveForm: function saveForm() {
      var _this = this;
      var _self = this;
      if (this.onSubmit) {
        return;
      }
      if (!this.validateForm()) {
        return;
      }
      $('#modal-calendar').find('.btn-primary').addClass('button-loading');
      this.onSubmit = true;
      $.ajax({
        url: this.getRoomAvailabilityUrl,
        data: this.form,
        dataType: 'json',
        method: 'POST',
        success: function success(res) {
          if (!res.error) {
            if (_this.calendar) {
              _this.calendar.refetchEvents();
            }
            _self.hide();
            Botble.showSuccess(res.message);
          } else {
            Botble.showError(res.message);
          }
          _self.onSubmit = false;
          $('#modal-calendar').find('.btn-primary').removeClass('button-loading');
        },
        error: function error() {
          _self.onSubmit = false;
          $('#modal-calendar').find('.btn-primary').removeClass('button-loading');
        }
      });
    },
    validateForm: function validateForm() {
      if (!this.form.start_date) {
        return false;
      }
      return this.form.end_date;
    }
  },
  created: function created() {
    var _self = this;
    this.$nextTick(function () {
      $(_self.$el).on('hide.bs.modal', function () {
        this.form = Object.assign({}, this.formDefault);
      });
    });
  },
  mounted: function mounted() {
    var _this2 = this;
    var calendarEl;
    calendarEl = document.getElementById('dates-calendar');
    if (this.calendar) {
      this.calendar.destroy();
    }
    this.calendar = new FullCalendar.Calendar(calendarEl, {
      headerToolbar: {
        left: 'title'
      },
      navLinks: true,
      // can click day/week names to navigate views
      editable: false,
      dayMaxEvents: false,
      // allow "more" link when too many events
      events: {
        url: this.getRoomAvailabilityUrl
      },
      loading: function loading(isLoading) {
        if (!isLoading) {
          $(calendarEl).removeClass('loading');
        } else {
          $(calendarEl).addClass('loading');
        }
      },
      select: function select(arg) {
        _this2.show({
          start_date: moment(arg.start).format('YYYY-MM-DD'),
          end_date: moment(arg.end).format('YYYY-MM-DD')
        });
      },
      eventClick: function eventClick(info) {
        var form = Object.assign({}, info.event.extendedProps);
        form.start_date = moment(info.event.start).format('YYYY-MM-DD');
        form.end_date = moment(info.event.start).format('YYYY-MM-DD');
        _this2.show(form);
      },
      eventRender: function eventRender(info) {
        $(info.el).find('.fc-title').html(info.event.title);
      }
    });
    this.calendar.render();
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/plugins/hotel/resources/assets/js/components/CalendarModalComponent.vue?vue&type=template&id=1129122b":
/*!**************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/plugins/hotel/resources/assets/js/components/CalendarModalComponent.vue?vue&type=template&id=1129122b ***!
  \**************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "vue");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);

function render(_ctx, _cache, $props, $setup, $data, $options) {
  return (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderSlot)(_ctx.$slots, "default", (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeProps)((0,vue__WEBPACK_IMPORTED_MODULE_0__.guardReactiveProps)({
    form: $data.form,
    saveForm: $options.saveForm
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

/***/ "./platform/plugins/hotel/resources/assets/js/components/CalendarModalComponent.vue":
/*!******************************************************************************************!*\
  !*** ./platform/plugins/hotel/resources/assets/js/components/CalendarModalComponent.vue ***!
  \******************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _CalendarModalComponent_vue_vue_type_template_id_1129122b__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./CalendarModalComponent.vue?vue&type=template&id=1129122b */ "./platform/plugins/hotel/resources/assets/js/components/CalendarModalComponent.vue?vue&type=template&id=1129122b");
/* harmony import */ var _CalendarModalComponent_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./CalendarModalComponent.vue?vue&type=script&lang=js */ "./platform/plugins/hotel/resources/assets/js/components/CalendarModalComponent.vue?vue&type=script&lang=js");
/* harmony import */ var _node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../../node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;
const __exports__ = /*#__PURE__*/(0,_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__["default"])(_CalendarModalComponent_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_CalendarModalComponent_vue_vue_type_template_id_1129122b__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"platform/plugins/hotel/resources/assets/js/components/CalendarModalComponent.vue"]])
/* hot reload */
if (false) // removed by dead control flow
{}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./platform/plugins/hotel/resources/assets/js/components/CalendarModalComponent.vue?vue&type=script&lang=js":
/*!******************************************************************************************************************!*\
  !*** ./platform/plugins/hotel/resources/assets/js/components/CalendarModalComponent.vue?vue&type=script&lang=js ***!
  \******************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_CalendarModalComponent_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_CalendarModalComponent_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./CalendarModalComponent.vue?vue&type=script&lang=js */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/plugins/hotel/resources/assets/js/components/CalendarModalComponent.vue?vue&type=script&lang=js");
 

/***/ }),

/***/ "./platform/plugins/hotel/resources/assets/js/components/CalendarModalComponent.vue?vue&type=template&id=1129122b":
/*!************************************************************************************************************************!*\
  !*** ./platform/plugins/hotel/resources/assets/js/components/CalendarModalComponent.vue?vue&type=template&id=1129122b ***!
  \************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_CalendarModalComponent_vue_vue_type_template_id_1129122b__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_CalendarModalComponent_vue_vue_type_template_id_1129122b__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!../../../../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./CalendarModalComponent.vue?vue&type=template&id=1129122b */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/plugins/hotel/resources/assets/js/components/CalendarModalComponent.vue?vue&type=template&id=1129122b");


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
/*!*************************************************************************!*\
  !*** ./platform/plugins/hotel/resources/assets/js/room-availability.js ***!
  \*************************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _components_CalendarModalComponent__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./components/CalendarModalComponent */ "./platform/plugins/hotel/resources/assets/js/components/CalendarModalComponent.vue");

if (typeof vueApp !== 'undefined') {
  vueApp.booting(function (vue) {
    vue.component('calendar-modal-component', _components_CalendarModalComponent__WEBPACK_IMPORTED_MODULE_0__["default"]);
  });
}
})();

/******/ })()
;