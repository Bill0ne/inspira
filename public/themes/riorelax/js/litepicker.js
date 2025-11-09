(function () {
    'use strict';

    const DEFAULTS = {
        autoApply: true,
        format: 'DD.MM.YYYY',
        lang: 'de-DE',
        singleMode: true,
        showTime: false,
        showSeconds: false
    };

    const MONTH_NAMES = {
        'de-DE': ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember']
    };

    const WEEKDAY_NAMES = {
        'de-DE': ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So']
    };

    function pad(value) {
        return value.toString().padStart(2, '0');
    }

    function formatDate(date, format) {
        if (!date) {
            return '';
        }

        const year = date.getFullYear();
        const month = pad(date.getMonth() + 1);
        const day = pad(date.getDate());

        switch (format) {
            case 'DD.MM.YYYY':
                return `${day}.${month}.${year}`;
            case 'YYYY-MM-DD':
                return `${year}-${month}-${day}`;
            default:
                return `${day}.${month}.${year}`;
        }
    }

    function formatTime(date, format) {
        const hours = pad(date.getHours());
        const minutes = pad(date.getMinutes());

        switch (format) {
            case 'HH:mm':
                return `${hours}:${minutes}`;
            default:
                return `${hours}:${minutes}`;
        }
    }

    function parseExistingValue(value, format) {
        if (!value) {
            return null;
        }

        if (format === 'DD.MM.YYYY') {
            const [day, month, year] = value.split('.').map(Number);
            if (!day || !month || !year) {
                return null;
            }
            return new Date(year, month - 1, day);
        }

        if (format === 'HH:mm') {
            const [hour, minute] = value.split(':').map(Number);
            if (Number.isNaN(hour) || Number.isNaN(minute)) {
                return null;
            }
            const now = new Date();
            now.setHours(hour, minute, 0, 0);
            return now;
        }

        return null;
    }

    class Litepicker {
        constructor(options) {
            this.options = Object.assign({}, DEFAULTS, options || {});
            if (!this.options.element) {
                throw new Error('Litepicker requires an element option.');
            }

            this.element = this.options.element;
            this.element.setAttribute('autocomplete', 'off');
            this.element.readOnly = true;

            this.isTimePicker = this.options.showTime || /H/.test(this.options.format);
            this.currentDate = parseExistingValue(this.element.value, this.options.format) || new Date();
            this.selectedDate = parseExistingValue(this.element.value, this.options.format);
            this.isOpen = false;

            this.onDocumentClick = this.onDocumentClick.bind(this);
            this.onResize = this.onResize.bind(this);
            this.open = this.open.bind(this);
            this.close = this.close.bind(this);

            this.element.addEventListener('focus', this.open);
            this.element.addEventListener('click', this.open);
        }

        open() {
            if (this.isOpen) {
                return;
            }
            this.isOpen = true;

            this.backdrop = document.createElement('div');
            this.backdrop.className = 'litepicker__backdrop';
            document.body.appendChild(this.backdrop);

            this.container = document.createElement('div');
            this.container.className = 'litepicker' + (this.isTimePicker ? ' is-time-picker' : '');
            document.body.appendChild(this.container);

            if (this.isTimePicker) {
                this.renderTimePicker();
            } else {
                this.renderCalendar();
            }

            this.position();

            window.addEventListener('resize', this.onResize, { passive: true });
            document.addEventListener('click', this.onDocumentClick, true);
        }

        close() {
            if (!this.isOpen) {
                return;
            }

            this.isOpen = false;
            if (this.container && this.container.parentNode) {
                this.container.parentNode.removeChild(this.container);
            }
            if (this.backdrop && this.backdrop.parentNode) {
                this.backdrop.parentNode.removeChild(this.backdrop);
            }
            document.removeEventListener('click', this.onDocumentClick, true);
            window.removeEventListener('resize', this.onResize, { passive: true });
        }

        onDocumentClick(event) {
            if (!this.container) {
                return;
            }

            if (this.container.contains(event.target) || event.target === this.element) {
                return;
            }

            this.close();
        }

        onResize() {
            this.position();
        }

        position() {
            if (!this.container) {
                return;
            }
            const rect = this.element.getBoundingClientRect();
            const top = rect.bottom + window.scrollY + 8;
            const left = rect.left + window.scrollX;
            this.container.style.top = `${top}px`;
            this.container.style.left = `${left}px`;
        }

        renderCalendar() {
            this.container.innerHTML = '';

            const header = document.createElement('div');
            header.className = 'litepicker__header';

            const monthLabel = document.createElement('div');
            monthLabel.className = 'litepicker__month';
            monthLabel.textContent = `${MONTH_NAMES[this.options.lang]?.[this.currentDate.getMonth()] || MONTH_NAMES['de-DE'][this.currentDate.getMonth()]} ${this.currentDate.getFullYear()}`;
            header.appendChild(monthLabel);

            const nav = document.createElement('div');
            nav.className = 'litepicker__nav';

            const prevBtn = document.createElement('button');
            prevBtn.type = 'button';
            prevBtn.setAttribute('aria-label', 'Vorheriger Monat');
            prevBtn.innerHTML = '&#10094;';
            prevBtn.addEventListener('click', () => {
                this.changeMonth(-1);
            });
            nav.appendChild(prevBtn);

            const nextBtn = document.createElement('button');
            nextBtn.type = 'button';
            nextBtn.setAttribute('aria-label', 'Nächster Monat');
            nextBtn.innerHTML = '&#10095;';
            nextBtn.addEventListener('click', () => {
                this.changeMonth(1);
            });
            nav.appendChild(nextBtn);

            header.appendChild(nav);
            this.container.appendChild(header);

            const weekdays = document.createElement('div');
            weekdays.className = 'litepicker__weekdays';
            const names = WEEKDAY_NAMES[this.options.lang] || WEEKDAY_NAMES['de-DE'];
            names.forEach(name => {
                const cell = document.createElement('div');
                cell.className = 'litepicker__weekday';
                cell.textContent = name;
                weekdays.appendChild(cell);
            });
            this.container.appendChild(weekdays);

            const days = document.createElement('div');
            days.className = 'litepicker__days';

            const firstDayOfMonth = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 1);
            let startIndex = firstDayOfMonth.getDay();
            startIndex = startIndex === 0 ? 6 : startIndex - 1;

            const daysInMonth = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 0).getDate();
            const daysInPrevMonth = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 0).getDate();

            for (let i = startIndex - 1; i >= 0; i--) {
                const day = document.createElement('div');
                day.className = 'litepicker__day is-outside';
                day.textContent = daysInPrevMonth - i;
                days.appendChild(day);
            }

            for (let dayNumber = 1; dayNumber <= daysInMonth; dayNumber++) {
                const day = document.createElement('div');
                day.className = 'litepicker__day';
                day.textContent = dayNumber;

                const dateValue = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), dayNumber);
                if (this.selectedDate &&
                    this.selectedDate.getFullYear() === dateValue.getFullYear() &&
                    this.selectedDate.getMonth() === dateValue.getMonth() &&
                    this.selectedDate.getDate() === dateValue.getDate()) {
                    day.classList.add('is-selected');
                }

                day.addEventListener('click', () => {
                    this.selectDate(dateValue);
                });

                days.appendChild(day);
            }

            const totalCells = days.children.length;
            const nextDays = totalCells % 7 === 0 ? 0 : 7 - (totalCells % 7);
            for (let i = 1; i <= nextDays; i++) {
                const day = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, i);
                const cell = document.createElement('div');
                cell.className = 'litepicker__day is-outside';
                cell.textContent = day.getDate();
                days.appendChild(cell);
            }

            this.container.appendChild(days);

            const footer = document.createElement('div');
            footer.className = 'litepicker__footer';
            const closeBtn = document.createElement('button');
            closeBtn.type = 'button';
            closeBtn.className = 'litepicker__close';
            closeBtn.textContent = 'Schließen';
            closeBtn.addEventListener('click', this.close);
            footer.appendChild(closeBtn);
            this.container.appendChild(footer);
        }

        renderTimePicker() {
            this.container.innerHTML = '';
            this.container.classList.add('is-time-picker');

            const title = document.createElement('div');
            title.className = 'litepicker__header';
            const label = document.createElement('div');
            label.className = 'litepicker__month';
            label.textContent = 'Zeit auswählen';
            title.appendChild(label);
            this.container.appendChild(title);

            const wrap = document.createElement('div');
            wrap.className = 'litepicker__time';

            const hourColumn = document.createElement('div');
            hourColumn.className = 'litepicker__column';
            const hourLabel = document.createElement('span');
            hourLabel.className = 'litepicker__label';
            hourLabel.textContent = 'Stunden';
            const hourSelect = document.createElement('select');
            hourSelect.className = 'litepicker__select';
            hourSelect.setAttribute('data-type', 'hour');
            for (let hour = 0; hour < 24; hour++) {
                const option = document.createElement('option');
                option.value = pad(hour);
                option.textContent = pad(hour);
                hourSelect.appendChild(option);
            }
            hourColumn.appendChild(hourLabel);
            hourColumn.appendChild(hourSelect);

            const minuteColumn = document.createElement('div');
            minuteColumn.className = 'litepicker__column';
            const minuteLabel = document.createElement('span');
            minuteLabel.className = 'litepicker__label';
            minuteLabel.textContent = 'Minuten';
            const minuteSelect = document.createElement('select');
            minuteSelect.className = 'litepicker__select';
            minuteSelect.setAttribute('data-type', 'minute');

            const minuteStep = this.options.dropdowns && this.options.dropdowns.minutes === true ? 30 : 5;
            for (let minute = 0; minute < 60; minute += minuteStep) {
                const option = document.createElement('option');
                option.value = pad(minute);
                option.textContent = pad(minute);
                minuteSelect.appendChild(option);
            }

            minuteColumn.appendChild(minuteLabel);
            minuteColumn.appendChild(minuteSelect);

            wrap.appendChild(hourColumn);
            wrap.appendChild(minuteColumn);

            this.container.appendChild(wrap);

            const footer = document.createElement('div');
            footer.className = 'litepicker__footer';
            const closeBtn = document.createElement('button');
            closeBtn.type = 'button';
            closeBtn.className = 'litepicker__close';
            closeBtn.textContent = 'Übernehmen';
            closeBtn.addEventListener('click', () => {
                const now = new Date();
                now.setHours(parseInt(hourSelect.value, 10), parseInt(minuteSelect.value, 10), 0, 0);
                this.selectDate(now);
            });
            footer.appendChild(closeBtn);
            this.container.appendChild(footer);

            const existing = parseExistingValue(this.element.value, this.options.format);
            if (existing) {
                hourSelect.value = pad(existing.getHours());
                minuteSelect.value = pad(existing.getMinutes());
            }
        }

        changeMonth(step) {
            this.currentDate.setMonth(this.currentDate.getMonth() + step);
            this.renderCalendar();
        }

        selectDate(date) {
            this.selectedDate = new Date(date.getTime());
            if (this.isTimePicker) {
                this.element.value = formatTime(this.selectedDate, this.options.format);
            } else {
                this.element.value = formatDate(this.selectedDate, this.options.format);
            }

            if (this.options.autoApply !== false) {
                this.close();
            }

            const event = new Event('change', { bubbles: true });
            this.element.dispatchEvent(event);
        }
    }

    window.Litepicker = Litepicker;
})();
