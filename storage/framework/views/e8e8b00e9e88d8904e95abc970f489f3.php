<?php
    Theme::asset()->container('footer')->add('popper', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js', ['jquery']);
    Theme::asset()->container('footer')->add('bootstrap-js', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.2/js/bootstrap.min.js', ['jquery', 'popper']);
    Theme::asset()->container('footer')->add('moment-js', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js');
    Theme::asset()->container('footer')->add('datetimepicker-js', 'https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js', ['bootstrap-js', 'moment-js']);
    Theme::asset()->container('footer')->add('datetimepicker-css', 'https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css');
?>

<script>
document.addEventListener("DOMContentLoaded", function () {
    if (!$.fn.datetimepicker) return;

    // 24h-Defaults (falls andere Skripte initialisieren)
    try {
        $.extend(true, $.fn.datetimepicker.Constructor.Default, {
            format: 'DD.MM.YYYY HH:mm',
            sideBySide: true,
            stepping: 15,
            useCurrent: true,
            allowInputToggle: true,
            widgetParent: 'body',
            locale: "<?php echo e(App::getLocale()); ?>",
            icons: {
                time: 'fal fa-clock',
                date: 'fal fa-calendar',
                up: 'fas fa-chevron-up',
                down: 'fas fa-chevron-down',
                previous: 'fas fa-chevron-left',
                next: 'fas fa-chevron-right',
                today: 'fas fa-calendar-check',
                clear: 'fas fa-trash',
                close: 'fas fa-times'
            }
        });
    } catch(e) {}

    // Alle Picker auf der Seite initialisieren (Start/End per ID)
    const $start = $('#StartDateTimePicker');
    const $end   = $('#EndDateTimePicker');

    // evtl. Altinstanzen zerstören (Reload / Ajax)
    try { if ($start.data('DateTimePicker')) $start.datetimepicker('destroy'); } catch(e){}
    try { if ($end.data('DateTimePicker'))   $end.datetimepicker('destroy');   } catch(e){}

    $start.datetimepicker({
        minDate: moment().startOf('minute')
    });

    $end.datetimepicker({
        useCurrent: false,
        minDate: moment().add(15, 'minutes').startOf('minute')
    });

    // UX: Klick auf Icon/Textfeld öffnet sicher
    function bindOpen($wrap){
        $wrap.on('click focusin', 'input.datetimepicker-input, input.date-picker', () => $wrap.datetimepicker('show'));
        $wrap.find('[data-toggle="datetimepicker"], .input-group-text')
             .on('click', () => $wrap.datetimepicker('show'));
    }
    bindOpen($start); bindOpen($end);

    // Logik: Ende ≥ Start +15 Min; Start ≤ Ende
    $start.on('change.datetimepicker', function (e) {
        if (!e.date) return;
        const minEnd = e.date.clone().add(15, 'minutes');
        $end.datetimepicker('minDate', minEnd);

        const endDate = $end.datetimepicker('date');
        if (!endDate || endDate.isBefore(minEnd)) {
            $end.datetimepicker('date', e.date.clone().add(1, 'hours'));
        }
    });

    $end.on('change.datetimepicker', function (e) {
        if (!e.date) return;
        $start.datetimepicker('maxDate', e.date.clone());
    });

    // Resync falls ein Fremdscript später AM/PM setzt
    setTimeout(function () {
        try { $start.datetimepicker('date', $start.datetimepicker('date')); } catch(e){}
        try { $end.datetimepicker('date',   $end.datetimepicker('date'));   } catch(e){}
    }, 200);
});
</script>

<style>
/* UI – neutral & modern */
.input-group.date { position: relative; }
.input-group.date .form-control {
    padding-right: 35px;
    height: 44px;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    box-shadow: none;
    transition: border-color .15s ease, box-shadow .15s ease;
}
.input-group.date .form-control:focus {
    border-color: #578E88;
    box-shadow: 0 0 0 3px rgba(87,142,136,.15);
}
.input-group.date .input-group-text {
    border: none;
    background: transparent;
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    z-index: 2;
    cursor: pointer;
    color: #666;
}
/* Picker sichtbar über Sidebar */
.tempus-dominus-widget { z-index: 10550 !important; border-radius: 10px; overflow: hidden; }
.tempus-dominus-widget .date-container-days .day.active,
.tempus-dominus-widget .time-container .time .active { background-color: #578E88 !important; }
</style>

<?php if(is_plugin_active('hotel')): ?>
    <?php
        $minimumNumberOfGuests = HotelHelper::getMinimumNumberOfGuests();
        $maximumNumberOfGuests = HotelHelper::getMaximumNumberOfGuests();
        $startDate = request()->query('start_date', Carbon\Carbon::now()->format(HotelHelper::getDateFormat()));
        $endDate = request()->query('end_date', Carbon\Carbon::now()->addDay()->format(HotelHelper::getDateFormat()));
        $adults = request()->query('adults', $minimumNumberOfGuests);
    ?>

    <form action="<?php echo e($availableForBooking ? route('public.booking') : route('public.rooms')); ?>" method="<?php echo e($availableForBooking ? 'POST' : 'GET'); ?>" class="contact-form mt-30 form-booking">
        <?php if($availableForBooking): ?>
            <?php echo csrf_field(); ?>
            <input type="hidden" name="room_id" value="<?php echo e($room->id); ?>">
        <?php endif; ?>

        <?php switch($style):
            case (2): ?>
                <div class="row align-items-center">
                    <?php if(! empty($title)): ?>
                        <div class="col-lg-12">
                            <div class="section-title center-align mb-30">
                                <h2><?php echo BaseHelper::clean($title); ?></h2>
                            </div>
                        </div>
                    <?php endif; ?>

                    
                    <div class="col-lg-2 col-md-6 mb-30">
                        <div class="contact-field p-relative c-name">
                            <label for="availability-form-start-date"><i class="fal fa-badge-check"></i><?php echo e(__('Check In Date')); ?></label>
                            <div class="input-group date" id="StartDateTimePicker" data-target-input="nearest">
                                <input
                                    id="availability-form-start-date"
                                    autocomplete="off"
                                    type="text"
                                    class="departure-date datetimepicker-input date-picker"
                                    data-target="#StartDateTimePicker"
                                    data-date-format="<?php echo e(HotelHelper::getBookingFormDateFormat()); ?>"
                                    placeholder="DD.MM.YYYY HH:mm"
                                    data-locale="<?php echo e(App::getLocale()); ?>"
                                    value="<?php echo e(BaseHelper::stringify($availableForBooking ? old('start_date', $startDate) : $startDate)); ?>"
                                    name="start_date"
                                >
                                <span class="input-group-text" data-target="#StartDateTimePicker" data-toggle="datetimepicker">
                                    <i class="fal fa-calendar-alt"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    
                    <div class="col-lg-2 col-md-6 mb-30">
                        <div class="contact-field p-relative c-name">
                            <label for="availability-form-end-date"><i class="fal fa-times-octagon"></i><?php echo e(__('Check Out Date')); ?></label>
                            <div class="input-group date" id="EndDateTimePicker" data-target-input="nearest">
                                <input
                                    type="text"
                                    id="availability-form-end-date"
                                    autocomplete="off"
                                    class="arrival-date datetimepicker-input date-picker"
                                    data-target="#EndDateTimePicker"
                                    data-date-format="<?php echo e(HotelHelper::getBookingFormDateFormat()); ?>"
                                    placeholder="DD.MM.YYYY HH:mm"
                                    data-locale="<?php echo e(App::getLocale()); ?>"
                                    value="<?php echo e(BaseHelper::clean($availableForBooking ? old('end_date', $endDate) : $endDate)); ?>"
                                    name="end_date"
                                >
                                <span class="input-group-text" data-target="#EndDateTimePicker" data-toggle="datetimepicker">
                                    <i class="fal fa-calendar-alt"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    
                    <div class="col-lg-5 col-md-6 mb-30">
                        <div class="contact-field p-relative c-name form-guests-and-rooms-wrapper">
                            <label for="adults"><i class="fal fa-users"></i><?php echo e(__('Guests and Rooms')); ?></label>
                            <button data-bb-toggle="toggle-guests-and-rooms" class="text-truncate" type="button" data-target="#toggle-guests-and-rooms">
                                <span data-bb-toggle="filter-adults-count" class="me-1">1</span> <?php echo e(__('Adult(s)')); ?> ,
                                <span data-bb-toggle="filter-children-count" class="ms-1 me-1">0</span> <?php echo e(__('Child(ren)')); ?>,
                                <span data-bb-toggle="filter-rooms-count" class="me-1 ms-1">1</span> <?php echo e(__('Room(s)')); ?>

                            </button>

                            <div class="custom-dropdown dropdown-menu p-3" id="toggle-guests-and-rooms">
                                <div class="inputs-filed">
                                    <label for="adults"><?php echo e(__('Adults')); ?></label>
                                    <div class="input-quantity">
                                        <button type="button" class="main-btn btn" data-bb-toggle="decrement-room">-</button>
                                        <input type="number" id="adults" name="adults" readonly value="1" min="<?php echo e(HotelHelper::getMinimumNumberOfGuests()); ?>" max="<?php echo e(HotelHelper::getMaximumNumberOfGuests()); ?>">
                                        <button type="button" class="main-btn btn" data-bb-toggle="increment-room">+</button>
                                    </div>
                                </div>
                                <div class="inputs-filed mt-30">
                                    <label for="children"><?php echo e(__('Children')); ?></label>
                                    <div class="input-quantity">
                                        <button type="button" class="main-btn btn" data-bb-toggle="decrement-room">-</button>
                                        <input type="number" id="children" name="children" readonly value="0" min="0" max="<?php echo e(HotelHelper::getMaximumNumberOfGuests()); ?>">
                                        <button type="button" class="main-btn btn" data-bb-toggle="increment-room">+</button>
                                    </div>
                                </div>
                                <div class="inputs-filed mt-30">
                                    <label for="rooms"><?php echo e(__('Rooms')); ?></label>
                                    <div class="input-quantity">
                                        <button type="button" class="main-btn btn" data-bb-toggle="decrement-room">-</button>
                                        <input type="number" id="rooms" name="rooms" readonly value="1" min="1" max="10">
                                        <button type="button" class="main-btn btn" data-bb-toggle="increment-room">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="col-lg-3 col-md-6">
                        <div class="slider-btn">
                            <button type="submit" class="btn ss-btn" data-animation="fadeInRight" data-delay=".8s">
                                <?php echo e($availableForBooking ? __('Book Now') : __('Check Availability')); ?>

                            </button>
                        </div>
                    </div>
                </div>
                <?php break; ?>

            <?php default: ?>
                <div class="row booking-area">
                    <?php if(! empty($title)): ?>
                        <div class="col-lg-12">
                            <div class="section-title center-align mb-30">
                                <h2><?php echo BaseHelper::clean($title); ?></h2>
                            </div>
                        </div>
                    <?php endif; ?>

                    
                    <div class="col-lg-12">
                        <div class="contact-field p-relative c-name mb-20">
                            <label for="room-detail-booking-form-start-date"><i class="fal fa-badge-check"></i><?php echo e(__('Check In Date')); ?></label>
                            <div class="input-group date" id="StartDateTimePicker" data-target-input="nearest">
                                <input
                                    type="text"
                                    id="room-detail-booking-form-start-date"
                                    class="departure-date datetimepicker-input date-picker"
                                    autocomplete="off"
                                    data-target="#StartDateTimePicker"
                                    data-date-format="<?php echo e(HotelHelper::getBookingFormDateFormat()); ?>"
                                    placeholder="DD.MM.YYYY HH:mm"
                                    data-locale="<?php echo e(App::getLocale()); ?>"
                                    value="<?php echo e(BaseHelper::stringify($startDate ?: old('start_date', $startDate))); ?>"
                                    name="start_date"
                                >
                                <span class="input-group-text" data-target="#StartDateTimePicker" data-toggle="datetimepicker">
                                    <i class="fal fa-calendar-alt"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    
                    <div class="col-lg-12">
                        <div class="contact-field p-relative c-subject mb-20">
                            <label for="room-detail-booking-form-end-date"><i class="fal fa-times-octagon"></i><?php echo e(__('Check Out Date')); ?></label>
                            <div class="input-group date" id="EndDateTimePicker" data-target-input="nearest">
                                <input
                                    type="text"
                                    id="room-detail-booking-form-end-date"
                                    class="arrival-date datetimepicker-input date-picker"
                                    autocomplete="off"
                                    data-date-format="<?php echo e(HotelHelper::getBookingFormDateFormat()); ?>"
                                    placeholder="DD.MM.YYYY HH:mm"
                                    data-locale="<?php echo e(App::getLocale()); ?>"
                                    value="<?php echo e(BaseHelper::stringify($endDate ?: old('end_date', $endDate))); ?>"
                                    name="end_date"
                                    data-target="#EndDateTimePicker"
                                >
                                <span class="input-group-text" data-target="#EndDateTimePicker" data-toggle="datetimepicker">
                                    <i class="fal fa-calendar-alt"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    
                    <div class="col-lg-12">
                        <div class="contact-field p-relative c-subject input-group input-group-two left-icon mb-20">
                            <label for="adults"><i class="fal fa-users"></i><?php echo e(__('Adults')); ?></label>
                            <div class="input-quantity">
                                <button type="button" class="main-btn btn" data-bb-toggle="decrement-room">-</button>
                                <input type="number" id="adults" name="adults" readonly value="<?php echo e(BaseHelper::stringify(request()->integer('adults', 1))); ?>" min="<?php echo e(HotelHelper::getMinimumNumberOfGuests()); ?>" max="<?php echo e(HotelHelper::getMaximumNumberOfGuests()); ?>">
                                <button type="button" class="main-btn btn" data-bb-toggle="increment-room">+</button>
                            </div>
                        </div>
                    </div>

                    
                    <div class="col-lg-12">
                        <div class="slider-btn mt-15">
                            <button type="submit" class="btn ss-btn" data-animation="fadeInRight" data-delay=".8s">
                                <span><?php echo e($availableForBooking ? __('Book Now') : __('Check Availability')); ?></span>
                            </button>
                        </div>
                    </div>
                </div>
        <?php endswitch; ?>
    </form>
<?php endif; ?>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/partials/hotel/forms/form.blade.php ENDPATH**/ ?>