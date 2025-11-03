jQuery(function ($) {
    console.log("Booking datetime JS loaded ✅");

    // 🔧 function to initialize a datetime picker pair inside a specific slot
    function initDateTimePickers($slot) {
        const $start = $slot.find('.theme-date-input-start');
        const $end = $slot.find('.theme-date-input-end');

        // destroy old instances (if any)
        try { $start.datetimepicker('destroy'); } catch (e) {}
        try { $end.datetimepicker('destroy'); } catch (e) {}

        const dateFormat = 'DD.MM.YYYY HH:mm';

        $start.datetimepicker({
            toolbarPlacement: 'bottom',
            showClose: true,
            sideBySide: true,
            minDate: moment(),
            format: dateFormat,
            locale: 'de',            // 🇩🇪 German locale (uses 24h by default)
            icons: {
                time: 'far fa-clock',
                close: 'fas fa-check'
            },
            stepping: 30,            // optional: set minute step
            useCurrent: false,
            keepOpen: false,
            debug: false,
            allowInputToggle: true,
        });

        $end.datetimepicker({
            showClose: true,
            sideBySide: true,
            collapse: true,
            toolbarPlacement: 'bottom',
            useCurrent: false,
            format: dateFormat,
            locale: 'de',            // force German locale
            icons: {
                time: 'far fa-clock',
                close: 'fas fa-check'
            },
            stepping: 30,
            allowInputToggle: true,
        });


        // logic for date linking
        $start.on("dp.change", function (e) {
            const endPicker = $end.data("DateTimePicker");
            if (endPicker) endPicker.minDate(e.date);
        });

        $end.on("dp.change", function (e) {
            const startPicker = $start.data("DateTimePicker");
            if (startPicker) startPicker.maxDate(e.date);
        });
    }

    // 🔹 initialize first slot
    $('#booking-slots .slot-item').each(function () {
        initDateTimePickers($(this));
    });

    // 🔹 add new slot
    const $bookingSlots = $('#booking-slots');
    const $addSlotBtn = $('#add-slot');
    let slotIndex = 1;

    $addSlotBtn.on('click', function () {
        const $firstSlot = $bookingSlots.find('.slot-item0').first();
        if (!$firstSlot.length) return;

        const $newSlot = $firstSlot.clone();
        $newSlot.addClass('slot-item new-slot').css('position', 'relative');

        // clear inputs + update names + ids
        $newSlot.find('input').each(function () {
            $(this).val('');
            const name = $(this).attr('name');
            if (name) $(this).attr('name', name.replace(/\[\d+\]/, `[${slotIndex}]`));

            const id = $(this).attr('id');
            if (id) $(this).attr('id', id.replace(/\d+$/, slotIndex));
        });

        // 🧹 remove any existing datepicker widget markup from clone
        $newSlot.find('.tempus-dominus-widget').remove();

        // add remove (×) button
        const $removeBtn = $('<span>&times;</span>')
            .addClass('remove-slot-btn')
            .attr('title', 'Remove slot')
            .css({
                cursor: 'pointer',
                position: 'absolute',
                top: '8px',
                right: '10px',
                width: '22px',
                height: '22px',
                'border': '1px solid #000',
                display: 'flex',
                'align-items': 'center',
                'justify-content': 'center',
                'background': '#fff',
                'border-radius': '3px',
                'font-weight': '600'
            })
            .on('click', function () {
                $newSlot.remove();
            });

        $newSlot.append($removeBtn);

        // ✅ append to DOM
        $bookingSlots.append($newSlot);

        // ✅ initialize datetimepickers for new slot
        initDateTimePickers($newSlot);

        slotIndex++;
    });
});
