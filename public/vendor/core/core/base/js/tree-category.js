/******/ (() => { // webpackBootstrap
/*!**********************************************************!*\
  !*** ./platform/core/base/resources/js/tree-category.js ***!
  \**********************************************************/
$(function () {
  var $treeWrapper = $('.file-tree-wrapper');
  var $formLoading = $('.tree-form-container');
  function initNestable() {
    var $dd = $(document).find('.dd');
    if (typeof $dd.nestable === 'function') {
      $dd.nestable({
        group: 1,
        maxDepth: $dd.data('depth') || 5,
        listClass: 'list-group dd-list',
        emptyClass: "dd-empty\">".concat($dd.data('empty-text'), "</div><div class=\""),
        callback: function callback(l, e) {
          $httpClient.make().put($treeWrapper.data('update-url'), {
            data: $dd.nestable('serialize')
          }).then(function (_ref) {
            var data = _ref.data;
            Botble.showSuccess(data.message);
          });
        }
      });
    }
  }
  initNestable();
  function reloadForm(data) {
    $('.tree-form-body').html(data);
    initNestable();
    Botble.initResources();
    Botble.handleCounterUp();
    if (window.EditorManagement) {
      window.EDITOR = new EditorManagement().init();
    }
    Botble.initMediaIntegrate();
  }
  function fetchData(url, $el) {
    Botble.showLoading($formLoading);
    $treeWrapper.find('.dd3-content.active').removeClass('active');
    if ($el) {
      $el.addClass('active');
    }
    $httpClient.make().get(url).then(function (_ref2) {
      var data = _ref2.data;
      return reloadForm(data.data);
    })["finally"](function () {
      return Botble.hideLoading($formLoading);
    });
  }
  $treeWrapper.on('click', '.fetch-data', function (event) {
    event.preventDefault();
    var currentTarget = $(event.currentTarget);
    if (currentTarget.data('href')) {
      fetchData(currentTarget.data('href'), currentTarget.closest('.dd3-content'));
    } else {
      $treeWrapper.find('.dd3-content.active').removeClass('active');
      currentTarget.closest('.dd3-content').addClass('active');
    }
  });
  $(document).on('click', '.tree-categories-create', function (event) {
    event.preventDefault();
    var $this = $(event.currentTarget);
    loadCreateForm($this.attr('href'));
  });
  var searchParams = new URLSearchParams(window.location.search);
  function loadCreateForm(url) {
    var data = {};
    if (searchParams.get('ref_lang')) {
      data.ref_lang = searchParams.get('ref_lang');
    }
    Botble.showLoading($formLoading);
    $httpClient.make().get(url, data).then(function (_ref3) {
      var data = _ref3.data;
      return reloadForm(data.data);
    })["finally"](function () {
      return Botble.hideLoading($formLoading);
    });
  }
  function reloadTree(activeId, callback) {
    $httpClient.make().get($treeWrapper.data('url') || window.location.href).then(function (_ref4) {
      var data = _ref4.data;
      $treeWrapper.html(data.data);
      handleOpenCollapseState();
      if (jQuery().tooltip) {
        $('[data-bs-toggle="tooltip"]').tooltip({
          placement: 'top',
          boundary: 'window'
        });
      }
      if (callback) {
        callback();
      }
    });
  }
  $(document).on('click', '#list-others-language a', function (event) {
    event.preventDefault();
    fetchData($(event.currentTarget).prop('href'));
  });
  $(document).on('submit', '.tree-form-container form', function (event) {
    var _event$originalEvent;
    event.preventDefault();
    var $form = $(event.currentTarget);
    var formData = new FormData(event.currentTarget);
    var submitter = (_event$originalEvent = event.originalEvent) === null || _event$originalEvent === void 0 ? void 0 : _event$originalEvent.submitter;
    var saveAndEdit = false;
    if (submitter && submitter.name) {
      saveAndEdit = submitter.value === 'apply';
      formData.append(submitter.name, submitter.value);
    }
    $form.find('select').each(function () {
      if ($(this).val() == null) {
        formData.append($(this).attr('name'), '');
      }
    });
    var method = $form.attr('method').toLowerCase() || 'post';
    $httpClient.make().withLoading($formLoading)[method]($form.attr('action'), formData).then(function (_ref5) {
      var data = _ref5.data;
      Botble.showSuccess(data.message);
      var $createButton = $('.tree-categories-create');
      var activeId = saveAndEdit && data.data && data.data.model ? data.data.model.id : null;
      reloadTree(activeId, function () {
        if (activeId) {
          var fetchDataButton = $(".dd-item[data-id=\"".concat(activeId, "\"] > .dd3-content .fetch-data"));
          if (fetchDataButton.length) {
            fetchDataButton.trigger('click');
          } else {
            location.reload();
          }
        } else if ($createButton.length) {
          $createButton.trigger('click');
        } else {
          var _data$data;
          reloadForm((_data$data = data.data) === null || _data$data === void 0 ? void 0 : _data$data.form);
        }
      });
    })["finally"](function () {
      return $form.find('button[type=submit]').prop('disabled', false).removeClass('disabled');
    });
  });
  $(document).on('show.bs.modal', '.modal-confirm-delete', function (event) {
    $(event.currentTarget).find('[data-bb-toggle="modal-confirm-delete"]').attr('data-url', $(event.relatedTarget).data('url'));
  }).on('click', '[data-bb-toggle="modal-confirm-delete"]', function (event) {
    event.preventDefault();
    var button = $(event.currentTarget);
    $httpClient.make().withButtonLoading(button)["delete"]($(button).get(0).dataset.url).then(function (_ref6) {
      var data = _ref6.data;
      Botble.showSuccess(data.message);
      reloadTree();
      var $createButton = $('.tree-categories-create');
      if ($createButton.length) {
        $createButton.trigger('click');
      } else {
        reloadForm('');
      }
    })["finally"](function () {
      return button.closest('.modal').modal('hide');
    });
  });
  function handleOpenCollapseState() {
    // Restore state from localStorage
    $treeWrapper.find('.dd-item').each(function () {
      var categoryId = $(this).data('id');
      var isCollapsed = localStorage.getItem('category_' + categoryId) === 'collapsed';
      if (isCollapsed) {
        $(this).addClass('dd-collapsed');
        $(this).find('.dd-list').hide();
      } else {
        $(this).removeClass('dd-collapsed');
        $(this).find('.dd-list').show();
      }
    });

    // Handle collapse button click
    $treeWrapper.on('click', '.dd-collapse', function () {
      var categoryItem = $(this).closest('.dd-item');
      var categoryId = categoryItem.data('id');
      categoryItem.addClass('dd-collapsed');
      categoryItem.find('.dd-list').slideUp();
      localStorage.setItem('category_' + categoryId, 'collapsed');
    });

    // Handle expand button click
    $treeWrapper.on('click', '.dd-expand', function () {
      var categoryItem = $(this).closest('.dd-item');
      var categoryId = categoryItem.data('id');
      categoryItem.removeClass('dd-collapsed');
      categoryItem.find('.dd-list').slideDown();
      localStorage.setItem('category_' + categoryId, 'expanded');
    });
  }
  $(document).ready(function () {
    handleOpenCollapseState();
  });
});
/******/ })()
;