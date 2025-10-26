/******/ (() => { // webpackBootstrap
/*!***************************************************!*\
  !*** ./platform/core/table/resources/js/table.js ***!
  \***************************************************/
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _createForOfIteratorHelper(r, e) { var t = "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (!t) { if (Array.isArray(r) || (t = _unsupportedIterableToArray(r)) || e && r && "number" == typeof r.length) { t && (r = t); var _n = 0, F = function F() {}; return { s: F, n: function n() { return _n >= r.length ? { done: !0 } : { done: !1, value: r[_n++] }; }, e: function e(r) { throw r; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var o, a = !0, u = !1; return { s: function s() { t = t.call(r); }, n: function n() { var r = t.next(); return a = r.done, r; }, e: function e(r) { u = !0, o = r; }, f: function f() { try { a || null == t["return"] || t["return"](); } finally { if (u) throw o; } } }; }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
;
(function ($, DataTable) {
  'use strict';

  var _buildParams = function _buildParams(dt, action) {
    var params = dt.ajax.params();
    params.action = action;
    params._token = $('meta[name="csrf-token"]').attr('content');
    return params;
  };
  var _downloadFromUrl = function _downloadFromUrl(url, params) {
    var postUrl = url + '/export';
    var xhr = new XMLHttpRequest();
    xhr.open('POST', postUrl, true);
    xhr.responseType = 'arraybuffer';
    xhr.onload = function () {
      if (this.status === 200) {
        var filename = '';
        var disposition = xhr.getResponseHeader('Content-Disposition');
        if (disposition && disposition.indexOf('attachment') !== -1) {
          var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
          var matches = filenameRegex.exec(disposition);
          if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
        }
        var type = xhr.getResponseHeader('Content-Type');
        var blob = new Blob([this.response], {
          type: type
        });
        if (typeof window.navigator.msSaveBlob !== 'undefined') {
          // IE workaround for "HTML7007: One or more blob URLs were revoked by closing the blob for which they were created. These URLs will no longer resolve as the data backing the URL has been freed."
          window.navigator.msSaveBlob(blob, filename);
        } else {
          var URL = window.URL || window.webkitURL;
          var downloadUrl = URL.createObjectURL(blob);
          if (filename) {
            // use HTML5 a[download] attribute to specify filename
            var a = document.createElement('a');
            // safari doesn't support this yet
            if (typeof a.download === 'undefined') {
              window.location = downloadUrl;
            } else {
              a.href = downloadUrl;
              a.download = filename;
              document.body.appendChild(a);
              a.click();
            }
          } else {
            window.location = downloadUrl;
          }
          setTimeout(function () {
            URL.revokeObjectURL(downloadUrl);
          }, 100); // cleanup
        }
      }
    };
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.send($.param(params));
  };
  var _buildUrl = function _buildUrl(dt, action) {
    var url = dt.ajax.url() || '';
    var params = dt.ajax.params();
    params.action = action;
    if (url.indexOf('?') > -1) {
      return url + '&' + $.param(params);
    }
    return url + '?' + $.param(params);
  };
  DataTable.ext.buttons.excel = {
    className: 'buttons-excel',
    text: function text(dt) {
      return "<svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\">\n                    <path stroke=\"none\" d=\"M0 0h24v24H0z\" fill=\"none\"></path>\n                    <path d=\"M14 3v4a1 1 0 0 0 1 1h4\"></path>\n                    <path d=\"M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z\"></path>\n                    <path d=\"M8 11h8v7h-8z\"></path>\n                    <path d=\"M8 15h8\"></path>\n                    <path d=\"M11 11v7\"></path>\n                </svg>\n            ".concat(dt.i18n('buttons.excel', BotbleVariables.languages.tables.excel ? BotbleVariables.languages.tables.excel : 'Excel'));
    },
    action: function action(e, dt) {
      window.location = _buildUrl(dt, 'excel');
    }
  };
  DataTable.ext.buttons.postExcel = {
    className: 'buttons-excel',
    text: function text(dt) {
      return "<svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\">\n                <path stroke=\"none\" d=\"M0 0h24v24H0z\" fill=\"none\"></path>\n                <path d=\"M14 3v4a1 1 0 0 0 1 1h4\"></path>\n                <path d=\"M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z\"></path>\n                <path d=\"M8 11h8v7h-8z\"></path>\n                <path d=\"M8 15h8\"></path>\n                <path d=\"M11 11v7\"></path>\n            </svg>\n            ".concat(dt.i18n('buttons.excel', BotbleVariables.languages.tables.excel ? BotbleVariables.languages.tables.excel : 'Excel'));
    },
    action: function action(e, dt) {
      var url = dt.ajax.url() || window.location.href;
      var params = _buildParams(dt, 'excel');
      _downloadFromUrl(url, params);
    }
  };
  DataTable.ext.buttons["export"] = {
    extend: 'collection',
    className: 'buttons-export',
    text: function text(dt) {
      return "<svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\">\n                <path stroke=\"none\" d=\"M0 0h24v24H0z\" fill=\"none\"></path>\n                <path d=\"M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2\"></path>\n                <path d=\"M7 11l5 5l5 -5\"></path>\n                <path d=\"M12 4l0 12\"></path>\n            </svg>\n            ".concat(dt.i18n('buttons.export', BotbleVariables.languages.tables["export"] ? BotbleVariables.languages.tables["export"] : 'Export'));
    },
    buttons: ['csv', 'excel']
  };
  DataTable.ext.buttons.csv = {
    className: 'buttons-csv',
    text: function text(dt) {
      return "<svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\">\n                <path stroke=\"none\" d=\"M0 0h24v24H0z\" fill=\"none\"></path>\n                <path d=\"M14 3v4a1 1 0 0 0 1 1h4\"></path>\n                <path d=\"M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4\"></path>\n                <path d=\"M7 16.5a1.5 1.5 0 0 0 -3 0v3a1.5 1.5 0 0 0 3 0\"></path>\n                <path d=\"M10 20.25c0 .414 .336 .75 .75 .75h1.25a1 1 0 0 0 1 -1v-1a1 1 0 0 0 -1 -1h-1a1 1 0 0 1 -1 -1v-1a1 1 0 0 1 1 -1h1.25a.75 .75 0 0 1 .75 .75\"></path>\n                <path d=\"M16 15l2 6l2 -6\"></path>\n            </svg>\n            ".concat(dt.i18n('buttons.csv', BotbleVariables.languages.tables.csv ? BotbleVariables.languages.tables.csv : 'CSV'));
    },
    action: function action(e, dt) {
      window.location = _buildUrl(dt, 'csv');
    }
  };
  DataTable.ext.buttons.postCsv = {
    className: 'buttons-csv',
    text: function text(dt) {
      return "<svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\">\n                <path stroke=\"none\" d=\"M0 0h24v24H0z\" fill=\"none\"></path>\n                <path d=\"M14 3v4a1 1 0 0 0 1 1h4\"></path>\n                <path d=\"M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4\"></path>\n                <path d=\"M7 16.5a1.5 1.5 0 0 0 -3 0v3a1.5 1.5 0 0 0 3 0\"></path>\n                <path d=\"M10 20.25c0 .414 .336 .75 .75 .75h1.25a1 1 0 0 0 1 -1v-1a1 1 0 0 0 -1 -1h-1a1 1 0 0 1 -1 -1v-1a1 1 0 0 1 1 -1h1.25a.75 .75 0 0 1 .75 .75\"></path>\n                <path d=\"M16 15l2 6l2 -6\"></path>\n            </svg>\n            ".concat(dt.i18n('buttons.csv', BotbleVariables.languages.tables.csv ? BotbleVariables.languages.tables.csv : 'CSV'));
    },
    action: function action(e, dt) {
      var url = dt.ajax.url() || window.location.href;
      var params = _buildParams(dt, 'csv');
      _downloadFromUrl(url, params);
    }
  };
  DataTable.ext.buttons.pdf = {
    className: 'buttons-pdf',
    text: function text(dt) {
      return "<svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\">\n                <path stroke=\"none\" d=\"M0 0h24v24H0z\" fill=\"none\"></path>\n                <path d=\"M14 3v4a1 1 0 0 0 1 1h4\"></path>\n                <path d=\"M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4\"></path>\n                <path d=\"M5 18h1.5a1.5 1.5 0 0 0 0 -3h-1.5v6\"></path>\n                <path d=\"M17 18h2\"></path>\n                <path d=\"M20 15h-3v6\"></path>\n                <path d=\"M11 15v6h1a2 2 0 0 0 2 -2v-2a2 2 0 0 0 -2 -2h-1z\"></path>\n            </svg>\n            ".concat(dt.i18n('buttons.pdf', 'PDF'));
    },
    action: function action(e, dt) {
      window.location = _buildUrl(dt, 'pdf');
    }
  };
  DataTable.ext.buttons.postPdf = {
    className: 'buttons-pdf',
    text: function text(dt) {
      return "<svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\">\n                <path stroke=\"none\" d=\"M0 0h24v24H0z\" fill=\"none\"></path>\n                <path d=\"M14 3v4a1 1 0 0 0 1 1h4\"></path>\n                <path d=\"M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4\"></path>\n                <path d=\"M5 18h1.5a1.5 1.5 0 0 0 0 -3h-1.5v6\"></path>\n                <path d=\"M17 18h2\"></path>\n                <path d=\"M20 15h-3v6\"></path>\n                <path d=\"M11 15v6h1a2 2 0 0 0 2 -2v-2a2 2 0 0 0 -2 -2h-1z\"></path>\n            </svg>\n            ".concat(dt.i18n('buttons.pdf', 'PDF'));
    },
    action: function action(e, dt) {
      var url = dt.ajax.url() || window.location.href;
      var params = _buildParams(dt, 'pdf');
      _downloadFromUrl(url, params);
    }
  };
  DataTable.ext.buttons.print = {
    className: 'buttons-print',
    text: function text(dt) {
      return "<svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\">\n                    <path stroke=\"none\" d=\"M0 0h24v24H0z\" fill=\"none\"></path>\n                    <path d=\"M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2\"></path>\n                    <path d=\"M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4\"></path>\n                    <path d=\"M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z\"></path>\n                </svg>\n                ".concat(dt.i18n('buttons.print', BotbleVariables.languages.tables.print ? BotbleVariables.languages.tables.print : 'Print'));
    },
    action: function action(e, dt) {
      window.location = _buildUrl(dt, 'print');
    }
  };
  DataTable.ext.buttons.reset = {
    className: 'buttons-reset',
    text: function text(dt) {
      return "<svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\">\n                    <path stroke=\"none\" d=\"M0 0h24v24H0z\" fill=\"none\"></path>\n                    <path d=\"M9 11l-4 4l4 4m-4 -4h11a4 4 0 0 0 0 -8h-1\"></path>\n                </svg>\n                ".concat(dt.i18n('buttons.reset', BotbleVariables.languages.tables.reset ? BotbleVariables.languages.tables.reset : 'Reset'));
    },
    action: function action() {
      $('.table thead input').val('').keyup();
      $('.table thead select').val('').change();
    }
  };
  DataTable.ext.buttons.reload = {
    className: 'buttons-reload',
    text: function text(dt) {
      return "\n                <svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\">\n                    <path stroke=\"none\" d=\"M0 0h24v24H0z\" fill=\"none\"/>\n                    <path d=\"M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4\" />\n                    <path d=\"M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4\" />\n                </svg>\n                ".concat(dt.i18n('buttons.reload', BotbleVariables.languages.tables.reload ? BotbleVariables.languages.tables.reload : 'Reload'));
    },
    action: function action(e, dt) {
      dt.draw(false);
    }
  };
  DataTable.ext.buttons.create = {
    className: 'buttons-create',
    text: function text(dt) {
      return "<svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\">\n                <path stroke=\"none\" d=\"M0 0h24v24H0z\" fill=\"none\"></path>\n                <path d=\"M12 5l0 14\"></path>\n                <path d=\"M5 12l14 0\"></path>\n            </svg>\n            ".concat(dt.i18n('buttons.create', 'Create'));
    },
    action: function action() {
      window.location = window.location.href.replace(/\/+$/, '') + '/create';
    }
  };
  if (typeof DataTable.ext.buttons.copyHtml5 !== 'undefined') {
    $.extend(DataTable.ext.buttons.copyHtml5, {
      text: function text(dt) {
        return "<svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\">\n                    <path stroke=\"none\" d=\"M0 0h24v24H0z\" fill=\"none\"></path>\n                    <path d=\"M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2\"></path>\n                    <path d=\"M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z\"></path>\n                </svg>\n                ".concat(dt.i18n('buttons.copy', 'Copy'));
      }
    });
  }
  var TableManagement = /*#__PURE__*/function () {
    function TableManagement() {
      _classCallCheck(this, TableManagement);
      this.currentTableHash = window.DATATABLES_RANDOM_HASH || "";
      this.init();
      this.handleActionsRow();
      this.handleActionsExport();
    }
    return _createClass(TableManagement, [{
      key: "init",
      value: function init() {
        // Add error handling for DataTables AJAX requests
        if ($.fn.dataTable) {
          $.fn.dataTable.ext.errMode = 'none';
          $(document).on('error.dt', function (e, settings, techNote, message) {
            Botble.handleDatatableError({
              responseJSON: {
                message: message
              }
            });
          });
        }
        $(document).on('change', '.table-check-all', function (event) {
          var _self = $(event.currentTarget);
          var set = _self.attr('data-set');
          var checked = _self.prop('checked');
          $(set).each(function (index, el) {
            if (checked) {
              $(el).prop('checked', true).trigger('change');
            } else {
              $(el).prop('checked', false).trigger('change');
            }
          });
        });
        $(document).find('.table-check-all').closest('th').removeAttr('title');
        $(document).on('change', '.checkboxes', function (event) {
          var _self = $(event.currentTarget);
          var table = _self.closest('.table-wrapper').find('.table').prop('id');
          var checkboxAll = _self.closest('.table-wrapper').find('.table-check-all');
          var ids = [];
          var $table = $('#' + table);
          $table.find('.checkboxes:checked').each(function (i, el) {
            ids[i] = $(el).val();
          });
          var row = _self.closest('tr');
          if (_self.prop('checked')) {
            row.addClass('selected');
          } else {
            row.removeClass('selected');
          }
          if (ids.length !== $table.find('.checkboxes').length) {
            checkboxAll.prop('checked', false);
            if (ids.length > 0) {
              checkboxAll.prop('indeterminate', true);
            } else {
              checkboxAll.prop('indeterminate', false);
            }
          } else {
            checkboxAll.prop('checked', true);
            checkboxAll.prop('indeterminate', false);
          }
        });
        $(document).on('click', '.btn-show-table-options', function (event) {
          event.preventDefault();
          $(event.currentTarget).closest('.table-wrapper').find('.table-configuration-wrap').slideToggle(500);
        });
        $(document).on('click', '.action-item', function (event) {
          event.preventDefault();
          var span = $(event.currentTarget).find('span[data-href]');
          var action = span.data('action');
          var url = span.data('href');
          if (action && url !== '#') {
            window.location.href = url;
          }
        });
        this.initRandomHash();
      }
    }, {
      key: "initRandomHash",
      value: function initRandomHash() {
        var localRandomHash = localStorage.getItem('DataTables_Random_Hash');
        if (!localRandomHash && !this.currentTableHash) {
          return;
        }
        if (localRandomHash !== this.currentTableHash) {
          Object.keys(localStorage).filter(function (key) {
            return key.startsWith('DataTables_');
          }).forEach(function (key) {
            localStorage.removeItem(key);
          });
          localStorage.setItem('DataTables_Random_Hash', this.currentTableHash);
          window.location.reload();
        }
      }
    }, {
      key: "handleActionsRow",
      value: function handleActionsRow() {
        var _this = this;
        var that = this;
        $(document).on('click', '.deleteDialog', function (event) {
          event.preventDefault();
          var _self = $(event.currentTarget);
          var url = _self.data('section');
          if (!url) {
            url = _self.prop('href');
          }
          $('.delete-crud-entry').data('section', url).data('parent-table', _self.closest('.table').prop('id'));
          $('.modal-confirm-delete').modal('show');
        });
        $('.delete-crud-entry').on('click', function (event) {
          event.preventDefault();
          var _self = $(event.currentTarget);
          Botble.showButtonLoading(_self);
          var deleteURL = _self.data('section');
          $httpClient.make()["delete"](deleteURL).then(function (_ref) {
            var data = _ref.data;
            window.LaravelDataTables[_self.data('parent-table')].row($("a[data-section=\"".concat(deleteURL, "\"]")).closest('tr')).remove().draw();
            Botble.showSuccess(data.message);
            _self.closest('.modal').modal('hide');
          })["catch"](function () {
            $('.modal-confirm-delete').modal('hide');
          })["finally"](function () {
            Botble.hideButtonLoading(_self);
          });
        });
        $(document).on('click', '.delete-many-entry-trigger', function (event) {
          event.preventDefault();
          var _self = $(event.currentTarget);
          var table = _self.closest('.table-wrapper').find('.table').prop('id');
          var ids = [];
          $("#".concat(table)).find('.checkboxes:checked').each(function (i, el) {
            ids[i] = $(el).val();
          });
          if (ids.length === 0) {
            Botble.showError(BotbleVariables.languages.tables.please_select_record ? BotbleVariables.languages.tables.please_select_record : 'Please select at least one record to perform this action!');
            return false;
          }
          $('.delete-many-entry-button').data('href', _self.prop('href')).data('parent-table', table).data('class-item', _self.data('class-item'));
          $('.delete-many-modal').modal('show');
        });
        $('.delete-many-entry-button').on('click', function (event) {
          event.preventDefault();
          var _self = $(event.currentTarget);
          Botble.showButtonLoading(_self);
          var $table = $("#".concat(_self.data('parent-table')));
          var ids = [];
          $table.find('.checkboxes:checked').each(function (i, el) {
            ids[i] = $(el).val();
          });
          $httpClient.make()["delete"](_self.data('href'), {
            ids: ids,
            "class": _self.data('class-item')
          }).then(function (_ref2) {
            var data = _ref2.data;
            Botble.showSuccess(data.message);
            $table.find('.table-check-all').prop('checked', false).prop('indeterminate', false);
            window.LaravelDataTables[_self.data('parent-table')].draw();
            _self.closest('.modal').modal('hide');
          })["finally"](function () {
            Botble.hideButtonLoading(_self);
          });
        });
        $(document).on('click', '[data-trigger-bulk-action]', function (event) {
          event.preventDefault();
          var _self = $(event.currentTarget);
          var tableId = _self.closest('.table-wrapper').find('.table').prop('id');
          var ids = [];
          $("#".concat(tableId)).find('.checkboxes:checked').each(function (i, el) {
            return ids.push($(el).val());
          });
          if (ids.length === 0) {
            Botble.showError(BotbleVariables.languages.tables.please_select_record ? BotbleVariables.languages.tables.please_select_record : 'Please select at least one record to perform this action!');
            return false;
          }
          $('.confirm-trigger-bulk-actions-button').data('href', _self.prop('href')).data('method', _self.data('method')).data('table-id', tableId).data('table-target', _self.data('table-target')).data('target', _self.data('target'));
          var modal = $('.bulk-action-confirm-modal');
          modal.find('h3').text(_self.data('confirmation-modal-title'));
          modal.find('.modal-body > .text-muted').text(_self.data('confirmation-modal-message'));
          modal.find('button.btn[data-bs-dismiss="modal"]').text(_self.data('confirmation-modal-cancel-button'));
          modal.find('button.confirm-trigger-bulk-actions-button').text(_self.data('confirmation-modal-button'));
          modal.modal('show');
        });
        $(document).on('click', '.confirm-trigger-bulk-actions-button', function (event) {
          event.preventDefault();
          var _self = $(event.currentTarget);
          Botble.showButtonLoading(_self);
          var tableId = _self.data('table-id');
          var method = _self.data('method').toLowerCase() || 'post';
          var $table = $("#".concat(tableId));
          var ids = [];
          $table.find('.checkboxes:checked').each(function (i, el) {
            return ids.push($(el).val());
          });
          $httpClient.make()[method](_self.data('href'), {
            ids: ids,
            bulk_action: 1,
            bulk_action_table: _self.data('table-target'),
            bulk_action_target: _self.data('target')
          }).then(function (_ref3) {
            var data = _ref3.data;
            Botble.showSuccess(data.message);
            $table.find('.table-check-all').prop('checked', false).prop('indeterminate', false);
            window.LaravelDataTables[tableId].draw();
            _self.closest('.modal').modal('hide');
          })["catch"](function () {
            _self.closest('.modal').modal('hide');
          })["finally"](function () {
            Botble.hideButtonLoading(_self);
          });
        });
        $(document).on('click', '[data-dt-single-action]', function (event) {
          event.preventDefault();
          var _self = $(event.currentTarget);
          var tableId = _self.closest('.table-wrapper').find('.table').prop('id');
          if (_self.data('confirmation-modal')) {
            $('.confirm-trigger-single-action-button').data('href', _self.prop('href')).data('method', _self.data('method')).data('table-id', tableId);
            var modal = $('.single-action-confirm-modal');
            modal.find('.modal-body > h3').text(_self.data('confirmation-modal-title'));
            modal.find('.modal-body > .text-muted').text(_self.data('confirmation-modal-message'));
            modal.find('button.btn[data-bs-dismiss="modal"]').text(_self.data('confirmation-modal-cancel-button'));
            modal.find('button.confirm-trigger-single-action-button').text(_self.data('confirmation-modal-button'));
            modal.modal('show');
          } else {
            triggerSingleAction(tableId, _self.prop('href'), _self.data('method'));
          }
        });
        $(document).on('click', '.confirm-trigger-single-action-button', function (event) {
          event.preventDefault();
          var _self = $(event.currentTarget);
          Botble.showButtonLoading(_self);
          triggerSingleAction(_self.data('table-id'), _self.data('href'), _self.data('method'), function () {
            _self.closest('.modal').modal('hide');
            Botble.hideButtonLoading(_self);
          }, function () {
            Botble.hideButtonLoading(_self);
          });
        });
        var triggerSingleAction = function triggerSingleAction(tableId, url, method, onSuccess, onError) {
          var $table = $("#".concat(tableId));
          var $method = method.toLowerCase() || 'post';
          $httpClient.make()[$method](url).then(function (_ref4) {
            var data = _ref4.data;
            Botble.showSuccess(data.message);
            $table.find('.table-check-all').prop('checked', false).prop('indeterminate', false);
            window.LaravelDataTables[tableId].draw();
            typeof onSuccess === 'function' && onSuccess.apply(_this, data);
          })["finally"](function () {
            Botble.hideButtonLoading(_self);
          });
        };
        $(document).on('click', '.bulk-change-item', function (event) {
          event.preventDefault();
          var _self = $(event.currentTarget);
          var table = _self.closest('.table-wrapper').find('.table').prop('id');
          var ids = [];
          $('#' + table).find('.checkboxes:checked').each(function (i, el) {
            ids[i] = $(el).val();
          });
          if (ids.length === 0) {
            Botble.showError(BotbleVariables.languages.tables.please_select_record ? BotbleVariables.languages.tables.please_select_record : 'Please select at least one record to perform this action!');
            return false;
          }
          that.loadBulkChangeData(_self);
          $('.confirm-bulk-change-button').data('parent-table', table).data('class-item', _self.data('class-item')).data('key', _self.data('key')).data('url', _self.data('save-url'));
          $('.modal-bulk-change-items').modal('show');
        });
        $(document).on('click', '.confirm-bulk-change-button', function (event) {
          event.preventDefault();
          var _self = $(event.currentTarget);
          var value = _self.closest('.modal').find('.input-value').val();
          var inputKey = _self.data('key');
          var $table = $('#' + _self.data('parent-table'));
          var ids = [];
          $table.find('.checkboxes:checked').each(function (i, el) {
            ids[i] = $(el).val();
          });
          Botble.showButtonLoading(_self);
          $httpClient.make().post(_self.data('url'), {
            ids: ids,
            key: inputKey,
            value: value,
            "class": _self.data('class-item')
          }).then(function (_ref5) {
            var data = _ref5.data;
            Botble.showSuccess(data.message);
            $table.find('.table-check-all').prop('checked', false).prop('indeterminate', false);
            $.each(ids, function (index, item) {
              window.LaravelDataTables[_self.data('parent-table')].row($table.find('.checkboxes[value="' + item + '"]').closest('tr')).remove().draw();
            });
            _self.closest('.modal').modal('hide');
          })["finally"](function () {
            Botble.hideButtonLoading(_self);
          });
        });
        $(document).on('keyup', '.table-search-input input[type=search]', function (event) {
          var $searchInput = $(event.currentTarget);
          setTimeout(function () {
            var searchValue = $searchInput.val();
            if (searchValue) {
              $searchInput.closest('label').find('.search-icon').hide();
              $searchInput.closest('label').find('.search-reset-icon').show();
            } else {
              $searchInput.closest('label').find('.search-icon').show();
              $searchInput.closest('label').find('.search-reset-icon').hide();
            }
            $searchInput.closest('.table-wrapper').find('table').DataTable().search(searchValue).draw();
          }, 200);
        });
        $(document).on('click', '.table-search-input .search-reset-icon', function (event) {
          var $searchInput = $(event.currentTarget).closest('.table-search-input').find('input[type=search]');
          $searchInput.val('');
          $searchInput.closest('.table-wrapper').find('table').DataTable().search('').draw();
        });
        $(document).on('click', '[data-bb-toggle="dt-buttons"]', function (event) {
          var target = $(event.currentTarget);
          var tableId = target.attr('aria-controls');
          var buttonTarget = target.data('bb-target');
          $("".concat(buttonTarget, "[aria-controls=\"").concat(tableId, "\"]")).trigger('click');
        });
        $(document).on('click', '[data-bb-toggle="dt-exports"]', function (event) {
          var target = $(event.currentTarget);
          var tableId = target.attr('aria-controls');
          var value = target.data('bb-target');
          var dt = window.LaravelDataTables[tableId];
          var url = dt.ajax.url() || window.location.href;
          var params = _buildParams(dt, value);
          _downloadFromUrl(url, params);
        });
        var $columnsVisibleDropdowns = document.querySelectorAll('[data-bb-toggle="dt-columns-visibility-dropdown"]');
        var $formDirty = {};
        if ($columnsVisibleDropdowns.length) {
          var _iterator = _createForOfIteratorHelper($columnsVisibleDropdowns),
            _step;
          try {
            for (_iterator.s(); !(_step = _iterator.n()).done;) {
              var $dropdown = _step.value;
              $dropdown.addEventListener('hidden.bs.dropdown', function (event) {
                var target = $(event.currentTarget);
                var tableId = target.attr('aria-controls');
                var form = target.find('form[data-bb-toggle="dt-columns-visibility"]');
                if (!$formDirty[tableId]) {
                  return;
                }
                $httpClient.make().putForm(form.prop('action'), new FormData(form[0])).then(function () {
                  var dt = window.LaravelDataTables[tableId];
                  dt.state.clear();
                  location.reload();
                  $formDirty[tableId] = false;
                });
              });
            }
          } catch (err) {
            _iterator.e(err);
          } finally {
            _iterator.f();
          }
        }
        $(document).on('change', '[data-bb-toggle="dt-columns-visibility-toggle"]', function (event) {
          var target = $(event.currentTarget).closest('.dropdown');
          var tableId = target.attr('aria-controls');
          $formDirty[tableId] = true;
        });
      }
    }, {
      key: "loadBulkChangeData",
      value: function loadBulkChangeData($element) {
        var $modal = $('.modal-bulk-change-items');
        $httpClient.make().get($modal.find('.confirm-bulk-change-button').data('load-url'), {
          "class": $element.data('class-item'),
          key: $element.data('key')
        }).then(function (response) {
          var data = $.map(response.data.data, function (value, key) {
            return {
              id: key,
              name: value
            };
          });
          var $parent = $('.modal-bulk-change-content');
          $parent.html(response.data.html);
          var $input = $modal.find('input[type=text].input-value');
          if ($input.length) {
            $input.typeahead({
              source: data
            });
            $input.data('typeahead').source = data;
          }
          Botble.initResources();
        });
      }
    }, {
      key: "handleActionsExport",
      value: function handleActionsExport() {
        $(document).on('click', '.export-data', function (event) {
          var _self = $(event.currentTarget);
          var table = _self.closest('.table-wrapper').find('.table').prop('id');
          var ids = [];
          $('#' + table).find('.checkboxes:checked').each(function (i, el) {
            ids[i] = $(el).val();
          });
          event.preventDefault();
          $httpClient.make().post(_self.prop('href'), {
            'ids-checked': ids
          }).then(function (_ref6) {
            var data = _ref6.data;
            var a = document.createElement('a');
            a.href = data.file;
            a.download = data.name;
            document.body.appendChild(a);
            a.trigger('click');
            a.remove();
          });
        });
      }
    }]);
  }();
  $.fn.dataTable.Buttons.defaults.dom.container.className = 'dt-buttons btn-group w-100 w-sm-auto';
  $.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';
  $(function () {
    new TableManagement();
  });
})(jQuery, jQuery.fn.dataTable);
/******/ })()
;