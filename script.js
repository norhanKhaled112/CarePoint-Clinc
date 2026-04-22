//login and register functions
let currentRole = 'Patient';

// ===== تبديل الـ Role (Patient / Doctor) =====
function setRole(role) {
    currentRole = role;

    document.querySelectorAll('.role-btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');

    document.getElementById('role-subtitle').textContent =
        role === 'Doctor' ? 'Doctor Portal' : 'Patient Portal';

    document.getElementById('login-btn').innerHTML =
        `Login as ${role} <i class="fa-solid fa-arrow-right-to-bracket"></i>`;

    document.getElementById('register-btn').innerHTML =
        `Register as ${role} <i class="fa-solid fa-user-plus"></i>`;

    // إظهار / إخفاء حقل القسم
    document.querySelectorAll('.doctor-only').forEach(el => {
        el.style.display = role === 'Doctor' ? 'flex' : 'none';
    });
}

// ===== تبديل بين Login / Register =====
function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.form-section').forEach(form => form.classList.remove('active'));

    document.querySelector(`.tab-btn[onclick="switchTab('${tab}')"]`).classList.add('active');
    document.getElementById(`${tab}-form`).classList.add('active');

    clearAlert();
}

// ===== إظهار / إخفاء كلمة المرور =====
function togglePassword(inputId, icon) {
    const input = document.getElementById(inputId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

// ===== عرض الـ Alert =====
function showAlert(message, type = 'error') {
    const box = document.getElementById('alert-box');
    box.textContent = message;
    box.className = `alert ${type} show`;
    setTimeout(() => box.classList.remove('show'), 10000);
}

function clearAlert() {
    const box = document.getElementById('alert-box');
    box.className = 'alert';
    box.textContent = '';
}

// ===== مسح الـ Errors =====
function clearErrors(formId) {
    document.querySelectorAll(`#${formId} .input-group`).forEach(g => g.classList.remove('error'));
}

function setError(inputId) {
    document.getElementById(inputId).closest('.input-group').classList.add('error');
}

// ===== تسجيل الدخول =====
async function handleLogin(event) {
    event.preventDefault();
    clearErrors('login-form');

    const email = document.getElementById('login-email').value.trim();
    const password = document.getElementById('login-password').value;

    let valid = true;

    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        setError('login-email'); valid = false;
    }
    if (!password) {
        setError('login-password'); valid = false;
    }
    if (!valid) return;

    const btn = document.getElementById('login-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Logging in...';

    try {
        const response = await fetch('login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ role: currentRole, email, password })
        });

        const result = await response.json();

        if (result.success) {
            showAlert(result.message, 'success');
            // ✅ بعد نجاح الـ Login، وجّهي المستخدم للصفحة المناسبة
            setTimeout(() => {
                if (currentRole === 'Doctor') {
                    window.location.href = 'profile.php';
                } else {
                    window.location.href = 'profile.php';
                }
            }, 1500);
        } else {
            showAlert(result.message, 'error');
        }

    } catch (err) {
        showAlert('An error occurred while logging in', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = `Login as ${currentRole} <i class="fa-solid fa-arrow-right-to-bracket"></i>`;
    }
}

// ===== إنشاء حساب جديد =====
async function handleRegister(event) {
    event.preventDefault();
    clearErrors('register-form');

    const full_name = document.getElementById('reg-name').value.trim();
    const phone = document.getElementById('reg-phone').value.trim();
    const email = document.getElementById('reg-email').value.trim();
    const department = document.getElementById('reg-department').value;
    const password = document.getElementById('reg-password').value;
    const confirm = document.getElementById('reg-confirm').value;

    let valid = true;

    if (!full_name) { setError('reg-name'); valid = false; }
    if (!phone || !/^[0-9+\-\s]{7,15}$/.test(phone)) { setError('reg-phone'); valid = false; }
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { setError('reg-email'); valid = false; }
    if (currentRole === 'Doctor' && !department) { setError('reg-department'); valid = false; }
    if (!password || password.length < 6) { setError('reg-password'); valid = false; }
    if (password !== confirm) { setError('reg-confirm'); valid = false; }
    if (!valid) return;

    const btn = document.getElementById('register-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Registering...';

    try {
        const response = await fetch('register.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ role: currentRole, full_name, phone, email, department, password, confirm })
        });

        const result = await response.json();

        if (result.success) {
            showAlert(result.message, 'success');
            // الانتقال لتبويب Login بعد التسجيل
            setTimeout(() => switchTab('login'), 2000);
        } else {
            showAlert(result.message, 'error');
        }

    } catch (err) {
        showAlert('An error occurred while registering', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = `Register as ${currentRole} <i class="fa-solid fa-user-plus"></i>`;
    }
}

