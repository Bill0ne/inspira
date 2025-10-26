/******/ (() => { // webpackBootstrap
/*!***************************************************!*\
  !*** ./platform/core/acl/resources/js/profile.js ***!
  \***************************************************/
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
/**
 * Created on 06/09/2015.
 */
var CropAvatar = /*#__PURE__*/function () {
  function CropAvatar($element) {
    _classCallCheck(this, CropAvatar);
    this.$container = $element;
    this.$avatarView = this.$container.find('.avatar-view');
    this.$triggerButton = this.$avatarView.find('.action .edit-avatar');
    this.$triggerButtonRemove = this.$avatarView.find('.action .remove-avatar');
    this.$avatar = this.$avatarView.find('img');
    this.$avatarModal = this.$container.find('#avatar-modal');
    this.$loading = this.$container.find('.loading');
    this.$avatarForm = this.$avatarModal.find('.avatar-form');
    this.$avatarSrc = this.$avatarForm.find('.avatar-src');
    this.$avatarData = this.$avatarForm.find('.avatar-data');
    this.$avatarInput = this.$avatarForm.find('.avatar-input');
    this.$avatarSave = this.$avatarForm.find('.avatar-save');
    this.$avatarWrapper = this.$avatarModal.find('.avatar-wrapper');
    this.$avatarPreview = this.$avatarModal.find('.avatar-preview');
    this.support = {
      fileList: !!$('<input type="file">').prop('files'),
      fileReader: !!window.FileReader,
      formData: !!window.FormData
    };
  }
  return _createClass(CropAvatar, [{
    key: "init",
    value: function init() {
      this.support.datauri = this.support.fileList && this.support.fileReader;
      if (!this.support.formData) {
        this.initIframe();
      }
      this.initTooltip();
      this.initModal();
      this.addListener();
    }
  }, {
    key: "addListener",
    value: function addListener() {
      this.$triggerButton.on('click', $.proxy(this.click, this));
      this.$avatarInput.on('change', $.proxy(this.change, this));
      this.$avatarForm.on('submit', $.proxy(this.submit, this));
      this.$triggerButtonRemove.on('click', function () {
        $httpClient.make().post($(this).attr('data-url')).then(function (response) {
          if (!response.data.error) {
            Botble.showSuccess(response.data.message);
            $('.image-preview').attr('src', response.data.data.url);
            $('.avatar-view').find('.action .remove-avatar').hide();
          }
        });
      });
    }
  }, {
    key: "initTooltip",
    value: function initTooltip() {
      this.$avatarView.tooltip({
        placement: 'bottom'
      });
    }
  }, {
    key: "initModal",
    value: function initModal() {
      if (this.$avatarModal.length && typeof this.$avatarModal.modal === 'function') {
        this.$avatarModal.modal('hide');
      }
      this.initPreview();
    }
  }, {
    key: "initPreview",
    value: function initPreview() {
      var url = this.$avatar.prop('src');
      this.$avatarPreview.empty().html('<img src="' + url + '" alt="avatar">');
    }
  }, {
    key: "initIframe",
    value: function initIframe() {
      var iframeName = 'avatar-iframe-' + Math.random().toString().replace('.', ''),
        $iframe = $('<iframe name="' + iframeName + '" style="display:none;"></iframe>'),
        firstLoad = true,
        _this = this;
      this.$iframe = $iframe;
      this.$avatarForm.attr('target', iframeName).after($iframe);
      this.$iframe.on('load', function () {
        var data, win, doc;
        try {
          win = this.contentWindow;
          doc = this.contentDocument;
          doc = doc ? doc : win.document;
          data = doc ? doc.body.innerText : null;
        } catch (e) {}
        if (data) {
          _this.submitDone(data);
        } else if (firstLoad) {
          firstLoad = false;
        } else {
          Botble.showError('Image upload failed!');
        }
        _this.submitEnd();
      });
    }
  }, {
    key: "click",
    value: function click() {
      if (this.$avatarModal.length && typeof this.$avatarModal.modal === 'function') {
        this.$avatarModal.modal('show');
      }
    }
  }, {
    key: "change",
    value: function change() {
      var files, file;
      if (this.support.datauri) {
        files = this.$avatarInput.prop('files');
        if (files.length > 0) {
          file = files[0];
          if (CropAvatar.isImageFile(file)) {
            this.read(file);
          }
        }
      } else {
        file = this.$avatarInput.val();
        if (CropAvatar.isImageFile(file)) {
          this.syncUpload();
        }
      }
    }
  }, {
    key: "submit",
    value: function submit() {
      if (!this.$avatarSrc.val() && !this.$avatarInput.val()) {
        Botble.showError('Please select image!');
        return false;
      }
      if (this.support.formData) {
        this.ajaxUpload();
        return false;
      }
    }
  }, {
    key: "read",
    value: function read(file) {
      var _this = this,
        fileReader = new FileReader();
      fileReader.readAsDataURL(file);
      fileReader.onload = function () {
        _this.url = this.result;
        _this.startCropper();
      };
    }
  }, {
    key: "startCropper",
    value: function startCropper() {
      var _this = this;
      if (this.active) {
        this.$img.cropper('replace', this.url);
      } else {
        this.$img = $('<img src="' + this.url + '" alt="avatar">');
        this.$avatarWrapper.empty().html(this.$img);
        this.$img.cropper({
          aspectRatio: 1,
          rotatable: true,
          preview: this.$avatarPreview.selector,
          done: function done(data) {
            var json = ['{"x":' + data.x, '"y":' + data.y, '"height":' + data.height, '"width":' + data.width + '}'].join();
            _this.$avatarData.val(json);
          }
        });
        this.active = true;
      }
    }
  }, {
    key: "stopCropper",
    value: function stopCropper() {
      if (this.active) {
        this.$img.cropper('destroy');
        this.$img.remove();
        this.active = false;
      }
    }
  }, {
    key: "ajaxUpload",
    value: function ajaxUpload() {
      var _this2 = this;
      var url = this.$avatarForm.attr('action');
      var data = new FormData(this.$avatarForm[0]);
      this.submitStart();
      $httpClient.make().post(url, data).then(function (response) {
        return _this2.submitDone(response.data);
      })["finally"](function () {
        return _this2.submitEnd();
      });
    }
  }, {
    key: "syncUpload",
    value: function syncUpload() {
      this.$avatarSave.trigger('click');
    }
  }, {
    key: "submitStart",
    value: function submitStart() {
      this.$loading.fadeIn();
      this.$avatarSave.attr('disabled', true).text('Saving...');
    }
  }, {
    key: "submitDone",
    value: function submitDone(data) {
      try {
        data = $.parseJSON(data);
      } catch (e) {}
      if (data && !data.error) {
        if (data.data) {
          this.url = data.data.url;
          if (this.support.datauri || this.uploaded) {
            this.uploaded = false;
            this.cropDone();
          } else {
            this.uploaded = true;
            this.$avatarSrc.val(this.url);
            this.startCropper();
          }
          $('.avatar-view').find('.action .remove-avatar').show();
          this.$avatarInput.val('');
          Botble.showSuccess(data.message);
        } else {
          Botble.showError(data.message);
        }
      } else {
        Botble.showError(data.message);
      }
    }
  }, {
    key: "submitEnd",
    value: function submitEnd() {
      this.$loading.fadeOut();
      this.$avatarSave.removeAttr('disabled').text('Save');
    }
  }, {
    key: "cropDone",
    value: function cropDone() {
      this.$avatarSrc.val('');
      this.$avatarData.val('');
      this.$avatar.prop('src', this.url);
      $('.user-menu img').prop('src', this.url);
      $('.user.dropdown img').prop('src', this.url);
      this.stopCropper();
      this.initModal();
    }
  }], [{
    key: "isImageFile",
    value: function isImageFile(file) {
      if (file.type) {
        return /^image\/\w+$/.test(file.type);
      }
      return /\.(jpg|jpeg|png|gif)$/.test(file);
    }
  }]);
}();
$(function () {
  new CropAvatar($('.crop-avatar')).init();
});
/******/ })()
;