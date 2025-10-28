/******/ (() => { // webpackBootstrap
/*!*******************************************************!*\
  !*** ./platform/core/base/resources/js/crop-image.js ***!
  \*******************************************************/
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
var CropImage = /*#__PURE__*/function () {
  function CropImage() {
    var _this = this;
    _classCallCheck(this, CropImage);
    _defineProperty(this, "modal", $(document).find('.crop-image-modal'));
    _defineProperty(this, "image", this.modal.find('.cropper-image'));
    _defineProperty(this, "cropper", null);
    this.modal.on('change', 'input[type="file"]', function (e) {
      var files = e.target.files;
      var reader;
      var file;
      if (files && files.length > 0) {
        file = files[0];
        if (URL) {
          _this.image.prop('src', URL.createObjectURL(file));
        } else if (FileReader) {
          reader = new FileReader();
          reader.onload = function () {
            _this.image.prop('src', reader.result);
          };
          reader.readAsDataURL(file);
        }
      }
      _this.init();
    }).on('click', 'button[type="submit"]', function (e) {
      e.preventDefault();
      var button = $(e.currentTarget);
      var form = _this.modal.find('form');
      var canvas = _this.cropper.getCroppedCanvas({
        width: 160,
        height: 160
      });
      canvas.toBlob(function (blob) {
        var formData = new FormData();
        formData.append(form.find('input[type="file"]').prop('name'), blob);
        $httpClient.make().withButtonLoading(button).post(form.prop('action'), formData).then(function (_ref) {
          var data = _ref.data;
          _this.updateImage(data.data.url);
          Botble.showSuccess(data.message);
          _this.modal.modal('hide');
        });
      });
    }).on('shown.bs.modal', function (e) {
      var originalImage = $(e.relatedTarget).closest('.crop-image-container').find('.crop-image-original');
      var image = new Image();
      image.src = originalImage.prop('src');
      image.onload = function () {
        _this.image.prop('src', image.src);
        _this.init();
      };
    }).on('hidden.bs.modal', function () {
      _this.destroy();
    });
    $(document).on('click', '[data-bb-toggle="delete-avatar"]', function (e) {
      e.preventDefault();
      var button = $(e.currentTarget);
      $httpClient.make().post(button.prop('href')).then(function (_ref2) {
        var data = _ref2.data;
        _this.updateImage(data.data.url);
        Botble.showSuccess(data.message);
        _this.modal.modal('hide');
      });
    });
  }
  return _createClass(CropImage, [{
    key: "init",
    value: function init() {
      this.cropper && this.cropper.destroy();
      this.cropper = new Cropper(this.image[0], {
        aspectRatio: 1,
        preview: '.img-preview'
      });
    }
  }, {
    key: "destroy",
    value: function destroy() {
      this.cropper.destroy();
      this.cropper = null;
      this.image.prop('src', '');
      this.modal.find('input[type="file"]').val('');
    }
  }, {
    key: "updateImage",
    value: function updateImage(url) {
      $(document).find('.crop-image-original').each(function (i, el) {
        if ($(el).is('img')) {
          $(el).prop('src', url);
        } else {
          $(el).css('background-image', "url(".concat(url, ")"));
        }
      });
    }
  }]);
}();
$(function () {
  new CropImage();
});
/******/ })()
;