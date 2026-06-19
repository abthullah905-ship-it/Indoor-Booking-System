// Booking Logic

function loadAvailability() {
    const courtId = document.getElementById('court_id').value;
    const date = document.getElementById('booking_date').value;
    const container = document.getElementById('slots-container');
    const loader = document.getElementById('loading-slots');
    
    // Reset selection
    document.getElementById('start_time').value = '';
    document.getElementById('display_time').innerText = 'None';
    updatePrice();

    if (!courtId || !date) {
        container.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: var(--text-secondary);">Please select a court and date first.</div>';
        return;
    }

    loader.style.display = 'block';
    container.innerHTML = '';

    fetch(`api/get_availability.php?court_id=${courtId}&date=${date}`)
        .then(res => res.json())
        .then(data => {
            loader.style.display = 'none';
            if (data.status === 'success') {
                if (data.data.length === 0) {
                    container.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: var(--text-secondary);">No slots available for this date.</div>';
                    return;
                }

                data.data.forEach(slot => {
                    const div = document.createElement('div');
                    div.className = `slot ${slot.status.toLowerCase()}`;
                    div.innerText = slot.time_label;
                    
                    if (slot.status === 'Available') {
                        div.onclick = () => selectTimeSlot(div, slot.raw_time, slot.time_label);
                    }
                    
                    container.appendChild(div);
                });
            } else {
                container.innerHTML = `<div style="grid-column: 1/-1; text-align: center; color: var(--danger);">${data.message}</div>`;
            }
        })
        .catch(err => {
            loader.style.display = 'none';
            container.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: var(--danger);">Failed to load availability.</div>';
        });
}

function selectTimeSlot(element, rawTime, timeLabel) {
    // Remove previous selection
    document.querySelectorAll('.slot.selected').forEach(el => el.classList.remove('selected'));
    
    // Select new
    element.classList.add('selected');
    document.getElementById('start_time').value = rawTime;
    document.getElementById('display_time').innerText = timeLabel;
    
    updatePrice();
}

function updatePrice() {
    const courtSelect = document.getElementById('court_id');
    const startTime = document.getElementById('start_time').value;
    const displayPrice = document.getElementById('display_price');
    
    if (courtSelect.value && startTime) {
        const option = courtSelect.options[courtSelect.selectedIndex];
        const price = parseFloat(option.getAttribute('data-price'));
        displayPrice.innerText = 'Rs. ' + price.toLocaleString();
    } else {
        displayPrice.innerText = 'Rs. 0';
    }
}

function submitBooking(event) {
    event.preventDefault();
    const form = event.target;
    const startTime = document.getElementById('start_time').value;
    
    if (!startTime) {
        showToast('Please select a time slot first.', 'error');
        return;
    }
    
    const formData = new FormData(form);
    const btn = form.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    
    btn.innerHTML = 'Processing...';
    btn.disabled = true;

    fetch('api/book.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        
        if (data.status === 'success') {
            showToast(data.message, 'success');
            
            // Setup Payment Modal Data
            document.getElementById('pay_booking_id').innerText = data.booking_id;
            document.getElementById('upload_booking_id').value = data.booking_id;
            
            const courtName = document.getElementById('court_id').options[document.getElementById('court_id').selectedIndex].text.split(' - ')[0];
            const date = document.getElementById('booking_date').value;
            const time = document.getElementById('display_time').innerText;
            const price = document.getElementById('display_price').innerText;
            
            document.getElementById('pay_court_name').innerText = 'Court: ' + courtName;
            document.getElementById('pay_date_time').innerText = 'Date: ' + date + ' | ' + time;
            document.getElementById('pay_amount').innerText = price;
            
            openModal('payment-modal');
        } else {
            showToast(data.message, 'error');
            loadAvailability(); // Refresh grid in case it was taken
        }
    })
    .catch(err => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        showToast('Network error occurred.', 'error');
    });
}

function closePaymentModal() {
    closeModal('payment-modal');
    window.location.href = 'profile.php';
}

function payLater() {
    showToast('You can complete the payment later from your profile.', 'success');
    closePaymentModal();
}

function uploadReceipt(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const btn = form.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    
    btn.innerHTML = 'Uploading...';
    btn.disabled = true;

    fetch('api/upload_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        
        if (data.status === 'success') {
            showToast('Receipt uploaded! Awaiting admin verification.', 'success');
            setTimeout(() => {
                window.location.href = 'profile.php';
            }, 1500);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(err => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        showToast('Upload failed due to network error.', 'error');
    });
}
