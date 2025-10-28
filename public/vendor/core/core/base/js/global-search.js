/******/ (() => { // webpackBootstrap
/*!**********************************************************!*\
  !*** ./platform/core/base/resources/js/global-search.js ***!
  \**********************************************************/
$(function () {
  var $globalSearchModal = $('[data-bb-toggle="gs-modal"]');
  var $noResultTemplate = $('#gs-no-result-template');
  var $form = $('[data-bb-toggle="gs-form"]');
  var $input = $('[data-bb-toggle="gs-input"]');
  var previousValue = '';
  var activateResult = function activateResult(element) {
    if (!element) {
      return;
    }
    element.addClass('active');
    element.attr('aria-selected', 'true');
  };
  var deactivateResult = function deactivateResult(element) {
    if (!element) {
      return;
    }
    element.removeClass('active');
    element.attr('aria-selected', 'false');
  };
  var searchTimeout = null;
  var debounceSearch = function debounceSearch(e) {
    if (searchTimeout) {
      clearTimeout(searchTimeout);
    }
    searchTimeout = setTimeout(function () {
      return searchHandler(e);
    }, 200);
  };
  var getResults = function getResults() {
    return $('[data-bb-toggle="gs-result"]');
  };
  var findSelected = function findSelected() {
    var $result = getResults();
    var $selected = $result.find('a.active');
    if ($selected.length <= 0) {
      $selected = $result.find('a:first');
    }
    return $selected;
  };
  var autoSelectHandler = function autoSelectHandler() {
    var $links = getResults().find('a');
    if ($links.length <= 0) {
      return;
    }
    $links.attr('aria-selected', 'false');
    $links.removeClass('active');
    var $selected = findSelected();
    if ($selected.length <= 0) {
      return;
    }
    if (!$selected.hasClass('active')) {
      activateResult($selected);
    }
  };
  var navigateHandler = function navigateHandler(e) {
    if (!$globalSearchModal.hasClass('show')) {
      return;
    }
    if (e.code !== 'ArrowUp' && e.code !== 'ArrowDown') {
      return;
    }
    var isNext = e.code === 'ArrowDown';
    var $selected = findSelected();
    if ($selected.length <= 0) {
      return;
    }
    if (isNext) {
      var $next = $selected.next();
      deactivateResult($selected);
      if ($next.length <= 0) {
        var $first = getResults().find('a:first');
        activateResult($first);
        return;
      }
      activateResult($next);
    } else {
      var $previous = $selected.prev();
      deactivateResult($selected);
      if ($previous.length <= 0) {
        var $last = getResults().find('a:last');
        activateResult($last);
        return;
      }
      activateResult($previous);
    }
  };
  var selectHandler = function selectHandler(e) {
    if (!$globalSearchModal.hasClass('show')) {
      return;
    }
    if (e.code !== 'Enter') {
      return;
    }
    var $selected = findSelected();
    if ($selected.length <= 0) {
      return;
    }
    $selected[0].click();
  };
  var searchHandler = function searchHandler(e) {
    e.preventDefault();
    if (!$globalSearchModal.hasClass('show')) {
      return;
    }
    var keyword = $input.val();
    if (keyword === previousValue) {
      return;
    }
    previousValue = keyword;
    var $result = getResults();
    $httpClient.make().withLoading($result).get($form.attr('action'), {
      keyword: keyword
    }).then(function (_ref) {
      var response = _ref.data;
      $result.html(response.data);
      autoSelectHandler();
    });
  };
  $(document).on('keydown', function (e) {
    if ($globalSearchModal.hasClass('show')) {
      return;
    }
    if ((e.code === 'Slash' || e.code === 'KeyK') && (e.metaKey || e.ctrlKey) || e.code === 'Slash' && e.target.tagName === 'BODY') {
      $globalSearchModal.modal('show');
    }
  });
  $(document).on('keydown', navigateHandler);
  $(document).on('keydown', selectHandler);
  $form.on('submit', searchHandler);
  $globalSearchModal.on('show.bs.modal', function () {
    getResults().html($noResultTemplate.html());
  });
  $globalSearchModal.on('shown.bs.modal', function () {
    $input.trigger('focus');
    autoSelectHandler();
  });
  $globalSearchModal.on('hidden.bs.modal', function () {
    $input.val('');
  });
  $input.on('keydown', function (e) {
    if (e.key === 'ArrowUp' || e.key === 'ArrowDown' || e.key === 'Enter') {
      e.preventDefault();
      return;
    }
    debounceSearch(e);
  });
  $('[data-bb-toggle="gs-navbar-input"]').on('focus', function () {
    return $globalSearchModal.modal('show');
  });
});
/******/ })()
;