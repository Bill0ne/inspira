/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/*!*******************************************************************!*\
  !*** ./platform/plugins/language/resources/js/language-public.js ***!
  \*******************************************************************/


$(function () {
  $('.language-wrapper .dropdown .dropdown-toggle').off('click').on('click', function (event) {
    event.preventDefault();
    var _self = $(event.currentTarget);
    if (_self.hasClass('active')) {
      _self.closest('.language-wrapper').find('.dropdown .dropdown-menu').hide();
      _self.removeClass('active');
    } else {
      _self.closest('.language-wrapper').find('.dropdown .dropdown-menu').show();
      _self.addClass('active');
    }
  });
  $(document).on('click', function (event) {
    var _self = $(event.currentTarget);
    if (_self.closest('.language-wrapper').length === 0) {
      _self.closest('.language-wrapper').find('.dropdown .dropdown-menu').hide();
      _self.closest('.language-wrapper').find('.dropdown .dropdown-toggle').removeClass('active');
    }
  });
});
/******/ })()
;