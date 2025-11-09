document.addEventListener('DOMContentLoaded', () => {
    const submitBtn = document.querySelector('.booking-widget__submit');
    if (!submitBtn) return;

    // === Info Box erzeugen ===
    const infoBox = document.createElement('div');
    infoBox.className = 'booking-widget__info';
    submitBtn.parentNode.insertBefore(infoBox, submitBtn);

    // === Helper ===
    const setInfo = (msg, type = 'error') => {
        infoBox.textContent = msg;
        infoBox.classList.remove('error', 'success');
        infoBox.classList.add(type);
    };

    const toMinutes = (t) => {
        if (!t || !t.includes(':')) return 0;
        const [h, m] = t.split(':').map(Number);
        return h * 60 + m;
    };

    // === Validierung ===
    const validateForm = () => {
        const slots = document.querySelectorAll('[data-slot-card]');
        const maxGuests = parseInt(document.querySelector('#booking-adults')?.max || 10, 10);
        const adults = parseInt(document.querySelector('#booking-adults')?.value || 1, 10);
        let valid = true;

        if (adults > maxGuests) {
            setInfo(`Maximale Teilnehmeranzahl (${maxGuests}) überschritten.`, 'error');
            submitBtn.disabled = true;
            return;
        }

        for (const slot of slots) {
            const date = slot.querySelector('[data-role="slot-date"]')?.value;
            const start = slot.querySelector('[data-role="slot-start"]')?.value;
            const end = slot.querySelector('[data-role="slot-end"]')?.value;

            if (!date || !start || !end) {
                setInfo('Bitte Datum und Uhrzeiten vollständig auswählen.', 'error');
                valid = false;
                break;
            }

            const startMin = toMinutes(start);
            const endMin = toMinutes(end);

            if (endMin <= startMin) {
                setInfo('Endzeit muss nach der Startzeit liegen.', 'error');
                valid = false;
                break;
            }

            if (endMin - startMin < 30) {
                setInfo('Jeder Slot muss mindestens 30 Minuten dauern.', 'error');
                valid = false;
                break;
            }

            if (start < '09:30' || end > '22:30') {
                setInfo('Buchungen sind nur zwischen 09:30 und 22:30 Uhr möglich.', 'error');
                valid = false;
                break;
            }
        }

        if (valid) {
            setInfo('Alle Angaben gültig. Du kannst jetzt buchen.', 'success');
            submitBtn.disabled = false;
        } else {
            submitBtn.disabled = true;
        }
    };

    // === Events beobachten ===
    document.addEventListener('change', (e) => {
        if (e.target.matches('[data-role="slot-date"], [data-role="slot-start"], [data-role="slot-end"], #booking-adults')) {
            validateForm();
        }
    });

    // === Initialzustand ===
    setInfo('Bitte fülle zuerst alle Felder korrekt aus.');
    submitBtn.disabled = true;
});
