/******/ (() => { // webpackBootstrap
/*!*************************************************************!*\
  !*** ./platform/plugins/analytics/resources/js/settings.js ***!
  \*************************************************************/
$(function () {
  var initializeParseJsonSetting = function initializeParseJsonSetting() {
    var $setting = document.getElementById('google-analytics-settings');
    var $field = document.createElement('input');
    var analyticsJsonSettingUrl = null;
    createAnalyticsJsonConfigFileField();
    $(document).on('click', '[data-bb-toggle="analytics-trigger-upload-json"]', function (e) {
      e.preventDefault();
      analyticsJsonSettingUrl = e.currentTarget.dataset.url;
      $field.click();
    });
    function createAnalyticsJsonConfigFileField() {
      $field.type = 'file';
      $field.accept = 'application/json';
      $field.classList.add('d-none');
      $field.addEventListener('change', function (e) {
        var target = e.currentTarget;
        var editor = $setting.getElementsByClassName('CodeMirror');
        var codeMirror = null;
        if (editor.length > 0) {
          codeMirror = editor[0].CodeMirror;
        } else {
          return;
        }
        if (target !== null && target !== void 0 && target.files && target.files.length === 0) {
          return;
        }
        var data = new FormData();
        data.set('json', target.files[0]);
        Botble.showLoading($setting);
        $httpClient.make().postForm(analyticsJsonSettingUrl, data).then(function (_ref) {
          var data = _ref.data;
          codeMirror.setValue(data.data.content);
        })["catch"](function (error) {
          if (!error.response || !error.response.data) {
            return;
          }
          codeMirror.setValue(error.response.data.data.content);
        })["finally"](function () {
          Botble.hideLoading($setting);
          target.value = '';
        });
      });
      document.body.appendChild($field);
    }
  };
  initializeParseJsonSetting();
});
/******/ })()
;