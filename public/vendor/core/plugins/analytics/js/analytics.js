/******/ (() => { // webpackBootstrap
/*!**************************************************************!*\
  !*** ./platform/plugins/analytics/resources/js/analytics.js ***!
  \**************************************************************/
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
var PluginAnalytics = /*#__PURE__*/function () {
  function PluginAnalytics() {
    _classCallCheck(this, PluginAnalytics);
  }
  return _createClass(PluginAnalytics, null, [{
    key: "initCharts",
    value: function initCharts() {
      var analyticsData = window.analyticsStats || {};
      var $statsChart = $('#stats-chart');
      var $worldMap = $('#world-map');
      var statArray = [];
      $.each(analyticsData.stats, function (index, el) {
        statArray.push({
          axis: el.axis,
          visitors: el.visitors,
          pageViews: el.pageViews
        });
      });
      if ($statsChart.length) {
        new Morris.Area({
          element: 'stats-chart',
          resize: true,
          data: statArray,
          xkey: 'axis',
          ykeys: ['visitors', 'pageViews'],
          labels: [analyticsData.translations.visits, analyticsData.translations.pageViews],
          lineColors: ['#d6336c', '#4299e1'],
          hideHover: 'auto',
          parseTime: false
        });
      }
      var visitorsData = {};
      $.each(analyticsData.countryStats, function (index, el) {
        visitorsData[el[0]] = el[1];
      });
      if ($worldMap.length) {
        $worldMap.vectorMap({
          map: 'world_mill_en',
          backgroundColor: 'transparent',
          regionStyle: {
            initial: {
              fill: '#f6f8fb',
              stroke: '#dce1e7',
              'stroke-width': 2
            }
          },
          series: {
            regions: [{
              values: visitorsData,
              scale: ['#ffffff', '#206bc4'],
              normalizeFunction: 'polynomial'
            }]
          },
          onRegionLabelShow: function onRegionLabelShow(e, el, code) {
            if (typeof visitorsData[code] !== 'undefined') {
              el.html(el.html() + ': ' + visitorsData[code] + ' ' + analyticsData.translations.visits);
            }
          }
        });
      }
    }
  }]);
}();
$(function () {
  var $analyticsGeneral = $('#widget_analytics_general');
  BDashboard.loadWidget($analyticsGeneral.find('.widget-content'), $analyticsGeneral.data('url'), null, function () {
    var _stats$;
    PluginAnalytics.initCharts();
    var stats = window.analyticsStats.stats || [];
    if (!((_stats$ = stats[1]) !== null && _stats$ !== void 0 && _stats$.visitors)) {
      $analyticsGeneral.find('.stats-warning').addClass('d-block');
      $analyticsGeneral.find('.stats-warning').removeClass('d-none');
    } else {
      $analyticsGeneral.find('.stats-warning').addClass('d-none');
      $analyticsGeneral.find('.stats-warning').removeClass('d-block');
    }
  });
  BDashboard.loadWidget($('#widget_analytics_page').find('.widget-content'), $('#widget_analytics_page').data('url'));
  BDashboard.loadWidget($('#widget_analytics_browser').find('.widget-content'), $('#widget_analytics_browser').data('url'));
  BDashboard.loadWidget($('#widget_analytics_referrer').find('.widget-content'), $('#widget_analytics_referrer').data('url'));
});
/******/ })()
;