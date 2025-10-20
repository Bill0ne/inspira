/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/*!*****************************************************************************!*\
  !*** ./platform/plugins/hotel/resources/assets/js/customer-autocomplete.js ***!
  \*****************************************************************************/


function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
var CustomerAutocomplete = /*#__PURE__*/function () {
  function CustomerAutocomplete() {
    _classCallCheck(this, CustomerAutocomplete);
    this.init();
    this.handleEvents();
  }
  return _createClass(CustomerAutocomplete, [{
    key: "init",
    value: function init() {
      this.customerSearchInput = $('#customer_search');
      this.customerSearchResults = $('#customer_search_results');
      this.selectedCustomerInfo = $('#selected_customer_info');
      this.customerIdInput = $('#customer_id');
      this.createNewCustomerBtn = $('#btn_create_new_customer');
      this.searchUrl = route('booking.search-customers');
      this.getCustomerUrl = route('booking.get-customer');
      this.customerFields = ['first_name', 'last_name', 'email', 'phone', 'country', 'state', 'city', 'address', 'zip'];
    }
  }, {
    key: "handleEvents",
    value: function handleEvents() {
      var _self = this;

      // Handle search input
      this.customerSearchInput.on('keyup', function () {
        var keyword = $(this).val();
        if (keyword.length < 1) {
          _self.customerSearchResults.hide();
          return;
        }
        _self.searchCustomers(keyword);
      });

      // Handle clicking outside the search results
      $(document).on('click', function (e) {
        if (!$(e.target).closest('#customer_search_results, #customer_search').length) {
          _self.customerSearchResults.hide();
        }
      });

      // Handle create new customer button
      this.createNewCustomerBtn.on('click', function () {
        // Show the modal
        $('#create-customer-modal').modal('show');
      });

      // Handle create customer form submission
      $('#create-customer-button').on('click', function () {
        _self.createCustomer();
      });

      // Handle modal hidden event
      $('#create-customer-modal').on('hidden.bs.modal', function () {
        // Clear form fields
        $('#create-customer-form')[0].reset();
        $('#modal-error-msg').addClass('d-none').html('');
      });
    }
  }, {
    key: "createCustomer",
    value: function createCustomer() {
      var _self = this;
      var formData = {
        first_name: $('#modal_first_name').val(),
        last_name: $('#modal_last_name').val(),
        email: $('#modal_email').val(),
        phone: $('#modal_phone').val(),
        address: $('#modal_address').val(),
        city: $('#modal_city').val(),
        state: $('#modal_state').val(),
        country: $('#modal_country').val(),
        zip: $('#modal_zip').val()
      };

      // Show loading state
      $('#create-customer-button').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Creating...');
      $('#modal-error-msg').addClass('d-none').html('');
      $.ajax({
        url: route('booking.create-customer'),
        type: 'POST',
        data: formData,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function success(res) {
          if (res.error) {
            $('#modal-error-msg').removeClass('d-none').html(res.message);
          } else {
            // Close the modal
            $('#create-customer-modal').modal('hide');

            // Update the customer info
            var customer = res.data.customer;
            _self.selectedCustomerInfo.html(res.data.html).show();
            _self.fillCustomerFields(customer);
            _self.customerIdInput.val(customer.id);
            _self.customerSearchInput.val("".concat(customer.first_name, " ").concat(customer.last_name));
            $('#booking_details_container').removeClass('d-none');

            // Show success message
            Botble.showSuccess(res.data.message);
          }
        },
        error: function error(_error) {
          var errorMessage = 'Error creating customer';
          if (_error.responseJSON && _error.responseJSON.errors) {
            errorMessage = '<ul>';
            for (var field in _error.responseJSON.errors) {
              errorMessage += "<li>".concat(_error.responseJSON.errors[field][0], "</li>");
            }
            errorMessage += '</ul>';
          } else if (_error.responseJSON && _error.responseJSON.message) {
            errorMessage = _error.responseJSON.message;
          }
          $('#modal-error-msg').removeClass('d-none').html(errorMessage);
        },
        complete: function complete() {
          $('#create-customer-button').prop('disabled', false).html(trans('core/base::forms.create'));
        }
      });
    }
  }, {
    key: "searchCustomers",
    value: function searchCustomers(keyword) {
      var _self = this;

      // Show loading indicator
      this.customerSearchResults.html('<div class="dropdown-item">Loading...</div>').show();
      $.ajax({
        url: this.searchUrl,
        type: 'GET',
        data: {
          q: keyword
        },
        success: function success(res) {
          _self.renderSearchResults(res.data);
        },
        error: function error(_error2) {
          console.error('Error searching customers:', _error2);
          _self.customerSearchResults.html('<div class="dropdown-item">Error loading results</div>');
        }
      });
    }
  }, {
    key: "renderSearchResults",
    value: function renderSearchResults(data) {
      var _self = this;

      // Use the HTML rendered by the server
      this.customerSearchResults.html(data.html).show();

      // Log for debugging
      console.log('Customer search results:', data);
      if (!data.html || data.html.trim() === '') {
        this.customerSearchResults.html('<div class="dropdown-item">No results found</div>');
      }

      // Handle customer selection
      $('.customer-item').on('click', function () {
        var customerId = $(this).data('id');
        console.log('Selected customer ID:', customerId);
        _self.selectCustomer(customerId);
        _self.customerSearchResults.hide();
      });
    }
  }, {
    key: "selectCustomer",
    value: function selectCustomer(customerId) {
      var _self = this;
      $.ajax({
        url: this.getCustomerUrl,
        type: 'GET',
        data: {
          id: customerId
        },
        success: function success(res) {
          if (res.error) {
            Botble.showError(res.message);
          } else {
            var customer = res.data.customer;
            // Use the HTML rendered by the server
            _self.selectedCustomerInfo.html(res.data.html).show();
            _self.fillCustomerFields(customer);
            _self.customerIdInput.val(customer.id);
            _self.customerSearchInput.val("".concat(customer.first_name, " ").concat(customer.last_name));
            $('#booking_details_container').removeClass('d-none');
          }
        },
        error: function error(_error3) {
          console.error('Error getting customer:', _error3);
        }
      });
    }
  }, {
    key: "fillCustomerFields",
    value: function fillCustomerFields(customer) {
      this.customerFields.forEach(function (field) {
        if (customer[field]) {
          $("#".concat(field)).val(customer[field]);
        }
      });
    }
  }, {
    key: "clearCustomerSelection",
    value: function clearCustomerSelection() {
      this.customerFields.forEach(function (field) {
        $("#".concat(field)).val('');
      });
    }
  }]);
}();
$(document).ready(function () {
  new CustomerAutocomplete();
});
/******/ })()
;