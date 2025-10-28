/******/ (() => { // webpackBootstrap
/*!*************************************************************!*\
  !*** ./platform/packages/revision/resources/js/revision.js ***!
  \*************************************************************/
$(function () {
  $.each($('.html-diff-content'), function (index, item) {
    $(item).html(htmldiff($(item).data('original'), $(item).html()));
  });
});
/******/ })()
;