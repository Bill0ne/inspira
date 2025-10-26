/******/ (() => { // webpackBootstrap
/*!***********************************************************!*\
  !*** ./platform/core/base/resources/js/repeater-field.js ***!
  \***********************************************************/
$(function () {
  var htmlUnescapes = {
    '&amp;': '&',
    '&lt;': '<',
    '&gt;': '>',
    '&quot;': '"',
    '&#39;': "'"
  };
  var unescapeHtmlChar = basePropertyOf(htmlUnescapes);
  var reEscapedHtml = /&(?:amp|lt|gt|quot|#39);/g;
  var reHasEscapedHtml = RegExp(reEscapedHtml.source);
  function basePropertyOf(object) {
    return function (key) {
      return object == null ? undefined : object[key];
    };
  }
  function unescape(string) {
    string = string.toString();
    return string && reHasEscapedHtml.test(string) ? string.replace(reEscapedHtml, unescapeHtmlChar) : string;
  }
  $(document).on('click', '[data-target="repeater-add"]', function () {
    var id = $(this).data('id');
    var $group = $("#".concat(id, "_group"));
    var $template = $("#".concat(id, "_template"));
    var $fields = $("#".concat(id, "_fields"));
    var nextIndex = parseInt($group.data('nextIndex'));
    var content = $template.html();
    var fields = $fields.text();
    content = content.replace(/__key__/g, nextIndex);
    fields = fields.replace(/__key__/g, nextIndex);
    content = content.replace(/__fields__/g, unescape(fields));
    $group.append(content);
    $group.data('nextIndex', nextIndex + 1);
    if (window.Botble) {
      window.Botble.initResources();
      window.Botble.initMediaIntegrate();
      window.Botble.initCoreIcon();
    }
    if (window.EditorManagement) {
      window.EDITOR = new EditorManagement().init();
    }
  });
  $(document).on('click', '[data-target="repeater-remove"]', function () {
    var id = $(this).data('id');
    $("[data-id=\"".concat(id, "\"]")).remove();
  });
});
/******/ })()
;