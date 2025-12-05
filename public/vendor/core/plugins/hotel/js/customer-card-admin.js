'use strict';

$(function () {
    const t = (key, fallback = '') => window.trans?.customerCard?.[key] ?? fallback;

    const csrfToken = () => $('meta[name="csrf-token"]').attr('content') || window.csrf_token;

    const toggleButton = ($button, isLoading) => {
        $button.prop('disabled', isLoading);

        const $spinner = $button.find('.spinner-border');
        if (isLoading) {
            if (!$spinner.length) {
                $button.append('<span class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>');
            }
        } else {
            $spinner.remove();
        }
    };

    const showSuccess = (message) => {
        if (!message) return;

        if (window.Botble?.showSuccess) {
            window.Botble.showSuccess(message);
            return;
        }

        if (window.toastr?.success) {
            window.toastr.success(message);
            return;
        }

        alert(message);
    };

    const showError = (message) => {
        if (!message) return;

        if (window.Botble?.showError) {
            window.Botble.showError(message);
            return;
        }

        if (window.toastr?.error) {
            window.toastr.error(message);
            return;
        }

        alert(message);
    };

    const handleRequestError = (error) => {
        const message = error?.response?.data?.message || error?.message || t('messages.error_occurred', 'Es ist ein Fehler aufgetreten.');
        showError(message);
    };

    const createRequestClient = ($context = null) => {
        const headers = {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken(),
        };

        return axios.create({
            headers,
        });
    };

    const reloadTables = ($button) => {
        const $table = $button.closest('table');
        const tableId = $table?.attr('id');

        if (tableId && window.LaravelDataTables?.[tableId]?.ajax?.reload) {
            window.LaravelDataTables[tableId].ajax.reload(null, false);
            return;
        }

        if (window.LaravelDataTables) {
            const tables = Object.values(window.LaravelDataTables);
            if (tables?.length && tables[0]?.ajax?.reload) {
                tables[0].ajax.reload(null, false);
                return;
            }
        }

        window.location.reload();
    };

    /* ----------------------------------------------------------
     *  MANUAL USAGE ADJUSTMENT
     * ---------------------------------------------------------- */
    $(document).on('click', '[data-bb-customer-card="usage-adjust"]', function (event) {
        event.preventDefault();

        const $button = $(this);
        const url = $button.data('url');
        const direction = $button.data('direction');
        const amount = Number($button.data('amount') || 1) || 1;
        const confirmMessage = $button.data('confirm') || t('messages.manual_usage_increase');
        const confirmTitle = $button.data('confirmTitle') || t('messages.manual_usage_title');

        if (!url || !direction) {
            return;
        }

        const performRequest = () => {
            toggleButton($button, true);

            createRequestClient($button)
                .post(url, {
                    _token: csrfToken(),
                    direction,
                    amount,
                })
                .then(({ data }) => {
                    toggleButton($button, false);
                    showSuccess(data?.message || t('messages.manual_usage_success'));
                    reloadTables($button);
                })
                .catch((error) => {
                    toggleButton($button, false);
                    handleRequestError(error);
                });
        };

        if (window.Botble?.showConfirm) {
            window.Botble.showConfirm({
                title: confirmTitle,
                message: confirmMessage,
                yes_button_text: t('general.yes', 'Ja'),
                no_button_text: t('general.no', 'Nein'),
                callback: (isConfirmed) => {
                    if (isConfirmed) {
                        performRequest();
                    }
                },
            });

            return;
        }

        if (confirm(confirmMessage)) {
            performRequest();
        }
    });

    /* ----------------------------------------------------------
     *  USAGE MODAL
     * ---------------------------------------------------------- */
    const ensureUsageModal = () => {
        let $modal = $('#customer-card-usage-modal');
        if ($modal.length) return $modal;

        $modal = $(`
            <div class="modal fade" id="customer-card-usage-modal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">${t('table.usage_title', 'Kartenverwendung')}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center py-4">${t('messages.loading', 'Loading...')}</div>
                        </div>
                    </div>
                </div>
            </div>
        `);
        $('body').append($modal);
        return $modal;
    };

    $(document).on('click', '[data-bb-customer-card="usage"]', function () {
        const url = $(this).data('url');
        const title = $(this).data('title') || t('table.usage_title', 'Kartenverwendung');

        if (!url) return;

        const $modal = ensureUsageModal();
        $modal.find('.modal-title').text(title);
        $modal.find('.modal-body').html(`<div class="text-center py-4">${t('messages.loading', 'Loading...')}</div>`);

        const renderUsageHtml = (html) => {
            if (!html) return false;
            $modal.find('.modal-body').html(html);
            return true;
        };

        $modal.modal('show');

        createRequestClient($modal)
            .get(url)
            .then(({ data }) => {
                if (renderUsageHtml(data?.data?.html)) return;
                if (renderUsageHtml(data?.html)) return;
                if (typeof data === 'string' && renderUsageHtml(data)) return;

                $modal.modal('hide');
                showError(t('messages.checkout_unavailable', 'Die Checkout-Antwort ist unvollständig.'));
            })
            .catch((error) => {
                $modal.modal('hide');
                handleRequestError(error);
            });
    });
});
