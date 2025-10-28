/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./platform/core/base/resources/js/base/toast.js":
/*!*******************************************************!*\
  !*** ./platform/core/base/resources/js/base/toast.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
/**
 * Options used for Toastify
 * @typedef {Object} ToastifyConfigurationObject
 * @property {string} text - Message to be displayed in the toast
 * @property {Element} node - Provide a node to be mounted inside the toast. node takes higher precedence over text
 * @property {number} duration - Duration for which the toast should be displayed. -1 for permanent toast
 * @property {string|Element} selector - CSS ID Selector on which the toast should be added
 * @property {boolean} close - To show the close icon or not
 * @property {string} gravity - To show the toast from top or bottom
 * @property {string} position - To show the toast on left or right
 * @property {string} className - Ability to provide custom class name for further customization
 * @property {boolean} stopOnFocus - To stop timer when hovered over the toast (Only if duration is set)
 * @property {Function} callback - Invoked when the toast is dismissed
 * @property {Function} onClick - Invoked when the toast is clicked
 * @property {Object} offset - Ability to add some offset to axis
 * @property {boolean} escapeMarkup - Toggle the default behavior of escaping HTML markup
 * @property {string} ariaLive - Use the HTML DOM style property to add styles to toast
 * @property {Object} style - Use the HTML DOM style property to add styles to toast
 * @property {string} icon - Icon to be shown before text
 */
var Toastify = /*#__PURE__*/function () {
  function Toastify(options) {
    _classCallCheck(this, Toastify);
    _defineProperty(this, "defaults", {
      oldestFirst: true,
      text: 'Toastify is awesome!',
      node: undefined,
      duration: 3000,
      selector: undefined,
      callback: function callback() {},
      close: false,
      gravity: 'toastify-top',
      position: '',
      className: '',
      stopOnFocus: true,
      onClick: function onClick() {},
      offset: {
        x: 0,
        y: 0
      },
      escapeMarkup: true,
      ariaLive: 'polite',
      style: {
        background: ''
      }
    });
    /**
     * The configuration object to configure Toastify
     * @type {ToastifyConfigurationObject}
     * @public
     */
    this.options = {};

    /**
     * The element that is the Toast
     * @type {Element}
     * @public
     */
    this.toastElement = null;

    /**
     * The root element that contains all the toasts
     * @type {Element}
     * @private
     */
    this._rootElement = document.body;
    this._init(options);
  }

  /**
   * Display the toast
   * @public
   */
  return _createClass(Toastify, [{
    key: "showToast",
    value: function showToast() {
      var _this = this;
      this.toastElement = this._buildToast();
      if (typeof this.options.selector === 'string') {
        this._rootElement = document.getElementById(this.options.selector);
      } else if (this.options.selector instanceof HTMLElement || this.options.selector instanceof ShadowRoot) {
        this._rootElement = this.options.selector;
      } else {
        this._rootElement = document.body;
      }
      if (!this._rootElement) {
        throw 'Root element is not defined';
      }
      this._rootElement.insertBefore(this.toastElement, this._rootElement.firstChild);
      this._reposition();
      if (this.options.duration > 0) {
        this.toastElement.timeOutValue = window.setTimeout(function () {
          _this._removeElement(_this.toastElement);
        }, this.options.duration);
      }
      return this;
    }

    /**
     * Hide the toast
     * @public
     */
  }, {
    key: "hideToast",
    value: function hideToast() {
      if (this.toastElement.timeOutValue) {
        clearTimeout(this.toastElement.timeOutValue);
      }
      this._removeElement(this.toastElement);
    }

    /**
     * Init the Toastify class
     * @param {ToastifyConfigurationObject} options - The configuration object to configure Toastify
     * @param {string} [options.text=Hi there!] - Message to be displayed in the toast
     * @param {Element} [options.node] - Provide a node to be mounted inside the toast. node takes higher precedence over text
     * @param {number} [options.duration=3000] - Duration for which the toast should be displayed. -1 for permanent toast
     * @param {string} [options.selector] - CSS Selector on which the toast should be added
     * @param {boolean} [options.close=false] - To show the close icon or not
     * @param {string} [options.gravity=toastify-top] - To show the toast from top or bottom
     * @param {string} [options.position=right] - To show the toast on left or right
     * @param {string} [options.className] - Ability to provide custom class name for further customization
     * @param {boolean} [options.stopOnFocus] - To stop timer when hovered over the toast (Only if duration is set)
     * @param {Function} [options.callback] - Invoked when the toast is dismissed
     * @param {Function} [options.onClick] - Invoked when the toast is clicked
     * @param {Object} [options.offset] - Ability to add some offset to axis
     * @param {boolean} [options.escapeMarkup=true] - Toggle the default behavior of escaping HTML markup
     * @param {string} [options.ariaLive] - Announce the toast to screen readers
     * @param {Object} [options.style] - Use the HTML DOM style property to add styles to toast
     * @private
     */
  }, {
    key: "_init",
    value: function _init(options) {
      this.options = Object.assign(this.defaults, options);
      this.toastElement = null;
      this.options.gravity = options.gravity === 'bottom' ? 'toastify-bottom' : 'toastify-top';
      this.options.stopOnFocus = options.stopOnFocus === undefined ? true : options.stopOnFocus;
    }

    /**
     * Build the Toastify element
     * @returns {Element}
     * @private
     */
  }, {
    key: "_buildToast",
    value: function _buildToast() {
      var _this2 = this;
      if (!this.options) {
        throw 'Toastify is not initialized';
      }
      var divElement = document.createElement('div');
      divElement.className = "toastify on ".concat(this.options.className, " pe-5");
      divElement.className += " toastify-".concat(this.options.position);
      divElement.className += " ".concat(this.options.gravity);
      for (var property in this.options.style) {
        divElement.style[property] = this.options.style[property];
      }
      if (this.options.ariaLive) {
        divElement.setAttribute('aria-live', this.options.ariaLive);
      }
      if (this.options.icon !== '') {
        var iconElement = document.createElement('div');
        iconElement.className = 'toastify-icon';
        iconElement.innerHTML = this.options.icon;
        divElement.appendChild(iconElement);
      }
      var textElement = document.createElement('span');
      textElement.className = 'toastify-text';
      if (this.options.node && this.options.node.nodeType === Node.ELEMENT_NODE) {
        textElement.appendChild(this.options.node);
      } else {
        if (this.options.escapeMarkup) {
          textElement.innerText = this.options.text;
        } else {
          textElement.innerHTML = this.options.text;
        }
      }
      divElement.appendChild(textElement);
      if (this.options.close === true) {
        var closeElement = document.createElement('button');
        closeElement.type = 'button';
        closeElement.setAttribute('aria-label', 'Close');
        closeElement.className = 'toast-close';
        closeElement.style.cssText = 'position: absolute; top: 8px; inset-inline-end: 8px;';
        closeElement.innerHTML = "<svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\">\n                <path stroke=\"none\" d=\"M0 0h24v24H0z\" fill=\"none\"></path>\n                <path d=\"M18 6l-12 12\"></path>\n                <path d=\"M6 6l12 12\"></path>\n            </svg>";
        closeElement.addEventListener('click', function (event) {
          event.stopPropagation();
          _this2._removeElement(_this2.toastElement);
          window.clearTimeout(_this2.toastElement.timeOutValue);
        });

        //Calculating screen width
        var width = window.innerWidth > 0 ? window.innerWidth : screen.width;
        if (this.options.position === 'left' && width > 360) {
          divElement.insertAdjacentElement('afterbegin', closeElement);
        } else {
          divElement.appendChild(closeElement);
        }
      }
      if (this.options.stopOnFocus && this.options.duration > 0) {
        divElement.addEventListener('mouseover', function (event) {
          window.clearTimeout(divElement.timeOutValue);
        });
        divElement.addEventListener('mouseleave', function () {
          divElement.timeOutValue = window.setTimeout(function () {
            _this2._removeElement(divElement);
          }, _this2.options.duration);
        });
      }
      if (typeof this.options.onClick === 'function') {
        divElement.addEventListener('click', function (event) {
          event.stopPropagation();
          _this2.options.onClick();
        });
      }
      if (_typeof(this.options.offset) === 'object') {
        var x = this._getAxisOffsetAValue('x', this.options);
        var y = this._getAxisOffsetAValue('y', this.options);
        var xOffset = this.options.position === 'left' ? x : "-".concat(x);
        var yOffset = this.options.gravity === 'toastify-top' ? y : "-".concat(y);
        divElement.style.transform = "translate(".concat(xOffset, ",").concat(yOffset, ")");
      }
      return divElement;
    }

    /**
     * Remove the toast from the DOM
     * @param {Element} toastElement
     */
  }, {
    key: "_removeElement",
    value: function _removeElement(toastElement) {
      var _this3 = this;
      toastElement.className = toastElement.className.replace(' on', '');
      window.setTimeout(function () {
        if (_this3.options.node && _this3.options.node.parentNode) {
          _this3.options.node.parentNode.removeChild(_this3.options.node);
        }
        if (toastElement.parentNode) {
          toastElement.parentNode.removeChild(toastElement);
        }
        _this3.options.callback.call(toastElement);
        _this3._reposition();
      }, 400);
    }

    /**
     * Position the toast on the DOM
     * @private
     */
  }, {
    key: "_reposition",
    value: function _reposition() {
      var topLeftOffsetSize = {
        top: 15,
        bottom: 15
      };
      var topRightOffsetSize = {
        top: 15,
        bottom: 15
      };
      var offsetSize = {
        top: 15,
        bottom: 15
      };
      var allToasts = this._rootElement.querySelectorAll('.toastify');
      var classUsed;
      for (var i = 0; i < allToasts.length; i++) {
        if (allToasts[i].classList.contains('toastify-top') === true) {
          classUsed = 'toastify-top';
        } else {
          classUsed = 'toastify-bottom';
        }
        var height = allToasts[i].offsetHeight;
        classUsed = classUsed.substr(9, classUsed.length - 1);
        var offset = 15;
        var width = window.innerWidth > 0 ? window.innerWidth : screen.width;
        if (width <= 360) {
          allToasts[i].style[classUsed] = "".concat(offsetSize[classUsed], "px");
          offsetSize[classUsed] += height + offset;
        } else {
          if (allToasts[i].classList.contains('toastify-left') === true) {
            allToasts[i].style[classUsed] = "".concat(topLeftOffsetSize[classUsed], "px");
            topLeftOffsetSize[classUsed] += height + offset;
          } else {
            allToasts[i].style[classUsed] = "".concat(topRightOffsetSize[classUsed], "px");
            topRightOffsetSize[classUsed] += height + offset;
          }
        }
      }
    }

    /**
     * Helper function to get offset
     * @param {string} axis - 'x' or 'y'
     * @param {ToastifyConfigurationObject} options - The options object containing the offset object
     */
  }, {
    key: "_getAxisOffsetAValue",
    value: function _getAxisOffsetAValue(axis, options) {
      if (options.offset[axis]) {
        if (isNaN(options.offset[axis])) {
          return options.offset[axis];
        } else {
          return "".concat(options.offset[axis], "px");
        }
      }
      return '0px';
    }
  }]);
}();
function injectCSS() {
  var element = document.createElement('style');
  element.textContent = "\n        .toastify {\n            padding: 0.75rem 2rem 0.75rem 0.75rem;\n            color: #ffffff;\n            display: flex;\n            align-items: center;\n            gap: 0.5rem;\n            box-shadow:\n                0 3px 6px -1px rgba(0, 0, 0, 0.12),\n                0 10px 36px -4px rgba(77, 96, 232, 0.3);\n            background: -webkit-linear-gradient(315deg, #73a5ff, #5477f5);\n            background: linear-gradient(135deg, #73a5ff, #5477f5);\n            position: fixed;\n            opacity: 0;\n            transition: all 0.4s cubic-bezier(0.215, 0.61, 0.355, 1);\n            border-radius: 2px;\n            cursor: pointer;\n            text-decoration: none;\n            z-index: 999999;\n            width: 25rem;\n            max-width: calc(100% - 30px);\n        }\n\n        .toastify.on {\n            opacity: 1;\n        }\n\n        .toastify-icon {\n            width: 1.5rem;\n            height: 1.5rem;\n        }\n\n        .toast-close {\n            background: transparent;\n            border: 0;\n            color: white;\n            cursor: pointer;\n            font-family: inherit;\n            font-size: 1em;\n            opacity: 0.4;\n            padding: 0 5px;\n            position: absolute;\n            top: 0.25rem;\n            inset-inline-end: 0.25rem;\n        }\n\n        .toast-close svg {\n            width: 1em;\n            height: 1em;\n        }\n\n        .toastify-text a {\n            text-decoration: underline;\n            color: #fff;\n        }\n\n        .toastify-right {\n            inset-inline-end: 15px;\n        }\n\n        .toastify-left {\n            inset-inline-start: 15px;\n        }\n\n        .toastify-top {\n            top: -150px;\n        }\n\n        .toastify-bottom {\n            bottom: -150px;\n        }\n\n        .toastify-rounded {\n            border-radius: 25px;\n        }\n\n        .toastify-center {\n            margin-inline-start: auto;\n            margin-inline-end: auto;\n            inset-inline-start: 0;\n            inset-inline-end: 0;\n            max-width: fit-content;\n            max-width: -moz-fit-content;\n        }\n\n        @media only screen and (max-width: 360px) {\n            .toastify-right,\n            .toastify-left {\n                margin-inline-start: auto;\n                margin-inline-end: auto;\n                inset-inline-start: 0;\n                inset-inline-end: 0;\n                max-width: fit-content;\n            }\n        }\n    ";
  document.head.appendChild(element);
}
injectCSS();
function StartToastifyInstance(options) {
  return new Toastify(options);
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (StartToastifyInstance);

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
/*!*************************************************!*\
  !*** ./platform/core/base/resources/js/core.js ***!
  \*************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _base_toast__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./base/toast */ "./platform/core/base/resources/js/base/toast.js");
function _readOnlyError(r) { throw new TypeError('"' + r + '" is read-only'); }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { _defineProperty(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _regenerator() { /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/babel/babel/blob/main/packages/babel-helpers/LICENSE */ var e, t, r = "function" == typeof Symbol ? Symbol : {}, n = r.iterator || "@@iterator", o = r.toStringTag || "@@toStringTag"; function i(r, n, o, i) { var c = n && n.prototype instanceof Generator ? n : Generator, u = Object.create(c.prototype); return _regeneratorDefine2(u, "_invoke", function (r, n, o) { var i, c, u, f = 0, p = o || [], y = !1, G = { p: 0, n: 0, v: e, a: d, f: d.bind(e, 4), d: function d(t, r) { return i = t, c = 0, u = e, G.n = r, a; } }; function d(r, n) { for (c = r, u = n, t = 0; !y && f && !o && t < p.length; t++) { var o, i = p[t], d = G.p, l = i[2]; r > 3 ? (o = l === n) && (u = i[(c = i[4]) ? 5 : (c = 3, 3)], i[4] = i[5] = e) : i[0] <= d && ((o = r < 2 && d < i[1]) ? (c = 0, G.v = n, G.n = i[1]) : d < l && (o = r < 3 || i[0] > n || n > l) && (i[4] = r, i[5] = n, G.n = l, c = 0)); } if (o || r > 1) return a; throw y = !0, n; } return function (o, p, l) { if (f > 1) throw TypeError("Generator is already running"); for (y && 1 === p && d(p, l), c = p, u = l; (t = c < 2 ? e : u) || !y;) { i || (c ? c < 3 ? (c > 1 && (G.n = -1), d(c, u)) : G.n = u : G.v = u); try { if (f = 2, i) { if (c || (o = "next"), t = i[o]) { if (!(t = t.call(i, u))) throw TypeError("iterator result is not an object"); if (!t.done) return t; u = t.value, c < 2 && (c = 0); } else 1 === c && (t = i["return"]) && t.call(i), c < 2 && (u = TypeError("The iterator does not provide a '" + o + "' method"), c = 1); i = e; } else if ((t = (y = G.n < 0) ? u : r.call(n, G)) !== a) break; } catch (t) { i = e, c = 1, u = t; } finally { f = 1; } } return { value: t, done: y }; }; }(r, o, i), !0), u; } var a = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} t = Object.getPrototypeOf; var c = [][n] ? t(t([][n]())) : (_regeneratorDefine2(t = {}, n, function () { return this; }), t), u = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(c); function f(e) { return Object.setPrototypeOf ? Object.setPrototypeOf(e, GeneratorFunctionPrototype) : (e.__proto__ = GeneratorFunctionPrototype, _regeneratorDefine2(e, o, "GeneratorFunction")), e.prototype = Object.create(u), e; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, _regeneratorDefine2(u, "constructor", GeneratorFunctionPrototype), _regeneratorDefine2(GeneratorFunctionPrototype, "constructor", GeneratorFunction), GeneratorFunction.displayName = "GeneratorFunction", _regeneratorDefine2(GeneratorFunctionPrototype, o, "GeneratorFunction"), _regeneratorDefine2(u), _regeneratorDefine2(u, o, "Generator"), _regeneratorDefine2(u, n, function () { return this; }), _regeneratorDefine2(u, "toString", function () { return "[object Generator]"; }), (_regenerator = function _regenerator() { return { w: i, m: f }; })(); }
function _regeneratorDefine2(e, r, n, t) { var i = Object.defineProperty; try { i({}, "", {}); } catch (e) { i = 0; } _regeneratorDefine2 = function _regeneratorDefine(e, r, n, t) { function o(r, n) { _regeneratorDefine2(e, r, function (e) { return this._invoke(r, n, e); }); } r ? i ? i(e, r, { value: n, enumerable: !t, configurable: !t, writable: !t }) : e[r] = n : (o("next", 0), o("throw", 1), o("return", 2)); }, _regeneratorDefine2(e, r, n, t); }
function asyncGeneratorStep(n, t, e, r, o, a, c) { try { var i = n[a](c), u = i.value; } catch (n) { return void e(n); } i.done ? t(u) : Promise.resolve(u).then(r, o); }
function _asyncToGenerator(n) { return function () { var t = this, e = arguments; return new Promise(function (r, o) { var a = n.apply(t, e); function _next(n) { asyncGeneratorStep(a, r, o, _next, _throw, "next", n); } function _throw(n) { asyncGeneratorStep(a, r, o, _next, _throw, "throw", n); } _next(void 0); }); }; }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }

var Botble = /*#__PURE__*/function () {
  function Botble() {
    _classCallCheck(this, Botble);
    this.initGlobalModal();
    this.countCharacter();
    this.manageSidebar();
    this.handleWayPoint();
    this.handleTurnOffDebugMode();
    Botble.initNavbarMinimal();
    Botble.initResources();
    Botble.initGlobalResources();
    Botble.handleCounterUp();
    Botble.initMediaIntegrate();
    if (BotbleVariables && BotbleVariables.authorized === '0' && typeof BotbleVariables.authorize_url !== 'undefined') {
      this.processAuthorize();
    }
    this.countMenuItemNotifications();
  }
  return _createClass(Botble, [{
    key: "initGlobalModal",
    value: function initGlobalModal() {
      $(function () {
        $('[data-bb-toggle="modal"]').on('click', function (event) {
          event.preventDefault();
          var $this = $(event.currentTarget);
          var modalType = $this.data('type');
          var isActionModal = $this.data('actionModal');
          var data = JSON.parse($this.data('payload'));
          var actionUrl = $this.data('url');
          var actionMethod = $this.data('method');
          var confirmText = $this.data('confirmText');
          var cancelText = $this.data('cancelText');
          var modalId = "global-".concat(modalType, "-modal");
          var modal = $('#' + modalId);
          var modalTitle = $this.find('.modal-replace-title').html();
          var modalDescription = $this.find('.modal-replace-description').html();
          modal.find('.mb-2 i').siblings().remove();
          modal.find('.mb-2').append(modalTitle);
          modal.find('.mb-2').append(modalDescription);
          if (!isActionModal) {
            modal.find('.modal-footer').remove();
          } else {
            var modalFooter = "\n                    <div class='modal-footer'>\n                        <div class='w-100'>\n                            <div class='row'>\n                                <div class='col'>\n                                    <button type='button' class='w-100 btn' data-bs-dismiss='modal'>".concat(cancelText, "</button>\n                                </div>\n                                <div class='col'>\n                                    <button type='button' class='w-100 btn btn-").concat(modalType, " confirm-trigger-single-action-button'>").concat(confirmText, "</button>\n                                </div>\n                            </div>\n                        </div>\n                    </div>\n                    ");
            modal.find('.modal-body').siblings().remove();
            modal.find('.modal-body').after(modalFooter);
            modal.find('.confirm-trigger-single-action-button').on('click', function () {
              $.ajax({
                type: actionMethod,
                url: actionUrl,
                data: data,
                success: function success(res) {},
                error: function error(res) {},
                complete: function complete() {
                  return modal.modal('hide');
                }
              });
            });
          }
          modal.modal('show');
        });
      });
    }
  }, {
    key: "countCharacter",
    value: function countCharacter() {
      $.fn.charCounter = function (max, settings) {
        max = max || 100;
        settings = $.extend({
          container: '<span></span>',
          classname: 'charcounter',
          format: "(%1/%2)",
          pulse: true,
          delay: 0,
          allowOverLimit: false
        }, settings);
        var p, timeout;
        var count = function count(el, container) {
          el = $(el);
          var current = el.val().length;
          var remaining = max - el.val().length;
          container.html(settings.format.replace(/%1/, current).replace(/%2/, max));
          container.toggleClass('text-danger', el.val().length > max);
          if (!settings.allowOverLimit && el.val().length > max) {
            el.val(el.val().substring(0, max));
            if (settings.pulse && !p) {
              _pulse(container, true);
            }
          }
          if (settings.delay > 0) {
            if (timeout) {
              window.clearTimeout(timeout);
            }
            timeout = window.setTimeout(function () {
              container.html(settings.format.replace(/%1/, remaining).replace(/%2/, max));
            }, settings.delay);
          }
        };
        var _pulse = function pulse(el, again) {
          if (p) {
            window.clearTimeout(p);
            p = null;
          }
          el.animate({
            opacity: 0.1
          }, 100, function () {
            $(el).animate({
              opacity: 1.0
            }, 100);
          });
          if (again) {
            p = window.setTimeout(function () {
              return _pulse(el);
            }, 200);
          }
        };
        return this.each(function (index, el) {
          var container;
          if (!settings.container.match(/^<.+>$/)) {
            container = $(settings.container);
          } else {
            $(el).nextAll('.' + settings.classname).remove();
            container = $(settings.container).insertAfter(el).addClass(settings.classname);
          }
          $(el).off('.charCounter').on('keydown.charCounter', function () {
            count(el, container);
          }).on('keypress.charCounter', function () {
            count(el, container);
          }).on('keyup.charCounter', function () {
            count(el, container);
          }).on('focus.charCounter', function () {
            count(el, container);
          }).on('mouseover.charCounter', function () {
            count(el, container);
          }).on('mouseout.charCounter', function () {
            count(el, container);
          }).on('paste.charCounter', function () {
            setTimeout(function () {
              count(el, container);
            }, 10);
          });
          if (el.addEventListener) {
            el.addEventListener('input', function () {
              count(el, container);
            }, false);
          }
          count(el, container);
        });
      };
      $(document).on('click', 'input[data-counter], textarea[data-counter]', function (event) {
        var $this = $(event.currentTarget);
        $(event.currentTarget).charCounter($this.data('counter'), {
          container: '<small></small>',
          allowOverLimit: $this.data('allow-over-limit') == '' ? true : false
        });
      });
    }
  }, {
    key: "manageSidebar",
    value: function manageSidebar() {
      var body = $('body');
      var navigation = $('.navigation');
      var sidebar_content = $('.sidebar-content');
      navigation.find('li.active').parents('li').addClass('active');
      navigation.find('li').has('ul').children('a').parent('li').addClass('has-ul');
      $(document).on('click', '.sidebar-toggle.d-none', function (event) {
        event.preventDefault();
        body.toggleClass('sidebar-narrow');
        body.toggleClass('page-sidebar-closed');
        if (body.hasClass('sidebar-narrow')) {
          navigation.children('li').children('ul').css('display', '');
          sidebar_content.delay().queue(function () {
            $(event.currentTarget).show().addClass('animated fadeIn').clearQueue();
          });
        } else {
          navigation.children('li').children('ul').hide();
          navigation.children('li.active').children('ul').show();
          sidebar_content.delay().queue(function () {
            $(event.currentTarget).show().addClass('animated fadeIn').clearQueue();
          });
        }
      });
    }
  }, {
    key: "handleWayPoint",
    value: function handleWayPoint() {
      $(document).find('[data-bb-waypoint]').each(function () {
        var target = $($(this).data('bb-target'));
        new Waypoint({
          element: $(this),
          handler: function handler(direction) {
            if (direction === 'down') {
              target.show();
            } else {
              target.hide();
            }
          }
        });
      });
    }
  }, {
    key: "handleTurnOffDebugMode",
    value: function handleTurnOffDebugMode() {
      var debugConfirmationModal = $(document).find('#debug-mode-turn-off-confirmation-modal');
      if (!debugConfirmationModal.length) {
        return;
      }
      var $submit = debugConfirmationModal.find('#debug-mode-turn-off-form-submit');
      if (!$submit.length) {
        return;
      }
      $submit.on('click', function (event) {
        event.preventDefault();
        Botble.showButtonLoading($submit[0]);
        $httpClient.make().post($submit.data('url')).then(function (_ref) {
          var data = _ref.data;
          Botble.showSuccess(data.message);
          debugConfirmationModal.modal('hide');
          setTimeout(function () {
            window.location.reload();
          }, 1000);
        })["finally"](function () {
          Botble.hideButtonLoading($submit[0]);
        });
      });
    }
  }, {
    key: "processAuthorize",
    value: function processAuthorize() {
      // Check if we should make the membership authorization request
      var shouldMakeAuthRequest = function shouldMakeAuthRequest() {
        var lastAuthTime = localStorage.getItem('membership_authorization_time');
        if (!lastAuthTime) {
          return true;
        }

        // Call once every 3 days (259200000 ms)
        var threeDaysInMs = 3 * 24 * 60 * 60 * 1000;
        return Date.now() - parseInt(lastAuthTime) > threeDaysInMs;
      };
      if (!shouldMakeAuthRequest()) {
        return;
      }
      $httpClient.makeWithoutErrorHandler().post(BotbleVariables.authorize_url).then(function () {
        // Store the current time as the last authorization time
        localStorage.setItem('membership_authorization_time', Date.now().toString());
      })["catch"](function () {
        // Even on error, we've made the request, so store the time
        localStorage.setItem('membership_authorization_time', Date.now().toString());
      });
    }
  }, {
    key: "countMenuItemNotifications",
    value: function countMenuItemNotifications() {
      var _this = this;
      var $menuItems = $('.menu-item-count');
      if ($menuItems.length) {
        // Check if we should make the menu items count request
        var shouldCheckMenuItemsCount = function shouldCheckMenuItemsCount() {
          var lastCheckTime = localStorage.getItem('menu_items_count_check_time');
          if (!lastCheckTime) {
            return true;
          }

          // Call once every 3 minutes (180000 ms)
          var threeMinutesInMs = 3 * 60 * 1000;
          return Date.now() - parseInt(lastCheckTime) > threeMinutesInMs;
        };

        // Try to get cached menu items count data
        var cachedMenuItemsCount = localStorage.getItem('menu_items_count_data');
        if (cachedMenuItemsCount && !shouldCheckMenuItemsCount()) {
          try {
            var cachedData = JSON.parse(cachedMenuItemsCount);
            this.updateMenuItemsCount(cachedData);
            return;
          } catch (e) {
            // If there's an error parsing the cached data, proceed with the request
          }
        }
        if (!shouldCheckMenuItemsCount()) {
          return;
        }
        $httpClient.make().get($menuItems.data('url') || BotbleVariables.menu_item_count_url).then(function (_ref2) {
          var data = _ref2.data;
          // Store the current time as the last check time
          localStorage.setItem('menu_items_count_check_time', Date.now().toString());

          // Store the menu items count data
          localStorage.setItem('menu_items_count_data', JSON.stringify(data.data));
          _this.updateMenuItemsCount(data.data);
        })["catch"](function () {
          // Even on error, we've made the request, so store the time
          localStorage.setItem('menu_items_count_check_time', Date.now().toString());
        });
      }
    }
  }, {
    key: "updateMenuItemsCount",
    value: function updateMenuItemsCount(data) {
      if (!data) {
        return;
      }
      data.map(function (x) {
        if (x.value > 0) {
          $(".menu-item-count.".concat(x.key)).text(x.value).show().removeClass('hidden');
        }
      });
    }
  }], [{
    key: "initCoreIcon",
    value: function initCoreIcon() {
      var $coreIcon = $(document).find('[data-bb-core-icon]');
      var formatTemplate = function formatTemplate(_ref3) {
        var id = _ref3.id,
          text = _ref3.text;
        if (typeof id === 'undefined') {
          id = '';
        }
        return $("<span><span class=\"dropdown-item-indicator\">".concat(text, "</span> ").concat(id, "</span>"));
      };
      Botble.select($coreIcon, {
        ajax: {
          url: $coreIcon.data('url'),
          delay: 250,
          cache: true,
          data: function data(params) {
            return {
              q: params.term,
              page: params.page || 1
            };
          },
          processResults: function processResults(_ref4) {
            var data = _ref4.data;
            return {
              results: $.map(data.data, function (icon, name) {
                return {
                  text: icon,
                  id: name
                };
              }),
              pagination: {
                more: data.next_page_url && Object.keys(data.data).length > 0
              }
            };
          }
        },
        placeholder: $coreIcon.data('placeholder'),
        templateResult: formatTemplate,
        templateSelection: formatTemplate
      }, true);
    }
  }, {
    key: "blockUI",
    value: function blockUI(options) {
      options = options || {};
      Botble.showLoading(options.target);
    }
  }, {
    key: "unblockUI",
    value: function unblockUI(target) {
      Botble.hideLoading(target);
    }
  }, {
    key: "showNotice",
    value: function showNotice(messageType, message) {
      var messageHeader = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : '';
      var key = "notices_msg.".concat(messageType, ".").concat(message);
      var color = '';
      var icon = '';
      if (Botble.noticesTimeout[key]) {
        clearTimeout(Botble.noticesTimeout[key]);
      }
      Botble.noticesTimeout[key] = setTimeout(function () {
        if (!messageHeader) {
          switch (messageType) {
            case 'error':
              messageHeader = BotbleVariables.languages.notices_msg.error;
              break;
            case 'success':
              messageHeader = BotbleVariables.languages.notices_msg.success;
              break;
          }
        }
        switch (messageType) {
          case 'error':
            color = '#f44336';
            icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>';
            break;
          case 'success':
            color = '#4caf50';
            icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>';
            break;
        }
        (0,_base_toast__WEBPACK_IMPORTED_MODULE_0__["default"])({
          text: message,
          duration: 5000,
          close: true,
          gravity: 'bottom',
          position: 'right',
          stopOnFocus: true,
          escapeMarkup: false,
          icon: icon,
          style: {
            background: color
          }
        }).showToast();
      }, Botble.noticesTimeoutCount);
    }
  }, {
    key: "showError",
    value: function showError(message) {
      var messageHeader = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
      this.showNotice('error', message, messageHeader);
    }
  }, {
    key: "showSuccess",
    value: function showSuccess(message) {
      var messageHeader = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
      this.showNotice('success', message, messageHeader);
    }
  }, {
    key: "handleError",
    value: function handleError(data) {
      if (typeof data.errors !== 'undefined' && !_.isArray(data.errors)) {
        Botble.handleValidationError(data.errors);
      } else {
        if (typeof data.responseJSON !== 'undefined') {
          if (typeof data.responseJSON.errors !== 'undefined') {
            if (data.status === 422) {
              Botble.handleValidationError(data.responseJSON.errors);
            }
          } else if (typeof data.responseJSON.message !== 'undefined') {
            Botble.showError(data.responseJSON.message);
          } else {
            $.each(data.responseJSON, function (index, el) {
              $.each(el, function (key, item) {
                Botble.showError(item);
              });
            });
          }
        } else {
          Botble.showError(data.statusText);
        }
      }
    }
  }, {
    key: "handleDatatableError",
    value: function handleDatatableError(error) {
      var errorMessage = BotbleVariables.languages.tables.error_loading ? BotbleVariables.languages.tables.error_loading : 'An error occurred while loading the data. Please refresh the page and try again.';
      if (typeof error.responseJSON !== 'undefined') {
        if (typeof error.responseJSON.message !== 'undefined') {
          // Remove any sensitive information from error message
          errorMessage = error.responseJSON.message.replace(/table\s+[a-zA-Z0-9_-]+/g, 'table').replace('botble-', '').replace(/column\s+['"]\w+['"]/g, 'column').replace(/parameter\s+['"]\w+['"]/g, 'parameter').replace(/row\s+\d+/g, 'row').replace(/\s+for\s+\d+/g, '').replace(/\s+\d+,\s+/g, ' ').replace(/\s+\d+\./g, '.');
        }
      }
      Botble.showError(errorMessage);
    }
  }, {
    key: "handleValidationError",
    value: function handleValidationError(errors) {
      var message = '';
      $.each(errors, function (index, item) {
        message += item + '\n';
      });
      Botble.showError(message);
    }
  }, {
    key: "callScroll",
    value: function callScroll(obj) {
      obj.mCustomScrollbar({
        theme: 'dark',
        scrollInertia: 0,
        callbacks: {
          whileScrolling: function whileScrolling() {
            obj.find('.tableFloatingHeaderOriginal').css({
              top: -this.mcs.top + 'px'
            });
          }
        }
      });
      obj.stickyTableHeaders({
        scrollableArea: obj,
        fixedOffset: 2
      });
    }
  }, {
    key: "copyToClipboard",
    value: function () {
      var _copyToClipboard = _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee(textToCopy, parentTarget) {
        return _regenerator().w(function (_context) {
          while (1) switch (_context.n) {
            case 0:
              if (!(navigator.clipboard && window.isSecureContext)) {
                _context.n = 2;
                break;
              }
              _context.n = 1;
              return navigator.clipboard.writeText(textToCopy);
            case 1:
              _context.n = 3;
              break;
            case 2:
              Botble.unsecuredCopyToClipboard(textToCopy, parentTarget);
            case 3:
              return _context.a(2);
          }
        }, _callee);
      }));
      function copyToClipboard(_x, _x2) {
        return _copyToClipboard.apply(this, arguments);
      }
      return copyToClipboard;
    }()
  }, {
    key: "unsecuredCopyToClipboard",
    value: function unsecuredCopyToClipboard(textToCopy, parentTarget) {
      parentTarget = parentTarget || document.body;
      var textArea = document.createElement('textarea');
      textArea.value = textToCopy;
      textArea.style.position = 'absolute';
      textArea.style.left = '-999999px';
      parentTarget.append(textArea);
      textArea.select();
      try {
        document.execCommand('copy');
      } catch (error) {
        console.error('Unable to copy to clipboard', error);
      }
      parentTarget.removeChild(textArea);
    }
  }, {
    key: "initNavbarMinimal",
    value: function initNavbarMinimal() {
      $(document).on('click', '[data-bb-toggle="navbar-minimal"]', function (event) {
        var _self = $(event.currentTarget);
        var navbar = $(_self.data('bb-target'));
        if (navbar.length > 0) {
          navbar.toggleClass('navbar-minimal');
          $httpClient.makeWithoutErrorHandler().post(_self.data('url'), {
            _method: _self.data('method'),
            minimal_sidebar: navbar.hasClass('navbar-minimal') ? 'yes' : 'no'
          }).then(function () {})["catch"](function () {});
        }
      });
    }
  }, {
    key: "initDatePicker",
    value: function initDatePicker(element) {
      if (jQuery().flatpickr) {
        var $element = $(document).find(element);
        var $input = $element.find('input');
        var format = $input.data('date-format');
        if (!format) {
          format = 'Y-m-d';
        }
        var locale = window.siteEditorLocale;
        if (locale === 'vi') {
          locale = 'vn';
        }
        var options = {
          dateFormat: format,
          wrap: true,
          locale: locale || 'en'
        };
        if ($input.data('options')) {
          options = Object.assign(options, $input.data('options'));
        }
        $element.flatpickr(options);
      }
    }
  }, {
    key: "initResources",
    value: function initResources() {
      $(document).on('click', '[data-bb-toggle="check-all"]', function (e) {
        var currentTarget = $(e.currentTarget);
        var targets = $(currentTarget.data('bb-target'));
        if (currentTarget.prop('checked')) {
          targets.prop('checked', true);
          currentTarget.prop('checked', false);
        } else {
          targets.prop('checked', false);
          currentTarget.prop('checked', true);
        }
      });
      $(document).find('[data-bb-toggle="check-all"]').each(function (index, element) {
        var $checkboxChildren = $(document).find($(element).attr('data-target'));
        var $parent = $(element).find('input[type=checkbox]');
        if ($checkboxChildren.length === $checkboxChildren.filter(':checked').length) {
          $parent.prop('indeterminate', false);
          $parent.prop('checked', true);
        } else {
          $parent.prop('indeterminate', true);
        }
      });
      $(document).find('[data-bb-toggle="check-all"]').each(function (index, element) {
        $(document).on('click', $(element).attr('data-target'), function () {
          var $checkboxChildren = $(document).find($(element).attr('data-target'));
          var $parent = $(element).find('input[type=checkbox]');
          if ($checkboxChildren.length === $checkboxChildren.filter(':checked').length) {
            $parent.prop('indeterminate', false);
            $parent.prop('checked', true);
          } else {
            $parent.prop('indeterminate', true);
          }
        });
      });
      $(document).on('change', '.check-all', function (event) {
        var _self = $(event.currentTarget);
        var set = _self.attr('data-set');
        var checked = _self.find('input').prop('checked');
        $(set).each(function (index, el) {
          if (checked) {
            $(el).prop('checked', true);
          } else {
            $(el).prop('checked', false);
          }
        });
      });
      $(document).find('.check-all').each(function (index, element) {
        $(document).on('click', $(element).attr('data-set'), function () {
          var $checkboxChildren = $(document).find($(element).attr('data-set'));
          var $parent = $(element).find('input[type=checkbox]');
          if ($checkboxChildren.length === $checkboxChildren.filter(':checked').length) {
            $parent.prop('indeterminate', false);
            $parent.prop('checked', true);
          } else {
            $parent.prop('indeterminate', true);
          }
        });
      });
      $(document).find('.check-all').each(function (index, element) {
        var $checkboxChildren = $(document).find($(element).attr('data-set'));
        var $parent = $(element).find('input[type=checkbox]');
        if ($checkboxChildren.length === $checkboxChildren.filter(':checked').length) {
          $parent.prop('indeterminate', false);
          $parent.prop('checked', true);
        } else {
          $parent.prop('indeterminate', true);
        }
      });
      $.each($(document).find('select.select-search-full'), function (index, element) {
        Botble.select(element);
      });
      $.each($(document).find('select.select-full'), function (index, element) {
        Botble.select(element, {
          controlInput: null
        });
      });
      $(document).find('select.select-autocomplete').each(function (index, element) {
        var $element = $(element);
        Botble.select(element, {
          minimumInputLength: $element.data('minimum-input') || 1,
          width: '100%',
          delay: 250,
          ajax: {
            url: $element.data('url'),
            data: function data(params) {
              return {
                q: params.term,
                page: params.page || 1
              };
            },
            dataType: 'json',
            type: $element.data('type') || 'GET',
            processResults: function processResults(response) {
              return {
                results: $.map(response.data, function (item) {
                  return Object.assign({
                    text: item.name,
                    id: item.id
                  }, item);
                }),
                pagination: {
                  more: response.links ? response.links.next : null
                }
              };
            },
            cache: true
          }
        });
      });
      $.each($(document).find('.select-multiple'), function (index, element) {
        var $element = $(element);
        Botble.select(element, {
          allowClear: $element.data('allow-clear'),
          placeholder: $element.data('placeholder')
        });
        if ($(this).hasClass('.select-sorting')) {
          $(this).on('select2:select', function (e) {
            var $element = $(e.params.data.element);
            $element.detach();
            $(this).append($element);
            $(this).trigger('change');
          });
        }
      });
      $.each($(document).find('.select-search-ajax'), function (index, element) {
        var $element = $(element);
        if ($element.data('url')) {
          var options = {
            placeholder: $element.data('placeholder') || '--Select--',
            minimumInputLength: $element.data('minimum-input') || 1,
            width: '100%',
            delay: 250,
            ajax: {
              url: $element.data('url'),
              dataType: 'json',
              type: $element.data('type') || 'GET',
              quietMillis: 50,
              data: function data(params) {
                // Query parameters will be ?search=[term]&page=[page]
                return {
                  search: params.term,
                  page: params.page || 1
                };
              },
              processResults: function processResults(response) {
                /**
                 * response {
                 *  error: false
                 *  data: {},
                 *  message: ''
                 * }
                 */
                var data = Array.isArray(response.data) ? response.data : response.data.data;
                return {
                  results: $.map(data, function (item) {
                    return {
                      text: item.name,
                      id: item.id
                    };
                  }),
                  pagination: {
                    more: response.links ? response.links.next : null
                  }
                };
              },
              cache: true
            },
            allowClear: true
          };
          Botble.select(element, options);
          var selected = $element.data('selected');
          if (typeof selected !== 'undefined' && Object.keys(selected).length > 0) {
            Object.keys(selected).forEach(function (key) {
              var option = new Option(selected[key], key, true, true);
              $element.append(option).trigger('change');
            });
          }
        }
      });
      $(document).find('[data-bb-toggle="google-font-selector"]').each(function (i, element) {
        if (!$(element).hasClass('select2-hidden-accessible')) {
          var options = {
            templateResult: function templateResult(opt) {
              if (!opt.id) {
                return opt.text;
              }
              return $('<span style="font-family:\'' + opt.id + '\';"> ' + opt.text + '</span>');
            },
            width: '100%'
          };
          Botble.select(element, options);
        }
      });
      if (jQuery().timepicker) {
        $(document).find('.timepicker-default').timepicker({
          autoclose: true,
          showSeconds: false,
          minuteStep: 1,
          defaultTime: false
        });
        $(document).find('.timepicker-24').timepicker({
          autoclose: true,
          minuteStep: 5,
          showSeconds: false,
          showMeridian: false,
          defaultTime: false,
          icons: {
            up: 'icon fa fa-chevron-up',
            down: 'icon fa fa-chevron-down'
          }
        });
      }
      if (jQuery().inputmask) {
        $.each($(document).find('.input-mask-number'), function (index, element) {
          var _$$data, _$$data2, _$$data3, _$$data4;
          $(element).inputmask({
            alias: 'numeric',
            rightAlign: false,
            digits: (_$$data = $(element).data('digits')) !== null && _$$data !== void 0 ? _$$data : 5,
            groupSeparator: (_$$data2 = $(element).data('thousands-separator')) !== null && _$$data2 !== void 0 ? _$$data2 : ',',
            radixPoint: (_$$data3 = $(element).data('decimal-separator')) !== null && _$$data3 !== void 0 ? _$$data3 : '.',
            digitsOptional: true,
            placeholder: (_$$data4 = $(element).data('placeholder')) !== null && _$$data4 !== void 0 ? _$$data4 : '0',
            autoGroup: true,
            autoUnmask: true,
            removeMaskOnSubmit: true
          });
        });
      }
      if (jQuery().colorpicker) {
        $.each($(document).find('.color-picker'), function (index, element) {
          $(element).colorpicker({
            popover: false,
            inline: false,
            container: true,
            format: 'hex',
            extensions: [{
              name: 'swatches',
              options: {
                colors: {
                  tetrad1: '#000000',
                  tetrad2: '#000000',
                  tetrad3: '#000000',
                  tetrad4: '#000000'
                },
                namesAsValues: false
              }
            }]
          }).on('colorpickerChange colorpickerCreate', function (e) {
            var colors = e.color.generate('tetrad');
            colors.forEach(function (color, i) {
              var colorStr = color.string(),
                swatch = e.colorpicker.picker.find('.colorpicker-swatch[data-name="tetrad' + (i + 1) + '"]');
              swatch.attr('data-value', colorStr).attr('title', colorStr).find('> i').css('background-color', colorStr);
            });
          });
        });
      }
      if (jQuery().fancybox) {
        $('.iframe-btn').fancybox({
          width: '900px',
          height: '700px',
          type: 'iframe',
          autoScale: false,
          openEffect: 'none',
          closeEffect: 'none',
          overlayShow: true,
          overlayOpacity: 0.7
        });
        $('.fancybox').fancybox({
          openEffect: 'none',
          closeEffect: 'none',
          overlayShow: true,
          overlayOpacity: 0.7,
          helpers: {
            media: {}
          }
        });
      }
      if (jQuery().tooltip) {
        $('[data-bs-toggle="tooltip"]').tooltip({
          placement: 'top',
          boundary: 'window'
        });
      }
      if (jQuery().areYouSure) {
        $('form.dirty-check').areYouSure();
      }
      function urlify(text) {
        var urlRegex = /(https?:\/\/[^\s]+)/g;
        if (text.includes('<a ') || text.includes('</a>') || text.includes(' href=') || text.includes('target="_blank"') || text.includes('<img src="')) {
          return text;
        }
        return text.replace(urlRegex, function (url) {
          return '<a href="' + url + '" target="_blank">' + url + '</a>';
        });
      }
      $.each($(document).find('.form-hint'), function (index, element) {
        $(element).html(urlify($(element).html()));
      });
      Botble.initDatePicker('.datepicker');
      if (jQuery().textareaAutoSize) {
        $('textarea.textarea-auto-height').textareaAutoSize();
      }
      Botble.initCodeEditorComponent();
      Botble.initColorPicker();
      Botble.initLightbox();
      Botble.initTreeCategoriesSelect();
      Botble.initCoreIcon();
      document.dispatchEvent(new CustomEvent('core-init-resources'));
    }
  }, {
    key: "initGlobalResources",
    value: function initGlobalResources() {
      $(document).on('submit', '.js-base-form', function (event) {
        $(event.currentTarget).find('button[type=submit]').addClass('disabled');
      });
      $(document).on('change', '.media-image-input', function () {
        var input = this;
        if (input.files && input.files.length > 0) {
          var reader = new FileReader();
          reader.onload = function (e) {
            $(input).closest('.image-box').find('.preview-image').prop('src', e.target.result);
          };
          reader.readAsDataURL(input.files[0]);
        }
      });
      $(document).on('click', '.media-select-file', function (event) {
        event.preventDefault();
        event.stopPropagation();
        $(this).closest('.attachment-wrapper').find('.media-file-input').trigger('click');
      });
      Botble.initFieldCollapse();
      Botble.initTreeCheckboxes();
      Botble.initClipboard();
      Botble.initDropdownCheckboxes();
    }
  }, {
    key: "numberFormat",
    value: function numberFormat(number, decimals, dec_point, thousands_sep) {
      // *     example 1: number_format(1234.56);
      // *     returns 1: '1,235'
      // *     example 2: number_format(1234.56, 2, ',', ' ');
      // *     returns 2: '1 234,56'
      // *     example 3: number_format(1234.5678, 2, '.', '');
      // *     returns 3: '1234.57'
      // *     example 4: number_format(67, 2, ',', '.');
      // *     returns 4: '67,00'
      // *     example 5: number_format(1000);
      // *     returns 5: '1,000'
      // *     example 6: number_format(67.311, 2);
      // *     returns 6: '67.31'
      // *     example 7: number_format(1000.55, 1);
      // *     returns 7: '1,000.6'
      // *     example 8: number_format(67000, 5, ',', '.');
      // *     returns 8: '67.000,00000'
      // *     example 9: number_format(0.9, 0);
      // *     returns 9: '1'
      // *    example 10: number_format('1.20', 2);
      // *    returns 10: '1.20'
      // *    example 11: number_format('1.20', 4);
      // *    returns 11: '1.2000'
      // *    example 12: number_format('1.2000', 3);
      // *    returns 12: '1.200'
      var n = !isFinite(+number) ? 0 : +number,
        precision = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = typeof thousands_sep === 'undefined' ? ',' : thousands_sep,
        dec = typeof dec_point === 'undefined' ? '.' : dec_point,
        toFixedFix = function toFixedFix(n, precision) {
          // Fix for IE parseFloat(0.55).toFixed(0) = 0;
          var k = Math.pow(10, precision);
          return Math.round(n * k) / k;
        },
        s = (precision ? toFixedFix(n, precision) : Math.round(n)).toString().split('.');
      if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
      }
      if ((s[1] || '').length < precision) {
        s[1] = s[1] || '';
        s[1] += new Array(precision - s[1].length + 1).join('0');
      }
      return s.join(dec);
    }
  }, {
    key: "handleCounterUp",
    value: function handleCounterUp() {
      if (!$().counterUp) {
        return;
      }
      $('[data-counter="counterup"]').counterUp({
        delay: 10,
        time: 1000
      });
    }
  }, {
    key: "openMediaUsing",
    value: function openMediaUsing(callback) {}
  }, {
    key: "handleOpenMedia",
    value: function handleOpenMedia(item) {}
  }, {
    key: "initMediaIntegrate",
    value: function initMediaIntegrate() {
      if (jQuery().rvMedia) {
        Botble.gallerySelectImageTemplate = "\n            <div class='custom-image-box image-box'>\n                <input type='hidden' name='__name__' value='' class='image-data'>\n                    <div class='preview-image-wrapper w-100'>\n                    <div class='preview-image-inner'>\n                        <img src='".concat(RV_MEDIA_CONFIG.default_image, "' alt='").concat(RV_MEDIA_CONFIG.translations.preview_image, "' class='preview-image'>\n                        <div class='image-picker-backdrop'></div>\n                        <span class='image-picker-remove-button'>\n                            <button data-bb-toggle='image-picker-remove' class='btn btn-sm btn-icon'>\n                                <svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon icon-sm icon-left\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\">\n                                  <path stroke=\"none\" d=\"M0 0h24v24H0z\" fill=\"none\"/>\n                                  <path d=\"M18 6l-12 12\" />\n                                  <path d=\"M6 6l12 12\" />\n                                </svg>\n                            </button>\n                        </span>\n                        <div data-bb-toggle='image-picker-edit' class='image-box-actions cursor-pointer'></div>\n                    </div>\n                </div>\n            </div>");
        var $btnGalleries = $('.btn_gallery');
        if ($btnGalleries.length > 0) {
          $btnGalleries.each(function () {
            var item = $(this);
            $(item).rvMedia({
              multiple: false,
              filter: $(item).data('action') === 'select-image' ? 'image' : 'everything',
              view_in: 'all_media',
              onSelectFiles: function onSelectFiles(files, $el) {
                switch ($el.data('action')) {
                  case 'media-insert-ckeditor':
                    var content = '';
                    $.each(files, function (index, file) {
                      var link = file.full_url;
                      if (file.type === 'youtube') {
                        link = link.replace('watch?v=', 'embed/');
                        content += '<iframe width="420" height="315" src="' + link + '" frameborder="0" allowfullscreen loading="lazy"></iframe><br />';
                      } else if (file.type === 'image') {
                        var alt = file.alt ? file.alt : file.name;
                        content += '<img src="' + link + '" alt="' + alt + '" loading="lazy"/><br />';
                      } else {
                        content += '<a href="' + link + '">' + file.name + '</a><br />';
                      }
                    });
                    window.EDITOR.CKEDITOR[$el.data('result')].insertHtml(content);
                    break;
                  case 'media-insert-tinymce':
                    var html = '';
                    $.each(files, function (index, file) {
                      var link = file.full_url;
                      if (file.type === 'youtube') {
                        link = link.replace('watch?v=', 'embed/');
                        html += "<iframe width='420' height='315' src='".concat(link, "' allowfullscreen loading='lazy'></iframe><br />");
                      } else if (file.type === 'image') {
                        var alt = file.alt ? file.alt : file.name;
                        html += "<img src='".concat(link, "' alt='").concat(alt, "' loading='lazy'/><br />");
                      } else {
                        html += "<a href='".concat(link, "'>").concat(file.name, "</a><br />");
                      }
                    });
                    tinymce.activeEditor.execCommand('mceInsertContent', false, html);
                    break;
                  case 'select-image':
                    var firstImage = _.first(files);
                    var $imageBox = $el.closest('.image-box');
                    var allowThumb = $el.data('allow-thumb');
                    $imageBox.find('.image-data').val(firstImage.url).trigger('change');
                    $imageBox.find('.preview-image').attr('src', allowThumb && firstImage.thumb ? firstImage.thumb : firstImage.full_url);
                    $imageBox.find('[data-bb-toggle="image-picker-remove"]').show();
                    $imageBox.find('.preview-image').removeClass('default-image');
                    $imageBox.find('.preview-image-wrapper').show();
                    break;
                  case 'attachment':
                    var attachment = _.first(files);
                    var wrapper = $el.closest('.attachment-wrapper');
                    wrapper.find('.attachment-url').val(attachment.url);
                    wrapper.find('.attachment-info').html("\n                                        <a href=\"".concat(attachment.full_url, "\" target=\"_blank\" title=\"").concat(attachment.name, "\">").concat(attachment.url, "</a>\n                                        <small class=\"d-block\">").concat(attachment.size, "</small>\n                                    "));
                    wrapper.find('[data-bb-toggle="media-file-remove"]').show();
                    wrapper.find('.attachment-details').removeClass('hidden');
                    break;
                  default:
                    var coreInsertMediaEvent = new CustomEvent('core-insert-media', {
                      detail: {
                        files: files,
                        element: $el
                      }
                    });
                    document.dispatchEvent(coreInsertMediaEvent);
                }
              }
            });
          });
        }
        var gallerySelectImages = function gallerySelectImages(files, $currentBoxList) {
          var excludeIndexes = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : [];
          var template = Botble.gallerySelectImageTemplate;
          var allowThumb = $currentBoxList.data('allow-thumb');
          _.forEach(files, function (file, index) {
            if (_.includes(excludeIndexes, index)) {
              return;
            }
            var imageBox = template.replace(/__name__/gi, $currentBoxList.data('name'));
            var $template = $('<div class="col-lg-2 col-md-3 col-4 gallery-image-item-handler mb-2">' + imageBox + '</div>');
            $template.find('.image-data').val(file.url).trigger('change');
            $template.find('.preview-image').attr('src', allowThumb ? file.thumb : file.full_url).show();
            if (!allowThumb) {
              $template.find('.preview-image-wrapper').addClass('preview-image-wrapper-not-allow-thumb');
            }
            $currentBoxList.append($template);
            $('.list-images').find('.footer-action').show();
          });
        };
        new RvMediaStandAlone('[data-bb-toggle="gallery-add"]', {
          filter: 'image',
          view_in: 'all_media',
          onSelectFiles: function onSelectFiles(files, $el) {
            var $currentBoxList = $el.closest('.gallery-images-wrapper').find('.images-wrapper .list-gallery-media-images');
            $currentBoxList.removeClass('hidden');
            $('.default-placeholder-gallery-image').addClass('hidden');
            gallerySelectImages(files, $currentBoxList);
          }
        });
        new RvMediaStandAlone('[data-bb-toggle="image-picker-edit"]', {
          filter: 'image',
          view_in: 'all_media',
          onSelectFiles: function onSelectFiles(files, $el) {
            var firstItem = _.first(files);
            var $currentBox = $el.closest('.gallery-image-item-handler').find('.image-box');
            var $currentBoxList = $el.closest('.list-gallery-media-images');
            var allowThumb = $currentBoxList.data('allow-thumb');
            $currentBox.find('.image-data').val(firstItem.url).trigger('change');
            $currentBox.find('.preview-image').attr('src', allowThumb ? firstItem.thumb : firstItem.full_url).show();
            gallerySelectImages(files, $currentBoxList, [0]);
          }
        });
        $.each($(document).find('[data-bb-toggle="image-picker-choose"][data-target="popup"]'), function (index, item) {
          var _self = $(item);
          _self.rvMedia({
            multiple: false,
            filter: 'image',
            view_in: 'all_media',
            onSelectFiles: function onSelectFiles(files, $el) {
              var firstImage = _.first(files);
              var $imageBox = $el.closest('.image-box');
              var allowThumb = $el.data('allow-thumb');
              $imageBox.find('.image-data').val(firstImage.url).trigger('change');
              $imageBox.find('.preview-image').attr('src', allowThumb && firstImage.thumb ? firstImage.thumb : firstImage.full_url);
              $imageBox.find('[data-bb-toggle="image-picker-remove"]').show();
              $imageBox.find('.preview-image').removeClass('default-image');
              $imageBox.find('.preview-image-wrapper').show();
              var coreInsertMediaEvent = new CustomEvent('core-insert-media', {
                detail: {
                  files: files,
                  element: $el
                }
              });
              document.dispatchEvent(coreInsertMediaEvent);
            }
          });
        });
      }
      $(document).on('click', '[data-bb-toggle="image-picker-choose"][data-target="direct"]', function (event) {
        event.preventDefault();
        event.stopPropagation();
        $(event.currentTarget).closest('.image-box').find('.media-image-input').trigger('click');
      });
      $(document).on('show.bs.modal', '#image-picker-add-from-url', function (event) {
        var relatedTarget = $(event.relatedTarget);
        var imageBoxTarget = relatedTarget.data('bb-target');
        var modal = $(event.currentTarget);
        modal.find('input[name="image-box-target"]').val(imageBoxTarget);
      });
      $(document).on('submit', '#image-picker-add-from-url-form', function (event) {
        event.preventDefault();
        var form = $(event.currentTarget);
        var modal = form.closest('.modal');
        var button = modal.find('button[type="submit"]');
        var imageUrl = form.find('input[name="url"]').val();
        if (form.find('#download_image_to_local_storage').is(':checked')) {
          $httpClient.make().withButtonLoading(button).post(form.prop('action'), {
            url: imageUrl,
            folderId: 0
          }).then(function (_ref5) {
            var data = _ref5.data;
            form[0].reset();
            modal.modal('hide');
            var $imageBox = $(form.find('input[name="image-box-target"]').val());
            $imageBox.find('.image-data').val(data.data.url).trigger('change');
            $imageBox.find('.preview-image').prop('src', data.data.src);
            $imageBox.find('[data-bb-toggle="image-picker-remove"]').show();
            $imageBox.find('.preview-image').removeClass('default-image');
            $imageBox.find('.preview-image-wrapper').show();
          });
        } else {
          form[0].reset();
          modal.modal('hide');
          var $imageBox = $(form.find('input[name="image-box-target"]').val());
          $imageBox.find('.image-data').val(imageUrl).trigger('change');
          $imageBox.find('.preview-image').prop('src', imageUrl);
          $imageBox.find('[data-bb-toggle="image-picker-remove"]').show();
          $imageBox.find('.preview-image').removeClass('default-image');
          $imageBox.find('.preview-image-wrapper').show();
        }
      });
      $(document).on('click', '[data-bb-toggle="image-picker-remove"]', function (event) {
        event.preventDefault();
        var $this = $(event.currentTarget);
        var $imageBox = $this.closest('.image-box');
        $imageBox.find('.preview-image-wrapper img').prop('src', $imageBox.find('.preview-image-wrapper img').data('default'));
        $imageBox.find('.image-data').val('').trigger('change');
        $imageBox.find('.preview-image').addClass('default-image');
        $this.hide();
      });
      $(document).on('click', '[data-bb-toggle="media-file-remove"]', function (event) {
        event.preventDefault();
        var currentTarget = $(event.currentTarget);
        var wrapper = currentTarget.closest('.attachment-wrapper');
        wrapper.find('.attachment-details').addClass('hidden');
        wrapper.find('.attachment-url').val('');
        currentTarget.hide();
      });
      $(document).on('click', '[data-bb-toggle="image-picker-remove"]', function (e) {
        e.preventDefault();
        var $this = $(e.currentTarget);
        $this.tooltip('dispose');
        var $list = $this.closest('.list-gallery-media-images');
        $this.closest('.gallery-image-item-handler').remove();
        if ($list.find('.gallery-image-item-handler').length === 0) {
          var $listImage = $list.closest('.list-images');
          $listImage.find('.default-placeholder-gallery-image').removeClass('hidden');
          $listImage.find('.footer-action').hide();
        }
      });
      var $listImages = $('.list-images');
      if ($listImages.length) {
        $(document).on('click', '[data-bb-toggle="gallery-reset"]', function (e) {
          e.preventDefault();
          $listImages.find('.list-gallery-media-images .gallery-image-item-handler').remove();
          $listImages.find('.default-placeholder-gallery-image').removeClass('hidden');
          $listImages.find('.footer-action').hide();
        });
        $listImages.find('.list-gallery-media-images').each(function (index, item) {
          if (jQuery().sortable) {
            var $current = $(item);
            if ($current.data('ui-sortable')) {
              $current.sortable('destroy');
            }
            $current.sortable();
          }
        });
      }
    }
  }, {
    key: "getViewPort",
    value: function getViewPort() {
      var e = window,
        a = 'inner';
      if (!('innerWidth' in window)) {
        a = 'client';
        e = document.documentElement || document.body;
      }
      return {
        width: e[a + 'Width'],
        height: e[a + 'Height']
      };
    }
  }, {
    key: "initCodeEditor",
    value: function initCodeEditor(id) {
      var type = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'css';
      var isObject = _typeof(id) === 'object';
      var editor = isObject ? $(id) : $(document).find('#' + id);
      id = isObject ? id.id : id;
      if (isObject ? editor === undefined : !editor.length) {
        return;
      }
      editor.wrap("<div id='wrapper_".concat(id, "'><div class='container_content_codemirror'></div> </div>"));
      $("#wrapper_".concat(id)).append("<div class='handle-tool-drag' id='tool-drag_".concat(id, "'></div>"));
      CodeMirror.fromTextArea(editor[0], {
        extraKeys: {
          'Ctrl-Space': 'autocomplete'
        },
        lineNumbers: true,
        mode: type,
        autoRefresh: true,
        lineWrapping: true
      });
      $('.handle-tool-drag').mousedown(function (event) {
        var _self = $(event.currentTarget);
        _self.attr('data-start_h', _self.parent().find('.CodeMirror').height()).attr('data-start_y', event.pageY);
        $('body').attr('data-dragtool', _self.attr('id')).on('mousemove', Botble.onDragTool);
        $(window).on('mouseup', Botble.onReleaseTool);
      });
    }
  }, {
    key: "onDragTool",
    value: function onDragTool(e) {
      var $element = $("#".concat($('body').attr('data-dragtool')));
      var startHeight = parseInt($element.attr('data-start_h'));
      $element.parent().find('.CodeMirror').css('height', Math.max(200, startHeight + e.pageY - $element.attr('data-start_y')));
    }
  }, {
    key: "onReleaseTool",
    value: function onReleaseTool() {
      $('body').off('mousemove', Botble.onDragTool);
      $(window).off('mouseup', Botble.onReleaseTool);
    }
  }, {
    key: "initFieldCollapse",
    value: function initFieldCollapse() {
      var parseValue = function parseValue(value) {
        if (typeof value === 'string') {
          try {
            return JSON.parse(value);
          } catch (e) {
            return value;
          }
        }
        return value;
      };
      var isEqual = function isEqual(value1, value2) {
        return String(value1) === String(value2);
      };
      var handleCheckbox = function handleCheckbox(target, isReverse, isChecked) {
        var targetElement = $(target);
        isReverse ? isChecked ? targetElement.slideUp() : targetElement.slideDown() : isChecked ? targetElement.slideDown() : targetElement.slideUp();
      };
      var handleValueBasedCollapse = function handleValueBasedCollapse(target, value) {
        var targets = $("".concat(target, "[data-bb-value]"));
        targets.each(function (_, element) {
          var $element = $(element);
          var bbValue = parseValue($element.data('bb-value'));
          var shouldShow = Array.isArray(bbValue) ? bbValue.some(function (v) {
            return isEqual(v, value);
          }) : isEqual(bbValue, value);
          shouldShow ? $element.slideDown() : $element.slideUp();
        });
      };
      var handleButtonCollapse = function handleButtonCollapse(target) {
        var targetElement = $(target);
        if (targetElement.length) {
          targetElement.slideToggle();
        }
      };
      $(document).on('click change', '[data-bb-toggle="collapse"]', function (e) {
        var $target = $(e.currentTarget);
        var target = $target.data('bb-target');
        var type = e.currentTarget.type;
        switch (type) {
          case 'checkbox':
            handleCheckbox(target, $target.data('bb-reverse'), $target.prop('checked'));
            break;
          case 'radio':
          case 'select-one':
            handleValueBasedCollapse(target, $target.val());
            break;
          case 'button':
            handleButtonCollapse(target);
            break;
          default:
            console.warn("[Botble] Unknown type ".concat(type, " of collapse"));
        }
      });
      var collapsibleTargets = {};
      $(document).find('[data-bb-collapse]').each(function (_, element) {
        collapsibleTargets[$(element).data('bb-trigger')] = true;
      });
      Object.keys(collapsibleTargets).forEach(function (target) {
        $(document).on('change', target, function (e) {
          var $target = $(e.currentTarget);
          var value = $target.val();
          var targetSelector = "[data-bb-trigger=\"".concat(target, "\"]");
          $(targetSelector).slideUp();
          if (e.currentTarget.type !== 'checkbox' || $target.is(':checked')) {
            handleValueBasedCollapse(targetSelector, value);
          }
        });
      });
    }
  }, {
    key: "initTreeCheckboxes",
    value: function initTreeCheckboxes() {
      var handleChildren = function handleChildren() {
        var $checkbox = $(this);
        var $checkboxChildren = $checkbox.parent().parent().find('ul input[type=checkbox]');
        $checkboxChildren.each(function () {
          if ($checkbox.prop('checked')) {
            $(this).prop('checked', true);
          } else {
            $(this).prop('checked', false);
            $(this).prop('indeterminate', false);
          }
        });
      };
      var _handleParents = function handleParents(current) {
        var $parent = $(current).closest('ul').closest('li').find('> .form-check > input[type=checkbox]');
        var $topParent = $parent.parent().parent();
        if ($topParent.find('ul input[type=checkbox]:checked').length > 0) {
          if ($topParent.find('ul input[type=checkbox]:checked').length === $topParent.find('ul input[type=checkbox]').length) {
            $parent.prop('indeterminate', false);
            $parent.prop('checked', true);
          } else {
            $parent.prop('indeterminate', true);
          }
        } else {
          $parent.prop('indeterminate', false);
          if ($parent.prop('checked') || $topParent.find('ul input[type=checkbox]:checked').length > 0) {
            $parent.prop('checked', true);
          } else {
            $parent.prop('checked', false);
          }
        }
        if ($parent.length > 0) {
          _handleParents($parent);
        }
      };
      var target = '[data-bb-toggle="tree-checkboxes"] input[type="checkbox"]';
      $(document).on('click', target, handleChildren);
      $(document).on('click', target, function () {
        _handleParents(this);
      });
      $(document).ready(function () {
        $(target).each(function () {
          _handleParents(this);
        });
      });
    }
  }, {
    key: "initCodeEditorComponent",
    value: function initCodeEditorComponent() {
      $(document).find('textarea[data-bb-code-editor]').each(function () {
        if ($(this).next().hasClass('CodeMirror')) {
          return;
        }
        Botble.initCodeEditor(this, this.dataset.mode || 'htmlmixed');
      });
    }

    /**
     * @param {HTMLElement} element
     * @param {Boolean} overlay
     * @param {String} position
     */
  }, {
    key: "showButtonLoading",
    value: function showButtonLoading(element) {
      var overlay = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
      var position = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'start';
      if (overlay && element) {
        $(element).addClass('btn-loading').attr('disabled', true);
        return;
      }
      var loading = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>';
      var icon = $(element).find('svg');
      if (icon.length) {
        icon.addClass('d-none');
      }
      if (position === 'start') {
        $(element).prepend(loading);
      } else if (position === 'end') {
        $(element).append(loading);
      }
    }
  }, {
    key: "hideButtonLoading",
    value: function hideButtonLoading(element) {
      if (!element) {
        return;
      }
      if ($(element).hasClass('btn-loading')) {
        $(element).removeClass('btn-loading').removeAttr('disabled');
        return;
      }
      $(element).find('.spinner-border').remove();
      $(element).find('svg').removeClass('d-none');
    }

    /**
     * @param {HTMLElement} element
     */
  }, {
    key: "showLoading",
    value: function showLoading() {
      var element = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
      if (!element) {
        element = document.querySelector('.page-wrapper');
      }
      if ($(element).find('.loading-spinner').length) {
        return;
      }
      $(element).addClass('position-relative');
      $(element).append('<div class="loading-spinner"></div>');
    }
  }, {
    key: "hideLoading",
    value: function hideLoading() {
      var element = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
      if (!element) {
        element = document.querySelector('.page-wrapper');
      }
      $(element).removeClass('position-relative');
      $(element).find('.loading-spinner').remove();
    }

    /**
     * @param {HTMLElement} element
     * @param {object} options
     * @param {boolean} force
     */
  }, {
    key: "select",
    value: function select(element) {
      var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      var force = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
      var $element = $(element);
      if (!jQuery().select2 || $element.hasClass('select2-hidden-accessible') && !force) {
        return;
      }
      options = _objectSpread({
        width: '100%',
        placeholder: $element.data('placeholder') || null,
        allowClear: $element.data('allow-clear') || false
      }, options);
      var parent = $element.closest('div[data-select2-dropdown-parent]') || $element.closest('.modal-content') || $element.closest('.modal');
      if (parent.length) {
        options.dropdownParent = parent;
        options.width = '100%';
      }
      $element.select2(options);
    }

    /**
     * @param {String[]|HTMLElement} sources
     * @return {FsLightbox}
     */
  }, {
    key: "lightbox",
    value: function lightbox(sources) {
      var lightbox = new FsLightbox();
      if (Array.isArray(sources)) {
        lightbox.props.sources = sources;
        lightbox.open();
      }
      return lightbox;
    }
  }, {
    key: "initLightbox",
    value: function initLightbox() {
      var instance = window.lightboxInstance || {};
      var a = document.querySelectorAll('a[data-bb-lightbox]');
      if (!a.length) {
        return;
      }
      a.forEach(function (element) {
        var instanceName = element.dataset.bbLightbox;
        if (!instance[instanceName]) {
          instance[instanceName] = Botble.lightbox();
        }
        var source = element.href;
        instance[instanceName].props.sources.push(source);
        instance[instanceName].elements.a.push(element);
        var currentIndex = instance[instanceName].props.sources.length - 1;
        element.addEventListener('click', function (e) {
          e.preventDefault();
          instance[instanceName].open(currentIndex);
        });
      });
      window.lightboxInstance = instance;
    }
  }, {
    key: "initColorPicker",
    value: function initColorPicker() {
      if (!document.querySelector('[data-bb-color-picker]')) {
        return;
      }
      console.log('aaa');
      $('[data-bb-color-picker]').each(function (index, item) {
        var $current = $(item);
        var options = {
          allowEmpty: true,
          color: $current.val() || 'rgb(51, 51, 51)',
          showInput: true,
          containerClassName: 'full-spectrum',
          showInitial: true,
          showSelectionPalette: false,
          showPalette: true,
          showAlpha: true,
          preferredFormat: 'hex',
          showButtons: false,
          palette: [['rgb(0, 0, 0)', 'rgb(102, 102, 102)', 'rgb(183, 183, 183)', 'rgb(217, 217, 217)', 'rgb(239, 239, 239)', 'rgb(243, 243, 243)', 'rgb(255, 255, 255)', 'rgb(230, 184, 175)', 'rgb(244, 204, 204)', 'rgb(252, 229, 205)', 'rgb(255, 242, 204)', 'rgb(217, 234, 211)', 'rgb(208, 224, 227)', 'rgb(201, 218, 248)', 'rgb(207, 226, 243)', 'rgb(217, 210, 233)', 'rgb(234, 209, 220)', 'rgb(221, 126, 107)', 'rgb(234, 153, 153)', 'rgb(249, 203, 156)', 'rgb(255, 229, 153)', 'rgb(182, 215, 168)', 'rgb(162, 196, 201)', 'rgb(164, 194, 244)', 'rgb(159, 197, 232)', 'rgb(180, 167, 214)', 'rgb(213, 166, 189)', 'rgb(204, 65, 37)', 'rgb(224, 102, 102)', 'rgb(246, 178, 107)', 'rgb(255, 217, 102)', 'rgb(147, 196, 125)', 'rgb(118, 165, 175)', 'rgb(109, 158, 235)', 'rgb(111, 168, 220)', 'rgb(142, 124, 195)', 'rgb(194, 123, 160)', 'rgb(166, 28, 0)', 'rgb(204, 0, 0)', 'rgb(230, 145, 56)', 'rgb(241, 194, 50)', 'rgb(106, 168, 79)', 'rgb(69, 129, 142)', 'rgb(60, 120, 216)', 'rgb(61, 133, 198)', 'rgb(103, 78, 167)', 'rgb(166, 77, 121)', 'rgb(133, 32, 12)', 'rgb(153, 0, 0)', 'rgb(180, 95, 6)', 'rgb(191, 144, 0)', 'rgb(56, 118, 29)', 'rgb(19, 79, 92)', 'rgb(17, 85, 204)', 'rgb(11, 83, 148)', 'rgb(53, 28, 117)', 'rgb(116, 27, 71)', 'rgb(91, 15, 0)', 'rgb(102, 0, 0)', 'rgb(120, 63, 4)', 'rgb(127, 96, 0)', 'rgb(39, 78, 19)', 'rgb(12, 52, 61)', 'rgb(28, 69, 135)', 'rgb(7, 55, 99)', 'rgb(32, 18, 77)', 'rgb(76, 17, 48)', 'rgb(152, 0, 0)', 'rgb(255, 0, 0)', 'rgb(255, 153, 0)', 'rgb(255, 255, 0)', 'rgb(0, 255, 0)', 'rgb(0, 255, 255)', 'rgb(74, 134, 232)', 'rgb(0, 0, 255)', 'rgb(153, 0, 255)', 'rgb(255, 0, 255)']],
          change: function change(color) {
            if (color) {
              $current.val(color.toRgbString());
            }
          }
        };
        var parent = $current.closest('.modal');
        if (parent.length) {
          options.appendTo = parent;
        }
        $current.spectrum(options);
      });
    }
  }, {
    key: "initClipboard",
    value: function initClipboard() {
      $(document).on('click', '[data-bb-toggle="clipboard"]', /*#__PURE__*/function () {
        var _ref6 = _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee2(e) {
          var target, copiedMessage, action, isCut, iconClipboard, iconClipboardSuccess, clipboardParent, clipboardParentTarget, text, copyTarget;
          return _regenerator().w(function (_context2) {
            while (1) switch (_context2.n) {
              case 0:
                e.preventDefault();
                target = $(e.currentTarget);
                copiedMessage = target.data('clipboard-message');
                action = target.data('clipboard-action') || 'copy';
                isCut = action.toLowerCase() === 'cut';
                iconClipboard = target.find('[data-clipboard-icon]');
                iconClipboardSuccess = target.find('[data-clipboard-success-icon]');
                clipboardParent = target.data('clipboard-parent');
                clipboardParentTarget = clipboardParent ? document.querySelector(clipboardParent) : undefined;
                text = target.data('clipboard-text');
                if (!text) {
                  copyTarget = $(target.data('clipboard-target'));
                  if (copyTarget.length > 0) {
                    text = copyTarget.val();
                    isCut && copyTarget.val('');
                  }
                }
                _context2.n = 1;
                return Botble.copyToClipboard(text, clipboardParentTarget);
              case 1:
                if (copiedMessage) {
                  Botble.showSuccess(copiedMessage);
                }
                iconClipboard.addClass('d-none');
                iconClipboardSuccess.removeClass('d-none');
                setTimeout(function () {
                  iconClipboard.removeClass('d-none');
                  iconClipboardSuccess.addClass('d-none');
                }, 1000);
              case 2:
                return _context2.a(2);
            }
          }, _callee2);
        }));
        return function (_x3) {
          return _ref6.apply(this, arguments);
        };
      }());
    }
  }, {
    key: "initTreeCategoriesSelect",
    value: function initTreeCategoriesSelect() {
      var element = document.querySelector('[data-bb-toggle="tree-categories-select"]');
      if (!element) {
        return;
      }
      Botble.select(element, {
        render: {
          option: function option(data) {
            return "<div>".concat(data.renderOption, "</div>");
          },
          item: function item(data) {
            return "<div>".concat(data.renderItem, "</div>");
          }
        }
      });
    }
  }, {
    key: "initDropdownCheckboxes",
    value: function initDropdownCheckboxes() {
      var _countCheckedDropdownCheckboxes = function countCheckedDropdownCheckboxes(e) {
        var $wrapper = e ? $(e.currentTarget).closest('[data-bb-toggle="dropdown-checkboxes"]') : $('[data-bb-toggle="dropdown-checkboxes"]');
        if (Array.isArray($wrapper)) {
          $wrapper.forEach(function (wrapper) {
            _countCheckedDropdownCheckboxes(wrapper);
          });
          return;
        }
        var $checkedCheckboxes = $wrapper.find('input[type="checkbox"]:checked');
        var $textElement = $wrapper.find('> span');
        if ($checkedCheckboxes.length) {
          if ($checkedCheckboxes.length > 3) {
            $textElement.text($checkedCheckboxes.length + ' ' + $wrapper.data('selected-text'));
          } else {
            var values = [];
            $checkedCheckboxes.each(function () {
              values.push($(this).siblings('.form-check-label').text().trim());
            });
            $textElement.text(values.join(', '));
          }
        } else {
          if ($wrapper.length) {
            $.map($wrapper, function (item) {
              $(item).find('> span').text($(item).data('placeholder') || ' ');
            });
          }
        }
      };
      _countCheckedDropdownCheckboxes();
      $(document).on('click', '[data-bb-toggle="dropdown-checkboxes"] input[type="checkbox"]', function (e) {
        _countCheckedDropdownCheckboxes(e);
        var $wrapper = $(e.currentTarget).closest('[data-bb-toggle="dropdown-checkboxes"]');
        var $selected = $wrapper.find('.multi-checklist-selected');
        if ($(e.currentTarget).is(':checked')) {
          var $input = "<input type=\"hidden\" name=\"".concat($wrapper.data('name'), "\" value=\"").concat($(e.currentTarget).val(), "\">");
          $selected.append($input);
        } else {
          var _$input = $selected.find("input[value=\"".concat($(e.currentTarget).val(), "\"]"));
          _$input.remove();
        }
      });
      $(document).on('click', '[data-bb-toggle="dropdown-checkboxes"] > span', function (event) {
        event.stopPropagation();
        var $this = $(this);
        var $input = $this.siblings('input[type="text"]');
        var $dropdown = $this.siblings('.dropdown-menu');
        var $wrapper = $this.closest('[data-bb-toggle="dropdown-checkboxes"]');
        $dropdown.addClass('show');
        $this.hide();
        $input.show().trigger('focus');
        if ($wrapper.data('ajax-url')) {
          var template = "<li>\n                    <label class=\"form-check\">\n                        <input type=\"checkbox\" id=\"__id__\" class=\"form-check-input\" value=\"__value__\">\n                        <span class=\"form-check-label\">\n                            __label__\n                        </span>\n                    </label>\n                </li>";
          var name = $wrapper.data('name');
          $httpClient.make().withLoading($dropdown).get($wrapper.data('ajax-url')).then(function (_ref7) {
            var data = _ref7.data;
            var html = '';
            Object.keys(data).map(function (item) {
              html += template.replace(/__id__/g, "".concat(name, "-").concat(item)).replace(/__value__/g, item).replace(/__label__/g, data[item]);
            });
            $dropdown.find('ul').html(html);
            var $selected = $wrapper.find('.multi-checklist-selected');
            var $inputs = $selected.find('input[type="hidden"]');
            $inputs.each(function () {
              var $input = $(this);
              var $checkbox = $dropdown.find("input[value=\"".concat($input.val(), "\"]"));
              $checkbox.prop('checked', true);
            });
          });
        }
      });
      $(document).on('click', function (event) {
        var $target = $(event.target);
        var $wrapper = $('[data-bb-toggle="dropdown-checkboxes"]');
        if (!$target.closest('[data-bb-toggle="dropdown-checkboxes"]').length) {
          $wrapper.find('> .dropdown-menu').removeClass('show');
          $wrapper.find('> span').show();
          $wrapper.find('> input[type="text"]').val('').hide();
          if ($wrapper.data('ajax-url')) {
            $wrapper.find('> .dropdown-menu ul').html('<div class="py-5"></div>');
          }
        }
      });
      $(document).on('keyup', '[data-bb-toggle="dropdown-checkboxes"] input[type="text"]', function () {
        var $this = $(this);
        var $wrapper = $this.closest('[data-bb-toggle="dropdown-checkboxes"]');
        var $items = $wrapper.find('li');
        var value = $this.val().trim().toLowerCase();
        if (value.length) {
          $items.hide();
          $items.each(function () {
            var $item = $(this);
            var $label = $item.find('.form-check-label');
            if ($label.text().trim().toLowerCase().indexOf(value) !== -1) {
              $item.show();
            }
          });
        } else {
          $items.show();
        }
      });
    }
  }, {
    key: "initEditable",
    value: function initEditable() {
      var $element = $('.editable');
      if (!$element.length) {
        return;
      }
      $element.editable({
        mode: 'inline',
        success: function success(response) {
          if (response.error && response.message) {
            Botble.showError(response.message);
          }
        },
        error: function error(response) {
          Botble.handleError(response);
        }
      });
    }
  }, {
    key: "unmaskInputNumber",
    value: function unmaskInputNumber($form, formData) {
      if (jQuery().inputmask) {
        $form.find('input.input-mask-number').map(function (i, e) {
          var $input = $(e);
          if ($input.inputmask) {
            if ($.isArray(formData)) {
              formData[$input.attr('name')] = $input.inputmask('unmaskedvalue');
            } else {
              formData.append($input.attr('name'), $input.inputmask('unmaskedvalue'));
            }
          }
        });
        return formData;
      }
    }
  }]);
}();
_defineProperty(Botble, "noticesTimeout", {});
_defineProperty(Botble, "noticesTimeoutCount", 500);
$(function () {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  new Botble();
  window.Botble = Botble;
});
})();

/******/ })()
;