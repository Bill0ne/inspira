$(function () {
    function toggleField($checkbox) {
        const target = $($checkbox.data('target'));
        const field = $checkbox.attr('name');

        if (field === 'unlimited_seats') {
            if ($checkbox.is(':checked')) {
                target.hide().find('input').val('');
            } else {
                target.show();
            }
        }

        if (field === 'is_recurring') {
            if ($checkbox.is(':checked')) {
                target.show();
            } else {
                target.hide().find('input, select').val('');
            }
        }
    }

    $('[data-toggle="toggle-field"]').each(function () {
        const $checkbox = $(this);
        toggleField($checkbox);
        $checkbox.on('change', function () {
            toggleField($checkbox);
        });
    });

    $(document).on('click', '.view-participants-btn', function() {
        let sessionId = $(this).data('session-id');
        let modalContent = $('#participantsModalContent');

        modalContent.html('<i class="fa fa-spinner fa-spin"></i> Loading...');

        const url = `/admin/course-sessions/${sessionId}/bookings`;

        $.ajax({
            url: url,
            method: 'GET',
            success: function(html) {
                modalContent.html(html);
            },
            error: function() {
                modalContent.html('<p>Error loading participants.</p>');
            }
        });

        $('#participantsModal').modal('show');
    });

    const $courseSelect = $('#admin_course_id');
    const $sessionSelect = $('#admin_session_id');
    const selectedSessionId = $sessionSelect.data('selected');

    $courseSelect.on('change', function () {
        const courseId = $(this).val();

        if (!courseId) {
            $sessionSelect.html('<option value="">Select a course first</option>');
            return;
        }

        const url = `/admin/courses/${courseId}/sessions`;

        $.ajax({
            url: url,
            type: 'GET',
            beforeSend: function () {
                $sessionSelect.html('<option>Loading sessions...</option>');
            },
            success: function (response) {
                if (response.data && response.data.length) {
                    let options = '<option value="">Select Session</option>';
                    response.data.forEach(function (session) {
                        const selectedAttr = session.id == selectedSessionId ? 'selected' : '';
                        options += `<option value="${session.id}" ${selectedAttr}>${session.text}</option>`;
                    });
                    $sessionSelect.html(options);
                } else {
                    $sessionSelect.html('<option value="">No sessions found</option>');
                }
            },
            error: function () {
                $sessionSelect.html('<option value="">Error loading sessions</option>');
            }
        });
    });

    const initialCourseId = $courseSelect.val();
    if (initialCourseId) {
        $courseSelect.trigger('change');
    }

    const $priceHelper = $('.course-price-helper');

    if ($priceHelper.length) {
        const helperData = $priceHelper.data();
        const taxRates = helperData.taxRates || {};
        const defaultTax = Number(helperData.defaultTax ?? 0);
        const currency = helperData.currency || {};
        const selectedTaxId = helperData.selectedTax;
        const $priceField = $(helperData.priceField || '#price');
        const $taxField = $(helperData.taxField || '#tax_id');
        const $taxText = $priceHelper.find('[data-course-price-tax]');
        const $grossText = $priceHelper.find('[data-course-price-gross]');

        const formatCurrency = value => {
            const decimals = Number(currency.decimals ?? 2);
            const decimalSeparator = currency.decimal_separator || '.';
            const thousandSeparator = currency.thousand_separator || ',';
            const symbol = currency.symbol || '';
            const isPrefix = !!currency.is_prefix;
            const addSpace = !!currency.add_space;

            const normalized = Number(value || 0);
            const fixed = normalized.toFixed(decimals);

            let [integer, fraction] = fixed.split('.');

            integer = integer.replace(/\B(?=(\d{3})+(?!\d))/g, thousandSeparator);

            let formatted = fraction ? [integer, fraction].join(decimalSeparator) : integer;

            if (! symbol) {
                return formatted;
            }

            const spacing = addSpace ? ' ' : '';

            return isPrefix
                ? `${symbol}${spacing}${formatted}`
                : `${formatted}${spacing}${symbol}`;
        };

        const calculateGross = () => {
            const rawPrice = ($priceField.val() || '').toString().replace(',', '.');
            const price = parseFloat(rawPrice) || 0;
            const taxId = $taxField.val() || selectedTaxId;
            const hasTaxRate = Object.prototype.hasOwnProperty.call(taxRates, taxId);
            const taxValue = hasTaxRate ? Number(taxRates[taxId]) : defaultTax;
            const gross = Math.round(price * (1 + taxValue / 100) * 100) / 100;

            $taxText.text(`${taxValue.toFixed(2)}%`);
            $grossText.text(formatCurrency(gross));
        };

        $priceField.on('input change', calculateGross);
        $taxField.on('change input', calculateGross);
        calculateGross();
    }
});
