/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/*!***************************************************************!*\
  !*** ./platform/packages/shortcode/resources/js/shortcode.js ***!
  \***************************************************************/


$(function () {
  $.fn.serializeObject = function () {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function () {
      if (o[this.name]) {
        if (!o[this.name].push) {
          o[this.name] = [o[this.name]];
        }
        o[this.name].push(this.value || '');
      } else {
        o[this.name] = this.value || '';
      }
    });
    return o;
  };
  var $shortcodeListModal = $('#shortcode-list-modal');
  var $shortcodeFormModal = $('#shortcode-modal');
  $('[data-bb-toggle="shortcode-item-radio"]').on('change', function () {
    $('[data-bb-toggle="shortcode-use"]').prop('disabled', false).removeClass('disabled');
  });
  $('[data-bb-toggle="shortcode-add-single"]').on('click', function (event) {
    event.preventDefault();
    var formElement = $('.shortcode-modal').find('.shortcode-data-form');
    var formData = formElement.serializeObject();
    var attributes = '';
    $.each(formData, function (name, value) {
      var element = formElement.find('*[name="' + name + '"]');
      var shortcodeAttribute = element.data('shortcode-attribute');
      if ((!shortcodeAttribute || shortcodeAttribute !== 'content') && value) {
        name = name.replace('[]', '');
        if (value && typeof value === 'string') {
          value = value.replace(/"([^"]*)"/g, '“$1”');
          value = value.replace(/"/g, '“');
        }
        if (element.data('shortcode-attribute') !== 'content') {
          name = name.replace('[]', '');
          attributes += ' ' + name + '="' + value + '"';
        }
      }
    });
    var content = '';
    var contentElement = formElement.find('*[data-shortcode-attribute=content]');
    if (contentElement != null && contentElement.val() != null && contentElement.val() !== '') {
      content = contentElement.val();
    }
    var $shortCodeKey = $(this).closest('.shortcode-modal').find('.shortcode-input-key').val();
    var editorInstance = $('.add_shortcode_btn_trigger').data('result');
    var shortcode = '[' + $shortCodeKey + attributes + ']' + content + '[/' + $shortCodeKey + ']';
    if (window.EDITOR && window.EDITOR.CKEDITOR && $('.editor-ckeditor').length > 0) {
      window.EDITOR.CKEDITOR[editorInstance].commands.execute('shortcode', shortcode);
    } else if ($('.editor-tinymce').length > 0) {
      tinymce.get(editorInstance).execCommand('mceInsertContent', false, shortcode);
    } else {
      var coreInsertShortCodeEvent = new CustomEvent('core-insert-shortcode', {
        detail: {
          shortcode: shortcode
        }
      });
      document.dispatchEvent(coreInsertShortCodeEvent);
    }
    $(this).closest('.modal').modal('hide');
  });
  $(document).on('click', '[data-bb-toggle="shortcode-list-modal"]', function () {
    $shortcodeListModal.modal('show');
  });
  $('[data-bb-toggle="shortcode-select"]').on('dblclick', function (event) {
    var $currentTarget = $(event.currentTarget);
    triggerShortcode($currentTarget);
  });
  $('[data-bb-toggle="shortcode-use"]').on('click', function () {
    var $shortcodeSelected = $shortcodeListModal.find('.shortcode-item-input:checked').closest('.shortcode-item-wrapper');
    triggerShortcode($shortcodeSelected);
    $('[data-bb-toggle="shortcode-item-radio"]').prop('checked', false);
    $('[data-bb-toggle="shortcode-use"]').prop('disabled', true).addClass('disabled');
  });
  $('[data-bb-toggle="shortcode-button-use"]').on('click', function (event) {
    var $shortcodeSelected = $(event.currentTarget).closest('.shortcode-item-wrapper');
    triggerShortcode($shortcodeSelected);
  });
  function triggerShortcode(el) {
    shortcodeCallback({
      href: el.attr('href'),
      key: el.data('key'),
      name: el.data('name'),
      description: el.data('description')
    });
  }
  function shortcodeCallback() {
    var params = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    var href = params.href,
      key = params.key,
      name = params.name,
      _params$description = params.description,
      description = _params$description === void 0 ? null : _params$description,
      _params$data = params.data,
      data = _params$data === void 0 ? {} : _params$data,
      _params$update = params.update,
      update = _params$update === void 0 ? false : _params$update,
      _params$previewImage = params.previewImage,
      previewImage = _params$previewImage === void 0 ? null : _params$previewImage;
    $('.shortcode-admin-config').html('');
    var $addShortcodeButton = $('.shortcode-modal button[data-bb-toggle="shortcode-add-single"]');
    $addShortcodeButton.text($addShortcodeButton.data(update ? 'update-text' : 'add-text'));
    $('.shortcode-modal .modal-title').text(name);
    if (previewImage != null && previewImage !== '') {
      $('.shortcode-modal .shortcode-preview-image-link').attr('href', previewImage).show();
    } else {
      $('.shortcode-modal .shortcode-preview-image-link').hide();
    }
    $('.shortcode-modal').modal('show');
    var $modalLoading = $shortcodeFormModal.find('.modal-content');
    Botble.showLoading($modalLoading);
    $httpClient.make().post(href, data).then(function (_ref) {
      var data = _ref.data;
      $('.shortcode-data-form').trigger('reset');
      $('.shortcode-input-key').val(key);
      $('.shortcode-admin-config').html(data.data);
      Botble.hideLoading($modalLoading);
      Botble.initResources();
      Botble.initMediaIntegrate();
      Botble.initFieldCollapse();
      document.dispatchEvent(new CustomEvent('core-shortcode-config-loaded'));
    });
  }
  $shortcodeFormModal.on('show.bs.modal', function () {
    $shortcodeListModal.modal('hide');
    $('[data-bb-toggle="shortcode-item-radio"]').prop('checked', false);
    $('[data-bb-toggle="shortcode-use"]').prop('disabled', true).addClass('disabled');
  });
  $(document).on('ckeditor-bb-shortcode-callback', function (e) {
    var _e$detail = e.detail,
      shortcode = _e$detail.shortcode,
      options = _e$detail.options;
    shortcodeCallback({
      key: shortcode,
      href: options.url,
      previewImage: ''
    });
  });
  $(document).on('ckeditor-bb-shortcode-edit', function (e) {
    var _e$detail2 = e.detail,
      shortcode = _e$detail2.shortcode,
      name = _e$detail2.name;
    var $shortcodeItem = $("[data-bb-toggle=\"shortcode-select\"][data-key=\"".concat(name, "\"]"));
    var description = $shortcodeItem.length > 0 ? $shortcodeItem.data('description') : '';
    shortcodeCallback({
      key: name,
      href: $shortcodeItem.data('url'),
      data: {
        key: name,
        code: shortcode
      },
      name: $shortcodeItem.data('name'),
      description: description,
      previewImage: '',
      update: true
    });
  });
  $('.shortcode-list-modal').on('keyup', 'input[type="search"]', function (e) {
    e.preventDefault();
    var search = $(this).val().toLowerCase();
    $('.shortcode-item-wrapper').each(function (index, element) {
      var $element = $(element);
      var name = $element.data('name').toLowerCase();
      var description = $element.data('description').toLowerCase();
      if (name.includes(search) || description.includes(search)) {
        $element.parent().show();
      } else {
        $element.parent().hide();
      }
    });
    if ($('.shortcode-item-wrapper:visible').length === 0) {
      $('.shortcode-empty').show();
    } else {
      $('.shortcode-empty').hide();
    }
  }).on('click', '[data-bb-toggle="shortcode-clear-search"]', function (e) {
    e.preventDefault();
    $(this).closest('.shortcode-list-modal').find('input[type="search"]').val('').trigger('keyup').trigger('focus');
  });
});
/******/ })()
;