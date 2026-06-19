// Admin JS Utilities

function verifyPayment(id) {
    if(!confirm('Are you sure you want to mark this payment as Verified?')) return;

    const formData = new FormData();
    formData.append('booking_id', id);
    formData.append('csrf_token', CSRF_TOKEN);

    fetch('../api/verify_payment.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            showToast(data.message, 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(err => showToast('Network error occurred.', 'error'));
}

function completeBooking(id) {
    if(!confirm('Mark this booking as Completed?')) return;

    const formData = new FormData();
    formData.append('booking_id', id);
    formData.append('csrf_token', CSRF_TOKEN);

    fetch('../api/complete_booking.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            showToast(data.message, 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(err => showToast('Network error occurred.', 'error'));
}

function deleteBooking(id) {
    if(!confirm('Are you sure you want to permanently delete this booking?')) return;

    const formData = new FormData();
    formData.append('booking_id', id);
    formData.append('csrf_token', CSRF_TOKEN);

    fetch('../api/delete_booking.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            showToast(data.message, 'success');
            const row = document.getElementById('booking-row-' + id);
            if(row) row.remove();
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(err => showToast('Network error occurred.', 'error'));
}

function toggleCourtStatus(id) {
    const formData = new FormData();
    formData.append('id', id);
    formData.append('action', 'toggle_status');
    formData.append('csrf_token', CSRF_TOKEN);

    fetch('../api/update_court.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            showToast(data.message, 'success');
            setTimeout(() => window.location.reload(), 500);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(err => showToast('Network error occurred.', 'error'));
}

function deleteCourt(id) {
    if(!confirm('WARNING: Deleting a court will also delete all associated bookings. This action cannot be undone. Proceed?')) return;

    const formData = new FormData();
    formData.append('court_id', id);
    formData.append('csrf_token', CSRF_TOKEN);

    fetch('../api/delete_court.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            showToast(data.message, 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(err => showToast('Network error occurred.', 'error'));
}
