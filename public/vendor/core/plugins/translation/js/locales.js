/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./platform/plugins/translation/resources/js/locales.js":
/*!**************************************************************!*\
  !*** ./platform/plugins/translation/resources/js/locales.js ***!
  \**************************************************************/
/***/ (function() {

var _this = this;
$(function () {
  var languageTable = $('.table-language');
  languageTable.on('click', '.delete-locale-button', function (event) {
    event.preventDefault();
    $('.delete-crud-entry').data('url', $(event.currentTarget).data('url'));
    $('.modal-confirm-delete').modal('show');
  });
  $(document).on('click', '.delete-crud-entry', function (event) {
    event.preventDefault();
    $('.modal-confirm-delete').modal('hide');
    var deleteURL = $(event.currentTarget).data('url');
    Botble.showButtonLoading($(_this));
    $httpClient.make()["delete"](deleteURL).then(function (_ref) {
      var data = _ref.data;
      if (data.data) {
        languageTable.find("i[data-locale=".concat(data.data, "]")).unwrap();
        $('.tooltip').remove();
      }
      languageTable.find(".delete-locale-button[data-url=\"".concat(deleteURL, "\"]")).closest('tr').remove();
      Botble.showSuccess(data.message);
    })["finally"](function () {
      Botble.hideButtonLoading($(_this));
    });
  });
  $(document).on('submit', '.add-locale-form', function (event) {
    event.preventDefault();
    event.stopPropagation();
    var form = $(this);
    var button = form.find('button[type="submit"]');
    Botble.showButtonLoading(button);
    $httpClient.make().postForm(form.prop('action'), new FormData(form[0])).then(function (_ref2) {
      var data = _ref2.data;
      Botble.showSuccess(data.message);
      languageTable.load("".concat(window.location.href, " .table-language > *"));
    })["finally"](function () {
      Botble.hideButtonLoading(button);
    });
  });
});

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module is referenced by other modules so it can't be inlined
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./platform/plugins/translation/resources/js/locales.js"].call(__webpack_exports__);
/******/ 	
/******/ })()
;