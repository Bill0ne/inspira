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
/*!*******************************************************!*\
  !*** ./platform/packages/theme/resources/js/toast.js ***!
  \*******************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _core_base_resources_js_base_toast__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../../core/base/resources/js/base/toast */ "./platform/core/base/resources/js/base/toast.js");

var Theme = Theme || {};
window.Theme = Theme;
Theme.showNotice = function (messageType, message) {
  var color = '#fff';
  var icon = '';
  switch (messageType) {
    case 'success':
      color = '#437a43';
      icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>';
      break;
    case 'danger':
      color = '#bd362f';
      icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>';
      break;
    case 'warning':
      color = '#f89406';
      icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 8v4" /><path d="M12 16h.01" /></svg>';
      break;
    case 'info':
      color = '#2f96b4';
      icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 9h.01" /><path d="M11 12h1v4h1" /></svg>';
      break;
  }
  (0,_core_base_resources_js_base_toast__WEBPACK_IMPORTED_MODULE_0__["default"])({
    text: message,
    icon: icon,
    duration: 5000,
    close: true,
    gravity: 'bottom',
    position: 'right',
    stopOnFocus: true,
    style: {
      background: color
    },
    escapeMarkup: false,
    className: 'toastify-' + messageType
  }).showToast();
};
Theme.showError = function (message) {
  this.showNotice('danger', message);
};
Theme.showSuccess = function (message) {
  this.showNotice('success', message);
};
Theme.handleError = function (data) {
  if (typeof data.errors !== 'undefined' && data.errors.length) {
    Theme.handleValidationError(data.errors);
  } else if (typeof data.responseJSON !== 'undefined') {
    if (typeof data.responseJSON.errors !== 'undefined') {
      if (data.status === 422) {
        Theme.handleValidationError(data.responseJSON.errors);
      }
    } else if (typeof data.responseJSON.message !== 'undefined') {
      Theme.showError(data.responseJSON.message);
    } else {
      Theme.showError(data.responseJSON.join(', ').join(', '));
    }
  } else {
    Theme.showError(data.statusText);
  }
};
Theme.handleValidationError = function (errors) {
  var message = '';
  Object.values(errors).forEach(function (item) {
    if (message !== '') {
      message += '\n';
    }
    message += item;
  });
  Theme.showError(message);
};
})();

/******/ })()
;