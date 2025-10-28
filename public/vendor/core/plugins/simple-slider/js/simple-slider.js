/******/ (() => { // webpackBootstrap
/*!**********************************************************************!*\
  !*** ./platform/plugins/simple-slider/resources/js/simple-slider.js ***!
  \**********************************************************************/
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
var SimpleSliderManagement = /*#__PURE__*/function () {
  function SimpleSliderManagement() {
    _classCallCheck(this, SimpleSliderManagement);
  }
  return _createClass(SimpleSliderManagement, [{
    key: "init",
    value: function init() {
      var target = $(document).find('.owl-slider');
      if (target.length > 0) {
        target.each(function () {
          var el = $(this),
            dataAuto = el.data('owl-auto'),
            dataLoop = el.data('owl-loop'),
            dataSpeed = el.data('owl-speed'),
            dataGap = el.data('owl-gap'),
            dataNav = el.data('owl-nav'),
            dataDots = el.data('owl-dots'),
            dataAnimateIn = el.data('owl-animate-in') ? el.data('owl-animate-in') : '',
            dataAnimateOut = el.data('owl-animate-out') ? el.data('owl-animate-out') : '',
            dataDefaultItem = el.data('owl-item'),
            dataItemXS = el.data('owl-item-xs'),
            dataItemSM = el.data('owl-item-sm'),
            dataItemMD = el.data('owl-item-md'),
            dataItemLG = el.data('owl-item-lg'),
            dataItemXL = el.data('owl-item-xl'),
            dataNavLeft = el.data('owl-nav-left') ? el.data('owl-nav-left') : '<i class="fa fa-angle-left"></i>',
            dataNavRight = el.data('owl-nav-right') ? el.data('owl-nav-right') : '<i class="fa fa-angle-right"></i>',
            duration = el.data('owl-duration'),
            dataMouseDrag = el.data('owl-mousedrag') === 'on',
            center = el.data('owl-center');
          if (target.children('div, span, a, img, h1, h2, h3, h4, h5, h5').length >= 1) {
            el.owlCarousel({
              rtl: $('body').prop('dir') === 'rtl',
              animateIn: dataAnimateIn,
              animateOut: dataAnimateOut,
              margin: dataGap,
              autoplay: dataAuto,
              autoplayTimeout: dataSpeed,
              autoplayHoverPause: true,
              loop: dataLoop,
              nav: dataNav,
              mouseDrag: dataMouseDrag,
              touchDrag: true,
              autoplaySpeed: duration,
              navSpeed: duration,
              dotsSpeed: duration,
              dragEndSpeed: duration,
              navText: [dataNavLeft, dataNavRight],
              dots: dataDots,
              items: dataDefaultItem,
              center: Boolean(center),
              responsive: {
                0: {
                  items: dataItemXS
                },
                480: {
                  items: dataItemSM
                },
                768: {
                  items: dataItemMD
                },
                992: {
                  items: dataItemLG
                },
                1200: {
                  items: dataItemXL
                },
                1680: {
                  items: dataDefaultItem
                }
              }
            });
            el.on('change.owl.carousel', function (event) {
              var $currentItem = $('.owl-item', el).eq(event.item.index);
              var $elementsToAnimation = $currentItem.find('[data-animation-out]');
              SimpleSliderManagement.setAnimation($elementsToAnimation, 'out');
            });
            el.on('changed.owl.carousel', function (event) {
              var $currentItem = $('.owl-item', el).eq(event.item.index);
              var $elementsToAnimation = $currentItem.find('[data-animation-in]');
              SimpleSliderManagement.setAnimation($elementsToAnimation, 'in');
            });
          }
        });
      }
    }
  }], [{
    key: "setAnimation",
    value: function setAnimation(_elem, _InOut) {
      var animationEndEvent = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
      _elem.each(function () {
        var $elem = $(this);
        var $animationType = 'animated ' + $elem.data('animation-' + _InOut);
        $elem.addClass($animationType).one(animationEndEvent, function () {
          $elem.removeClass($animationType);
        });
      });
    }
  }]);
}();
$(function () {
  new SimpleSliderManagement().init();
});
/******/ })()
;