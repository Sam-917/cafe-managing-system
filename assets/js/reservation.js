// assets/js/reservation.js

document.addEventListener('DOMContentLoaded', function() {
    initReservationSystem();
});

function initReservationSystem() {
    let currentStep = 1;
    let currentMonth = new Date();
    let selectedLocation = null;
    let selectedDate = null;
    let selectedTime = null;
    let selectedTable = null;
    let locationData = {};
    let tableData = {};

    // DOM Elements
    const stepIndicators = {
        1: document.getElementById('step1'),
        2: document.getElementById('step2'),
        3: document.getElementById('step3'),
        4: document.getElementById('step4'),
        5: document.getElementById('step5')
    };
    
    const stepContents = {
        1: document.getElementById('locationStep'),
        2: document.getElementById('dateStep'),
        3: document.getElementById('timeStep'),
        4: document.getElementById('tableStep'),
        5: document.getElementById('detailsStep')
    };
    
    const navButtons = {
        back: document.getElementById('backBtn'),
        next: document.getElementById('nextBtn'),
        confirm: document.getElementById('confirmBtn'),
        home: document.getElementById('homeBtn'),
        
    };

    // Initialize
    checkUrlParameters();
    updateQuickDates();
    renderCalendar();
    setupEventListeners();

    function checkUrlParameters() {
        const urlParams = new URLSearchParams(window.location.search);
        
        if (urlParams.has('location_id')) {
            selectedLocation = urlParams.get('location_id');
            currentStep = 2;
            updateSelectedLocationDisplay();
        }
        
        if (urlParams.has('date')) {
            selectedDate = urlParams.get('date');
            currentStep = 3;
            updateSelectedDateDisplay();
        }
        
        if (urlParams.has('time')) {
            selectedTime = urlParams.get('time');
            currentStep = 4;
            updateSelectedTimeDisplay();
        }
        
        if (urlParams.has('table_id')) {
            selectedTable = urlParams.get('table_id');
            currentStep = 5;
            updateSelectedTableDisplay();
        }
        
        updateStepDisplay();
    }

    function updateQuickDates() {
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        
        const weekend = new Date(today);
        const daysUntilSaturday = (6 - today.getDay()) % 7;
        weekend.setDate(today.getDate() + (daysUntilSaturday || 7));
        
        const nextWeek = new Date(today);
        nextWeek.setDate(today.getDate() + 7);
        
        document.getElementById('todayDate').textContent = formatDate(today, 'short');
        document.getElementById('tomorrowDate').textContent = formatDate(tomorrow, 'short');
        document.getElementById('weekendDate').textContent = formatDate(weekend, 'short');
        document.getElementById('nextWeekDate').textContent = formatDate(nextWeek, 'short');
    }

    function renderCalendar() {
        const year = currentMonth.getFullYear();
        const month = currentMonth.getMonth();
        
        document.getElementById('currentMonth').textContent = 
            new Date(year, month).toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
        
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const today = new Date();
        
        let calendarHTML = '';
        let dayCount = 1;
        
        for (let week = 0; week < 6; week++) {
            calendarHTML += '<div class="row text-center">';
            
            for (let day = 0; day < 7; day++) {
                if (week === 0 && day < firstDay) {
                    calendarHTML += '<div class="col"></div>';
                } else if (dayCount > daysInMonth) {
                    calendarHTML += '<div class="col"></div>';
                } else {
                    const currentDate = new Date(year, month, dayCount);
                    const isToday = currentDate.toDateString() === today.toDateString();
                    const isSelected = selectedDate && currentDate.toISOString().split('T')[0] === selectedDate;
                    const isPast = currentDate < today.setHours(0,0,0,0);
                    
                    let classes = 'calendar-day';
                    if (isToday) classes += ' today';
                    if (isSelected) classes += ' selected';
                    if (isPast) classes += ' disabled';
                    
                    calendarHTML += `
                        <div class="col">
                            <div class="${classes}" onclick="${isPast ? '' : `selectCalendarDate('${currentDate.toISOString().split('T')[0]}')`}">
                                ${dayCount}
                            </div>
                        </div>
                    `;
                    dayCount++;
                }
            }
            calendarHTML += '</div>';
            
            if (dayCount > daysInMonth) break;
        }
        
        document.getElementById('calendarGrid').innerHTML = calendarHTML;
    }

    function selectLocation(locationId) {
        selectedLocation = locationId;
        updateUrlParameters({ location_id: locationId });
        updateSelectedLocationDisplay();
    }

    function updateSelectedLocationDisplay() {
        if (selectedLocation && locationData[selectedLocation]) {
            document.getElementById('selectedLocationDisplay').textContent = locationData[selectedLocation].name;
            document.getElementById('selectedLocationDisplay2').textContent = locationData[selectedLocation].name;
            document.getElementById('selectedLocationDisplay3').textContent = locationData[selectedLocation].name;
            document.getElementById('summaryLocation').textContent = locationData[selectedLocation].name;
            document.getElementById('formLocationId').value = selectedLocation;
        }
    }

    function selectCalendarDate(date) {
        selectedDate = date;
        updateUrlParameters({ date });
        updateSelectedDateDisplay();
        loadAvailableTimes();
    }

    function updateSelectedDateDisplay() {
        if (selectedDate) {
            const dateObj = new Date(selectedDate);
            document.getElementById('selectedDateDisplay').textContent = formatDate(dateObj);
            document.getElementById('selectedDateDisplay2').textContent = formatDate(dateObj);
            document.getElementById('summaryDate').textContent = formatDate(dateObj);
            document.getElementById('formDate').value = selectedDate;
        }
    }

    function loadAvailableTimes() {
        const mockTimes = [
            '11:00', '11:30', '12:00', '12:30', '13:00', '13:30',
            '17:00', '17:30', '18:00', '18:30', '19:00', '19:30', '20:00'
        ];
        
        const timeSlotsContainer = document.getElementById('timeSlots');
        timeSlotsContainer.innerHTML = '';
        
        mockTimes.forEach(time => {
            const formattedTime = formatTime(time);
            timeSlotsContainer.innerHTML += `
                <div class="col-6 col-md-4 col-lg-3">
                    <button class="btn w-100 py-2 time-slot" onclick="selectTime('${time}')">
                        ${formattedTime}
                    </button>
                </div>
            `;
        });
    }

    function selectTime(time) {
        selectedTime = time;
        updateUrlParameters({ time });
        updateSelectedTimeDisplay();
        loadTableAvailability();
    }

    function updateSelectedTimeDisplay() {
        if (selectedTime) {
            document.getElementById('selectedTimeDisplay').textContent = formatTime(selectedTime);
            document.getElementById('summaryTime').textContent = formatTime(selectedTime);
            document.getElementById('formTime').value = selectedTime;
        }
    }

    function loadTableAvailability() {
        const mockTables = {
            'W1': { name: 'Window Table W1', capacity: 2, description: 'Intimate table with beautiful street view', location: 'Window', available: true },
            'W2': { name: 'Window Table W2', capacity: 2, description: 'Cozy window seat with natural lighting', location: 'Window', available: true },
            'W3': { name: 'Window Table W3', capacity: 4, description: 'Spacious window table', location: 'Window', available: false },
            'C1': { name: 'Center Table C1', capacity: 4, description: 'Central location with easy access', location: 'Center', available: true },
            'C2': { name: 'Center Table C2', capacity: 6, description: 'Large round table', location: 'Center', available: true },
            'B1': { name: 'Booth B1', capacity: 4, description: 'Private booth seating', location: 'Booth Area', available: true },
            'P1': { name: 'Private Table P1', capacity: 8, description: 'Exclusive table for large groups', location: 'Private Room', available: true }
        };
        
        tableData = mockTables;
        
        document.querySelectorAll('.restaurant-table').forEach(table => {
            const tableId = table.dataset.table;
            if (tableData[tableId]) {
                if (!tableData[tableId].available) {
                    table.classList.add('occupied');
                    table.onclick = null;
                } else {
                    table.classList.remove('occupied');
                    table.onclick = function() { selectTable(tableId); };
                }
            }
        });
    }

    function selectTable(tableId) {
        if (tableData[tableId] && tableData[tableId].available) {
            document.querySelectorAll('.restaurant-table').forEach(table => {
                table.classList.remove('selected');
            });
            
            const tableElement = document.querySelector(`[data-table="${tableId}"]`);
            if (tableElement) {
                tableElement.classList.add('selected');
                selectedTable = tableId;
                updateUrlParameters({ table_id: tableId });
                updateSelectedTableDisplay();
                showTableInfo(tableId);
            }
        }
    }

    function updateSelectedTableDisplay() {
        if (selectedTable && tableData[selectedTable]) {
            document.getElementById('summaryTable').textContent = tableData[selectedTable].name;
            document.getElementById('formTableId').value = selectedTable;
            
            const partySize = document.getElementById('partySize').value;
            document.getElementById('summaryPartySize').textContent = 
                `${partySize} ${partySize === '1' ? 'Person' : 'People'}`;
            document.getElementById('formPartySize').value = partySize;
        }
    }

    function showTableInfo(tableId) {
        const table = tableData[tableId];
        const infoPanel = document.getElementById('tableInfo');
        
        if (table && infoPanel) {
            document.getElementById('tableTitle').textContent = table.name;
            document.getElementById('tableDescription').textContent = table.description || 'No description available';
            document.getElementById('tableCapacity').textContent = `${table.capacity} people`;
            document.getElementById('tableLocation').textContent = table.location;
            
            infoPanel.style.display = 'block';
        }
    }

    function nextStep() {
        if (!validateCurrentStep()) return;
        currentStep++;
        updateStepDisplay();
    }

    function previousStep() {
        currentStep--;
        updateStepDisplay();
    }

    function validateCurrentStep() {
        switch(currentStep) {
            case 1: return !!selectedLocation || alert('Please select a location first.');
            case 2: return !!selectedDate || alert('Please select a date first.');
            case 3: return !!selectedTime || alert('Please select a time slot.');
            case 4: return !!selectedTable || alert('Please select a table.');
            default: return true;
        }
    }

    function updateStepDisplay() {
        Object.values(stepContents).forEach(step => step?.classList.add('d-none'));
        stepContents[currentStep]?.classList.remove('d-none');
        
        Object.entries(stepIndicators).forEach(([step, indicator]) => {
            if (!indicator) return;
            const stepNum = parseInt(step);
            if (stepNum < currentStep) {
                indicator.className = 'step-indicator completed';
                indicator.innerHTML = '<i class="bi bi-check"></i>';
            } else if (stepNum === currentStep) {
                indicator.className = 'step-indicator active';
                indicator.textContent = step;
            } else {
                indicator.className = 'step-indicator inactive';
                indicator.textContent = step;
            }
        });
        
        // Navigation buttons logic
        navButtons.next.style.display = currentStep < 5 ? 'block' : 'none';
        navButtons.confirm.style.display = currentStep === 5 ? 'block' : 'none';
        navButtons.home.style.display = currentStep === 1 ? 'block' : 'none';
        
       
    }

    async function confirmReservation() {
        if (!validateReservationForm()) return;
        
        try {
            const form = document.getElementById('reservationForm');
            const formData = new FormData(form);
            
            const response = await fetch('php/process_reservation.php', {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: formData
            });
            
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            
            const result = await response.json();
            
            if (result.success) {
                showConfirmation(result.confirmationNumber);
            } else {
                showError(result.errors || ['Unknown error occurred']);
            }
        } catch (error) {
            console.error('Error:', error);
            showError([`An error occurred: ${error.message}`]);
        }
    }

    function validateReservationForm() {
        const requiredFields = ['firstName', 'lastName', 'email', 'phone'];
        let isValid = true;
        const errors = [];
        
        requiredFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field && !field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
                errors.push(`${fieldId.replace(/([A-Z])/g, ' $1').trim()} is required`);
            } else if (field) {
                field.classList.remove('is-invalid');
            }
        });
        
        const emailField = document.getElementById('email');
        if (emailField && !validateEmail(emailField.value)) {
            emailField.classList.add('is-invalid');
            errors.push('Please enter a valid email address');
            isValid = false;
        }
        
        const phoneField = document.getElementById('phone');
        if (phoneField && !validatePhone(phoneField.value)) {
            phoneField.classList.add('is-invalid');
            errors.push('Please enter a valid phone number (10-15 digits)');
            isValid = false;
        }
        
        if (!isValid) showError(errors);
        return isValid;
    }

    function validateEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function validatePhone(phone) {
        return /^[\d\s\-\(\)]{8,15}$/.test(phone);
    }

    function showConfirmation(confirmationNumber) {
        const confirmationElement = document.getElementById('confirmationNumber');
        if (confirmationElement) confirmationElement.textContent = confirmationNumber;
        
        new bootstrap.Modal(document.getElementById('successModal')).show();
        document.getElementById('reservationForm').reset();
    }

    function showError(errors) {
        alert('Please fix the following errors:\n\n' + 
            (Array.isArray(errors) ? errors.join('\n') : errors));
    }

    function updateUrlParameters(params) {
        const url = new URL(window.location);
        Object.entries(params).forEach(([key, value]) => {
            value ? url.searchParams.set(key, value) : url.searchParams.delete(key);
        });
        
        const partySize = document.getElementById('partySize').value;
        if (partySize && partySize !== '2') {
            url.searchParams.set('party_size', partySize);
        }
        
        window.history.pushState({}, '', url);
    }

    function formatDate(date, format = 'long') {
        if (typeof date === 'string') date = new Date(date);
        
        return format === 'short' 
            ? date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
            : date.toLocaleDateString('en-US', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
    }

    function formatTime(time) {
        const [hours, minutes] = time.split(':');
        const hourNum = parseInt(hours, 10);
        const period = hourNum >= 12 ? 'PM' : 'AM';
        const displayHour = hourNum % 12 || 12;
        return `${displayHour}:${minutes} ${period}`;
    }

    function setupEventListeners() {
        document.getElementById('partySize')?.addEventListener('change', function() {
            updateUrlParameters({ party_size: this.value });
        });
        
        document.querySelectorAll('.quick-date').forEach(button => {
            button.addEventListener('click', function() {
                const type = this.getAttribute('onclick').match(/'(.*?)'/)[1];
                selectQuickDate(type);
            });
        });
        
        document.querySelector('[onclick="changeMonth(-1)"]').addEventListener('click', () => changeMonth(-1));
        document.querySelector('[onclick="changeMonth(1)"]').addEventListener('click', () => changeMonth(1));
        
        if (navButtons.next) navButtons.next.addEventListener('click', nextStep);
        if (navButtons.back) navButtons.back.addEventListener('click', previousStep);
        if (navButtons.confirm) navButtons.confirm.addEventListener('click', confirmReservation);
    }

    function selectQuickDate(type) {
        const today = new Date();
        let targetDate = new Date(today);
        
        switch(type) {
            case 'today': break;
            case 'tomorrow': targetDate.setDate(today.getDate() + 1); break;
            case 'weekend': 
                const daysUntilSaturday = (6 - today.getDay()) % 7;
                targetDate.setDate(today.getDate() + (daysUntilSaturday || 7));
                break;
            case 'nextweek': targetDate.setDate(today.getDate() + 7); break;
        }
        
        selectCalendarDate(targetDate.toISOString().split('T')[0]);
    }

    function changeMonth(delta) {
        currentMonth.setMonth(currentMonth.getMonth() + delta);
        renderCalendar();
    }

    // Global functions
    window.selectTime = selectTime;
    window.selectTable = selectTable;
    window.selectLocation = selectLocation;
    window.changeMonth = changeMonth;
    window.selectQuickDate = selectQuickDate;
}