<?php
session_start();
require_once 'db.php';

$errorMsg = '';
$successMsg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $confirmPass = $_POST['confirmPassword'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $dob = $_POST['dob'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $height_ft = $_POST['height_ft'] ?? '';
    $height_in = $_POST['height_in'] ?? '';
    $weight = $_POST['weight'] ?? '';
    $address = trim($_POST['address'] ?? '');
    $plan = $_POST['plan'] ?? '';
    
    // Server-side validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($pass) || empty($height_ft) || empty($weight)) {
        $errorMsg = "Required fields are missing.";
    } elseif ($pass !== $confirmPass) {
        $errorMsg = "Passwords do not match.";
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $errorMsg = "Email is already registered.";
        } else {
            // Insert user
            $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);
            $insertQuery = "INSERT INTO users (first_name, last_name, email, password, phone, dob, gender, height_ft, height_in, weight, address, plan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($insertQuery);
            if ($stmt->execute([$firstName, $lastName, $email, $hashedPassword, $phone, $dob, $gender, $height_ft, $height_in, $weight, $address, $plan])) {
                $successMsg = "Registration successful! You can now <a href='login.php'>login</a>.";
            } else {
                $errorMsg = "Registration failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - FitPro Fitness Management</title>
    <!-- Stylesheet -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- ================================
         NAVIGATION BAR
         ================================ -->
    <nav>
        <div class="container">
            <a href="index.php" class="logo">FitPro</a>
            <button class="menu-toggle" aria-label="Toggle menu">☰</button>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="features.php">Features</a></li>
                <li><a href="membership.php">Membership</a></li>
                <li><a href="schedule.php">Schedule</a></li>
                <li><a href="contact.php">Contact</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="profile.php">Dashboard</a></li>
                    <li><a href="profile.php?action=logout">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php" class="active">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- ================================
         REGISTER SECTION
         ================================ -->
    <section class="login-section">
        <div class="login-container" style="max-width: 800px; grid-template-columns: 1fr;">
            <!-- Register Form -->
            <div class="login-form-wrapper">
                <div class="login-header">
                    <h1>Create Your Account</h1>
                    <p>Join FitPro to track your progress and achieve your goals</p>
                    
                    <?php if(!empty($errorMsg)): ?>
                        <div style="background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 6px; margin-top: 1rem; border: 1px solid #f5c6cb; font-size: 0.9375rem;">
                            <?php echo htmlspecialchars($errorMsg); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(!empty($successMsg)): ?>
                        <div style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 6px; margin-top: 1rem; border: 1px solid #c3e6cb; font-size: 0.9375rem;">
                            <?php echo $successMsg; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <form class="login-form" id="registerForm" action="register.php" method="post" novalidate>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <!-- First Name (1) -->
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="firstName">First Name</label>
                            <input type="text" id="firstName" name="firstName" placeholder="First Name" required minlength="2" value="<?php echo htmlspecialchars($firstName ?? ''); ?>">
                            <span class="error-message" id="firstNameError">First name goes here</span>
                        </div>
                        
                        <!-- Last Name (2) -->
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="lastName">Last Name</label>
                            <input type="text" id="lastName" name="lastName" placeholder="Last Name" required minlength="2" value="<?php echo htmlspecialchars($lastName ?? ''); ?>">
                            <span class="error-message" id="lastNameError">Last name goes here</span>
                        </div>
                    </div>

                    <!-- Email (3) -->
                    <div class="form-group mt-3" style="margin-top: 1.5rem;">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="your.email@example.com" required value="<?php echo htmlspecialchars($email ?? ''); ?>">
                        <span class="error-message" id="emailError">Please enter a valid email</span>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem;">
                        <!-- Password (4) -->
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" placeholder="Create a password" required minlength="8">
                            <span class="error-message" id="passwordError">Password must be at least 8 characters</span>
                        </div>
                        
                        <!-- Confirm Password (5) -->
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="confirmPassword">Confirm Password</label>
                            <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm your password" required>
                            <span class="error-message" id="confirmPasswordError">Passwords do not match</span>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem;">
                        <!-- Phone Number (6) -->
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" placeholder="e.g. 1234567890" pattern="^[0-9]{10}$" required value="<?php echo htmlspecialchars($phone ?? ''); ?>">
                            <span class="error-message" id="phoneError">Please enter a valid 10-digit phone number</span>
                        </div>
                        
                        <!-- Date of Birth (7) -->
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="dob">Date of Birth</label>
                            <input type="date" id="dob" name="dob" required value="<?php echo htmlspecialchars($dob ?? ''); ?>">
                            <span class="error-message" id="dobError">Please select your date of birth</span>
                        </div>
                    </div>

                    <!-- Gender (8) -->
                    <div class="form-group" style="margin-top: 1.5rem;">
                        <label>Gender</label>
                        <div style="display: flex; gap: 1rem;">
                            <label class="checkbox-label" style="font-weight: normal;"><input type="radio" name="gender" value="Male" required <?php echo (isset($gender) && $gender == 'Male') ? 'checked' : ''; ?>> Male</label>
                            <label class="checkbox-label" style="font-weight: normal;"><input type="radio" name="gender" value="Female" <?php echo (isset($gender) && $gender == 'Female') ? 'checked' : ''; ?>> Female</label>
                            <label class="checkbox-label" style="font-weight: normal;"><input type="radio" name="gender" value="Other" <?php echo (isset($gender) && $gender == 'Other') ? 'checked' : ''; ?>> Other</label>
                        </div>
                        <span class="error-message" id="genderError">Please select your gender</span>
                    </div>

                    <div class="form-group" style="margin-top: 1.5rem;">
                        <label for="weight">Weight (kg)</label>
                        <input type="number" id="weight" name="weight" placeholder="Weight in kg" step="0.1" required min="20" max="300" value="<?php echo htmlspecialchars($weight ?? ''); ?>">
                        <span class="error-message" id="weightError">Valid weight (20-300kg)</span>
                    </div>

                    <div class="form-group" style="margin-top: 1.5rem;">
                        <label>Height (Feet & Inches)</label>
                        <div style="display: flex; gap: 1rem; align-items: center;">
                            <div style="display: flex; align-items: center; gap: 0.5rem; flex: 1;">
                                <input type="number" id="height_ft" name="height_ft" placeholder="Ft" required min="1" max="8" style="margin-bottom: 0;" value="<?php echo htmlspecialchars($height_ft ?? ''); ?>">
                                <span style="font-size: 0.875rem; color: var(--text-light); font-weight: 500;">ft</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.5rem; flex: 1;">
                                <input type="number" id="height_in" name="height_in" placeholder="In" required min="0" max="11" style="margin-bottom: 0;" value="<?php echo htmlspecialchars($height_in ?? ''); ?>">
                                <span style="font-size: 0.875rem; color: var(--text-light); font-weight: 500;">in</span>
                            </div>
                        </div>
                        <span class="error-message" id="heightError">Enter valid height (1-8 ft)</span>
                    </div>

                    <!-- Address (9) -->
                    <div class="form-group" style="margin-top: 1.5rem;">
                        <label for="address">Full Address</label>
                        <textarea id="address" name="address" placeholder="Enter your full address" rows="2" style="min-height: 80px;" required minlength="10"><?php echo htmlspecialchars($address ?? ''); ?></textarea>
                        <span class="error-message" id="addressError">Please enter a valid address (min 10 chars)</span>
                    </div>

                    <!-- Plan Selection (10) -->
                    <div class="form-group" style="margin-top: 1.5rem;">
                        <label for="plan">Select Membership Plan</label>
                        <select id="plan" name="plan" required style="width: 100%; padding: 0.875rem 1rem; background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 6px; font-family: var(--font-body); font-size: 0.9375rem;">
                            <option value="">-- Choose a plan --</option>
                            <option value="Basic" <?php echo (isset($plan) && $plan == 'Basic') ? 'selected' : ''; ?>>Basic - ₹199/mo</option>
                            <option value="Pro" <?php echo (isset($plan) && $plan == 'Pro') ? 'selected' : ''; ?>>Pro - ₹399/mo</option>
                            <option value="Elite" <?php echo (isset($plan) && $plan == 'Elite') ? 'selected' : ''; ?>>Elite - ₹699/mo</option>
                        </select>
                        <span class="error-message" id="planError">Please select a membership plan</span>
                    </div>

                    <!-- Terms and Conditions (11) -->
                    <div class="form-options" style="margin-top: 1.5rem; justify-content: flex-start;">
                        <label class="checkbox-label">
                            <input type="checkbox" id="terms" name="terms" required>
                            <span>I agree to the <a href="#" class="forgot-link">Terms and Conditions</a></span>
                        </label>
                    </div>
                    <span class="error-message" id="termsError" style="margin-bottom: 1.5rem;">You must agree to the terms and conditions</span>

                    <!-- Submit Button -->
                    <button type="submit" name="register" class="btn btn-primary btn-full" style="margin-top: 1.5rem;">
                        Register Account
                    </button>

                    <!-- Login Link -->
                    <p class="signup-text">
                        Already have an account? <a href="login.php" class="signup-link">Login here</a>
                    </p>
                </form>
            </div>
        </div>
    </section>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function(event) {
            let isValid = true;

            function showError(inputId, errorId, message) {
                const input = document.getElementById(inputId);
                const error = document.getElementById(errorId);
                if (input) { input.classList.add('error-input'); input.classList.remove('success-input'); }
                if (error) { error.textContent = message; error.classList.add('visible'); }
            }

            function clearError(inputId, errorId) {
                const input = document.getElementById(inputId);
                const error = document.getElementById(errorId);
                if (input) { input.classList.remove('error-input'); input.classList.add('success-input'); }
                if (error) { error.classList.remove('visible'); }
            }

            // 1. First Name
            const firstName = document.getElementById('firstName').value.trim();
            if(firstName.length < 2) {
                showError('firstName', 'firstNameError', 'First name is required (min 2 chars)');
                isValid = false;
            } else { clearError('firstName', 'firstNameError'); }

            // 2. Last Name
            const lastName = document.getElementById('lastName').value.trim();
            if(lastName.length < 2) {
                showError('lastName', 'lastNameError', 'Last name is required (min 2 chars)');
                isValid = false;
            } else { clearError('lastName', 'lastNameError'); }

            // 3. Email
            const email = document.getElementById('email').value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if(!emailRegex.test(email)) {
                showError('email', 'emailError', 'Please enter a valid email address');
                isValid = false;
            } else { clearError('email', 'emailError'); }

            // 4. Password
            const password = document.getElementById('password').value;
            if(password.length < 8) {
                showError('password', 'passwordError', 'Password must be at least 8 characters');
                isValid = false;
            } else { clearError('password', 'passwordError'); }

            // 5. Confirm Password
            const confirmPassword = document.getElementById('confirmPassword').value;
            if(confirmPassword === '' || confirmPassword !== password) {
                showError('confirmPassword', 'confirmPasswordError', 'Passwords do not match');
                isValid = false;
            } else { clearError('confirmPassword', 'confirmPasswordError'); }

            // 6. Phone
            const phone = document.getElementById('phone').value.trim();
            const phoneRegex = /^[0-9]{10}$/;
            if(!phoneRegex.test(phone)) {
                showError('phone', 'phoneError', 'Enter a valid 10-digit phone number');
                isValid = false;
            } else { clearError('phone', 'phoneError'); }

            // 7. DOB
            const dob = document.getElementById('dob').value;
            if(dob === '') {
                showError('dob', 'dobError', 'Date of birth is required');
                isValid = false;
            } else { clearError('dob', 'dobError'); }

            // 8. Gender
            const genderOptions = document.getElementsByName('gender');
            let genderSelected = false;
            for(let i = 0; i < genderOptions.length; i++) {
                if(genderOptions[i].checked) genderSelected = true;
            }
            if(!genderSelected) {
                document.getElementById('genderError').textContent = 'Please select your gender';
                document.getElementById('genderError').classList.add('visible');
                isValid = false;
            } else { document.getElementById('genderError').classList.remove('visible'); }

            // 9. Weight
            const weight = document.getElementById('weight').value;
            if(weight === '' || weight < 20 || weight > 300) {
                showError('weight', 'weightError', 'Enter a valid weight between 20-300 kg');
                isValid = false;
            } else { clearError('weight', 'weightError'); }

            // 10. Height
            const heightFt = document.getElementById('height_ft').value;
            if(heightFt === '' || heightFt < 1 || heightFt > 8) {
                showError('height_ft', 'heightError', 'Enter a valid height (1-8 ft)');
                isValid = false;
            } else { clearError('height_ft', 'heightError'); }

            // 11. Address
            const address = document.getElementById('address').value.trim();
            if(address.length < 10) {
                showError('address', 'addressError', 'Address must be at least 10 characters');
                isValid = false;
            } else { clearError('address', 'addressError'); }

            // 12. Plan
            const plan = document.getElementById('plan').value;
            if(plan === '') {
                showError('plan', 'planError', 'Please select a membership plan');
                isValid = false;
            } else { clearError('plan', 'planError'); }

            // 13. Terms
            const terms = document.getElementById('terms').checked;
            if(!terms) {
                document.getElementById('termsError').textContent = 'You must agree to the terms';
                document.getElementById('termsError').classList.add('visible');
                isValid = false;
            } else { document.getElementById('termsError').classList.remove('visible'); }

            if(!isValid) {
                event.preventDefault();
            }
        });
    </script>
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>FitPro</h3>
                    <p>Your ultimate destination for fitness management. Track, schedule, and transform your life with our comprehensive platform.</p>
                </div>
                <div class="footer-section">
                    <h3>Contact Us</h3>
                    <p>📧 Email: <a href="mailto:support@fitpro.com">support@fitpro.com</a></p>
                    <p>📞 Phone: +1 (555) 123-4567</p>
                    <p>📍 Address: 123 Fitness Avenue, Health City, HC 12345</p>
                    <p>🕐 Hours: Mon-Fri: 6AM - 10PM, Sat-Sun: 7AM - 8PM</p>
                </div>
                <div class="footer-section">
                    <h3>Follow Us</h3>
                    <p>Stay connected and get daily fitness inspiration</p>
                    <div class="social-links">
                        <a href="#" class="social-link" title="Facebook">f</a>
                        <a href="#" class="social-link" title="Instagram">📷</a>
                        <a href="#" class="social-link" title="Twitter">🐦</a>
                        <a href="#" class="social-link" title="YouTube">▶</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 FitPro - Online Fitness Management System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.querySelector('.menu-toggle');
            const navLinks = document.querySelector('.nav-links');
            if (menuToggle && navLinks) {
                menuToggle.addEventListener('click', () => {
                    navLinks.classList.toggle('active');
                });
            }
        });
    </script>
</body>
</html>
