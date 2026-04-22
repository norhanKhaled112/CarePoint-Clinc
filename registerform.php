<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registeration</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" type="image/png" href="img/icon.png">
    <link rel="stylesheet" href="style2.css">
</head>

<body>
    <div class="auth-card">
        <div class="card-header">
            <h2><i class="fa-solid fa-staff-snake"></i> CarePoint</h2>
            <p id="role-subtitle">Patient Portal</p>
        </div>

       
        <div class="role-toggle">
            <button type="button" class="role-btn active" onclick="setRole('Patient')">Patient</button>
            <button type="button" class="role-btn" onclick="setRole('Doctor')">Doctor</button>
        </div>

        <!-- Login / Register Tabs -->
        <div class="tabs">
            <button type="button" class="tab-btn active" onclick="switchTab('login')">Login</button>
            <button type="button" class="tab-btn" onclick="switchTab('register')">Register</button>
        </div>

        <div class="forms-container">
            <div id="alert-box" class="alert"></div>

            <!-- Login Form -->
            <form id="login-form" class="form-section active" onsubmit="handleLogin(event)" novalidate>
                <div class="input-group">
                    <i class="fa-regular fa-envelope input-icon"></i>
                    <input type="email" id="login-email" placeholder="Email Address">
                    <div class="error-message">Valid email is required</div>
                </div>
                <div class="input-group">
                    <i class="fa-solid fa-lock input-icon"></i>
                    <input type="password" id="login-password" placeholder="Password">
                    <i class="fa-regular fa-eye password-toggle" onclick="togglePassword('login-password', this)"
                        title="Toggle Show Password"></i>
                    <div class="error-message">Password is required</div>
                </div>
                <button type="submit" class="submit-btn" id="login-btn">
                    Login as Patient <i class="fa-solid fa-arrow-right-to-bracket"></i>
                </button>
            </form>

            <!-- Register Form -->
            <form id="register-form" class="form-section" onsubmit="handleRegister(event)" novalidate>
                <div class="input-group">
                    <i class="fa-regular fa-user input-icon"></i>
                    <input type="text" id="reg-name" placeholder="Full Name">
                    <div class="error-message">Full name is required</div>
                </div>
                <div class="input-group">
                    <i class="fa-solid fa-phone input-icon"></i>
                    <input type="tel" id="reg-phone" placeholder="Phone Number">
                    <div class="error-message">Valid phone number is required</div>
                </div>
                <div class="input-group">
                    <i class="fa-regular fa-envelope input-icon"></i>
                    <input type="email" id="reg-email" placeholder="Email Address">
                    <div class="error-message">Valid email is required</div>
                </div>
                <div class="input-group doctor-only" style="display: none;">
                    <i class="fa-solid fa-stethoscope input-icon"></i>
                    <select id="reg-department" class="custom-select">
                        <option value="" disabled selected>Select Department</option>
                        <option value="Cardiology">General Medicine</option>
                        <option value="Dermatology">Cardiology</option>
                        <option value="Neurology">Neurology</option>
                        <option value="Pediatrics">Pediatrics</option>
                    </select>
                    <div class="error-message">Department is required</div>
                </div>
                <div class="input-group">
                    <i class="fa-solid fa-lock input-icon"></i>
                    <input type="password" id="reg-password" placeholder="Password">
                    <i class="fa-regular fa-eye password-toggle" onclick="togglePassword('reg-password', this)"
                        title="Toggle Show Password"></i>
                    <div class="error-message">Password must be at least 6 characters</div>
                </div>
                <div class="input-group">
                    <i class="fa-solid fa-lock input-icon"></i>
                    <input type="password" id="reg-confirm" placeholder="Confirm Password">
                    <i class="fa-regular fa-eye password-toggle" onclick="togglePassword('reg-confirm', this)"
                        title="Toggle Show Password"></i>
                    <div class="error-message">Passwords do not match</div>
                </div>
                <button type="submit" class="submit-btn" id="register-btn">
                    Register as Patient <i class="fa-solid fa-user-plus"></i>
                </button>
            </form>
        </div>
       
    </div>
       

       
    
    <script src="script.js"></script>
</body>

</html>