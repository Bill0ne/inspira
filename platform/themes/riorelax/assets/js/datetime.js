jQuery(function ($) {
    const dateFormat = 'DD.MM.YYYY';

    function initDateTimePicker($wrapper) {
        const $picker = $wrapper.find('.time-range-picker');
        const $input = $wrapper.find('input[name="slots[]"]');

        if (!$picker.length || !$input.length) return;

        if ($picker.data('DateTimePicker')) {
            $picker.data('DateTimePicker').destroy();
        }

        $picker.datetimepicker({
            format: dateFormat,
            locale: 'de',
            minDate: moment(),
            icons: { time: 'far fa-clock', close: 'fas fa-check' },
            sideBySide: false,
            ignoreReadonly: true,
            useCurrent: false,
            keepOpen: true,
        });

        const picker = $picker.data('DateTimePicker');
        let selectedDate = null;

        $picker.add($input).off('click').on('click', function (e) {
            e.stopPropagation();
            if (picker) picker.show();
        });

        $(document).on('mousedown click', '.bootstrap-datetimepicker-widget', function (e) {
            e.stopPropagation();
        });

        $(document).off('click', '.bootstrap-datetimepicker-widget td.day')
            .on('click', '.bootstrap-datetimepicker-widget td.day', function (e) {
                e.preventDefault();
                e.stopPropagation();

                const $this = $(this);
                const day = parseInt($this.text(), 10);
                const currentMonth = picker.viewDate().month();
                const currentYear = picker.viewDate().year();

                selectedDate = moment([currentYear, currentMonth, day]);
                $picker.data('selectedDate', selectedDate);

                $('.bootstrap-datetimepicker-widget td.day').removeClass('active');
                $this.addClass('active');

                const existingVal = $input.val();
                if (!existingVal.includes('-')) {
                    $input.val(`${selectedDate.format(dateFormat)} 10:00 - 11:00`);
                }
            });

        $picker.off('dp.show').on('dp.show', function () {
            setTimeout(() => {
                const widget = $('.bootstrap-datetimepicker-widget:visible').last();
                if (!widget || !widget.length) return;

                widget.css('z-index', 99999);
                widget.find('.time-range-inline').remove();

                const timeHtml = `
<div class="time-range-inline"
    style="display:flex; flex-direction:column; align-items:flex-start; padding:10px 0;
           background:#fff; border-top:1px solid #e6e6e6; margin-top:6px; margin-left:50%;
           transform:translateX(-25%);">
  <div style="display:flex; justify-content:center; align-items:center; gap:10px;
              margin-top:4px; margin-left:35px;">
    <!-- START SLOT -->
    <div class="time-slot" data-slot="start" style="display:flex; flex-direction:column; align-items:center;">
      <div class="time-counter" style="display:flex; justify-content:center; align-items:center; gap:10px;">
        <div class="counter hour-counter" style="display:flex; flex-direction:column; align-items:center;">
          <i class="fas fa-chevron-up hour-up"
             style="cursor:pointer; background:#578e88; color:#fff; border-radius:4px;
                    width:26px; height:22px; display:flex; align-items:center;
                    justify-content:center; margin-bottom:3px;"></i>
          <div class="hour-value" style="font-size:20px; font-weight:700;">10</div>
          <i class="fas fa-chevron-down hour-down"
             style="cursor:pointer; background:#578e88; color:#fff; border-radius:4px;
                    width:26px; height:22px; display:flex; align-items:center;
                    justify-content:center; margin-top:3px;"></i>
        </div>
        <div style="font-size:22px; font-weight:700;">:</div>
        <div class="counter minute-counter" style="display:flex; flex-direction:column; align-items:center;">
          <i class="fas fa-chevron-up minute-up"
             style="cursor:pointer; background:#578e88; color:#fff; border-radius:4px;
                    width:26px; height:22px; display:flex; align-items:center;
                    justify-content:center; margin-bottom:3px;"></i>
          <div class="minute-value" style="font-size:20px; font-weight:700;">00</div>
          <i class="fas fa-chevron-down minute-down"
             style="cursor:pointer; background:#578e88; color:#fff; border-radius:4px;
                    width:26px; height:22px; display:flex; align-items:center;
                    justify-content:center; margin-top:3px;"></i>
        </div>
      </div>
    </div>

    <div style="font-size:18px; font-weight:600; color:#444; margin:0 4px;">–</div>

    <!-- END SLOT -->
    <div class="time-slot" data-slot="end" style="display:flex; flex-direction:column; align-items:center;">
      <div class="time-counter" style="display:flex; justify-content:center; align-items:center; gap:10px;">
        <div class="counter hour-counter" style="display:flex; flex-direction:column; align-items:center;">
          <i class="fas fa-chevron-up hour-up"
             style="cursor:pointer; background:#578e88; color:#fff; border-radius:4px;
                    width:26px; height:22px; display:flex; align-items:center;
                    justify-content:center; margin-bottom:3px;"></i>
          <div class="hour-value" style="font-size:20px; font-weight:700;">11</div>
          <i class="fas fa-chevron-down hour-down"
             style="cursor:pointer; background:#578e88; color:#fff; border-radius:4px;
                    width:26px; height:22px; display:flex; align-items:center;
                    justify-content:center; margin-top:3px;"></i>
        </div>
        <div style="font-size:22px; font-weight:700;">:</div>
        <div class="counter minute-counter" style="display:flex; flex-direction:column; align-items:center;">
          <i class="fas fa-chevron-up minute-up"
             style="cursor:pointer; background:#578e88; color:#fff; border-radius:4px;
                    width:26px; height:22px; display:flex; align-items:center;
                    justify-content:center; margin-bottom:3px;"></i>
          <div class="minute-value" style="font-size:20px; font-weight:700;">00</div>
          <i class="fas fa-chevron-down minute-down"
             style="cursor:pointer; background:#578e88; color:#fff; border-radius:4px;
                    width:26px; height:22px; display:flex; align-items:center;
                    justify-content:center; margin-top:3px;"></i>
        </div>
      </div>
    </div>
  </div>

  <div style="margin-top:10px; text-align:center; width:100%; margin-left:15px;">
    <button type="button" class="btn btn-sm btn-primary apply-time-range"
            style="width:100%; min-width:240px; padding:10px 0; font-weight:600;">
      Anwenden
    </button>
  </div>
</div>
`;


                widget.find('.datepicker-days').after(timeHtml);

                const getTime = (slot) => {
                    const hour = parseInt(widget.find(`.time-slot[data-slot="${slot}"] .hour-value`).text(), 10);
                    const minute = parseInt(widget.find(`.time-slot[data-slot="${slot}"] .minute-value`).text(), 10);
                    return { hour, minute };
                };

                const setTime = (slot, hour, minute) => {
                    widget.find(`.time-slot[data-slot="${slot}"] .hour-value`).text(hour.toString().padStart(2, '0'));
                    widget.find(`.time-slot[data-slot="${slot}"] .minute-value`).text(minute.toString().padStart(2, '0'));
                };

                const updateCounter = (slot, type, direction) => {
                    let { hour, minute } = getTime(slot);
                    if (type === 'hour') hour = direction === 'up' ? (hour + 1) % 24 : (hour - 1 + 24) % 24;
                    else {
                        minute += direction === 'up' ? 30 : -30;
                        if (minute >= 60) { minute = 0; hour = (hour + 1) % 24; }
                        if (minute < 0) { minute = 30; hour = (hour - 1 + 24) % 24; }
                    }
                    setTime(slot, hour, minute);
                };

                widget.off('click', '.hour-up, .hour-down, .minute-up, .minute-down')
                    .on('click', '.hour-up, .hour-down, .minute-up, .minute-down', function () {
                        const $btn = $(this);
                        const slot = $btn.closest('.time-slot').data('slot');
                        const type = $btn.hasClass('hour-up') || $btn.hasClass('hour-down') ? 'hour' : 'minute';
                        const direction = $btn.hasClass('hour-up') || $btn.hasClass('minute-up') ? 'up' : 'down';
                        updateCounter(slot, type, direction);
                    });

                widget.off('click', '.apply-time-range')
                    .on('click', '.apply-time-range', function (e) {
                        e.stopPropagation();
                        selectedDate = $picker.data('selectedDate') || picker.date() || moment();
                        const start = getTime('start');
                        const end = getTime('end');
                        const formatted = `${selectedDate.format(dateFormat)} ${start.hour.toString().padStart(2,'0')}:${start.minute.toString().padStart(2,'0')} - ${end.hour.toString().padStart(2,'0')}:${end.minute.toString().padStart(2,'0')}`;
                        $input.val(formatted);
                        picker.hide();
                    });
            }, 50);
        });
    }

    // initialize first slot
    initDateTimePicker($('.slot-item0'));

    // add new slot
    $('#add-slot').on('click', function () {
        const index = $('.slot-item').length;
        const slotId = `booking-slot-${index}`;

        const newSlot = $(`
      <div class="slot-item slot-item${index}" style="position:relative; margin-top:15px;">
        <button type="button" class="btn-remove-slot" style="position:absolute; top:5px; right:5px; border-radius:50%; width:30px; height:30px; display:flex; align-items:center; justify-content:center; background:#578e88; border:none; color:#fff;">
          <i class="fas fa-times"></i>
        </button>
        <div class="contact-field">
          <label><i class="fal fa-badge-check"></i> Date & Time</label>
          <div class="input-group date time-range-picker" id="${slotId}" data-target-input="nearest">
            <input type="text" name="slots[]" class="datetime-range-input input-group-append"
              data-target="#${slotId}" data-toggle="datetimepicker"
              placeholder="DD.MM.YYYY HH:mm - HH:mm" autocomplete="off" />
            <div class="input-group-append" data-target="#${slotId}" data-toggle="datetimepicker">
              <div style="display: none" class="input-group-text"><i class="far fa-calendar-alt"></i></div>
            </div>
          </div>
        </div>
      </div>
    `);

        $('#booking-slots').append(newSlot);
        initDateTimePicker(newSlot);
    });


    // remove slot
    $(document).on('click', '.btn-remove-slot', function () {
        if ($('.slot-item').length > 1) $(this).closest('.slot-item').remove();
    });
});
