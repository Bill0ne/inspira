/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/*!**************************************************!*\
  !*** ./platform/plugins/faq/resources/js/faq.js ***!
  \**************************************************/


$(function () {
  $(document).on('click', '[data-bb-toggle="select-from-existing"]', function (e) {
    e.preventDefault();
    $('.existing-faq-schema-items').show();
  });
});
/******/ })()
;