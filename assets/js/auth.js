/* ================================================
   AUTH MODAL FUNCTIONS
   ================================================ */

function openAuthModal() {
    document.getElementById('authModal').style.display = 'block';
}

function closeAuthModal() {
    document.getElementById('authModal').style.display = 'none';
}

function toggleAuth() {
    document.getElementById('loginSection').classList.toggle('hidden');
    document.getElementById('signupSection').classList.toggle('hidden');
}

function handleAuth(type) {
    const errorDiv = document.getElementById('authError');
    const params = new URLSearchParams();
    params.append('action', type);

    if (type === 'login') {
        params.append('phone',    document.getElementById('loginPhno').value);
        params.append('password', document.getElementById('loginPass').value);
    } else {
        params.append('name',     document.getElementById('regName').value);
        params.append('email',    document.getElementById('regEmail').value);
        params.append('phone',    document.getElementById('regPhone').value);
        params.append('password', document.getElementById('regPass').value);
    }

    fetch('api/auth.php', { method: 'POST', body: params })
        .then(res => res.text())
        .then(text => {
            if (text.trim() === 'success') {
                location.reload();
            } else {
                errorDiv.innerText = text;
            }
        })
        .catch(() => {
            errorDiv.innerText = 'Network error. Please try again.';
        });
}

/* Close modal on backdrop click */
window.addEventListener('click', function (event) {
    const authModal = document.getElementById('authModal');
    if (event.target === authModal) {
        authModal.style.display = 'none';
    }
});
