<?php
session_start();
require_once 'db.php';

$errorMsg = '';

if (isset($_SESSION['user_id'])) {
    header("Location: profile.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';

    if (empty($email) || empty($pass)) {
        $errorMsg = "Please enter both email and password.";
    } else {
        $stmt = $pdo->prepare("SELECT id, first_name, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($pass, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['first_name'] = $user['first_name'];
                header("Location: profile.php");
                exit();
            } else {
                $errorMsg = "Invalid email or password.";
            }
        } else {
            $errorMsg = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login to your FitPro account and continue your fitness journey">
    <title>Login - FitPro Fitness Management</title>
    
    <link rel="stylesheet" href="style.css">
</head>
<body>
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
                    <li><a href="login.php" class="active">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    <section class="login-section">
        <div class="login-container">
            <div class="login-form-wrapper">
                <div class="login-header">
                    <h1>Welcome Back</h1>
                    <p>Login to continue your fitness journey</p>
                    
                    <?php if(!empty($errorMsg)): ?>
                        <div style="background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 6px; margin-top: 1rem; border: 1px solid #f5c6cb; font-size: 0.9375rem;">
                            <?php echo htmlspecialchars($errorMsg); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <form class="login-form" id="loginForm" action="login.php" method="post" novalidate>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            placeholder="your.email@example.com" 
                            required
                        >
                        <span class="error-message" id="emailError">Please enter a valid email</span>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="Enter your password" 
                            required
                        >
                        <span class="error-message" id="passwordError">Password is required (min 8 chars)</span>
                    </div>

                    <div class="form-options">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember">
                            <span>Remember me</span>
                        </label>
                        <a href="#" class="forgot-link">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn btn-primary btn-full">
                        Login to Account
                    </button>

                    <div class="divider">
                        <span>or continue with</span>
                    </div>

                    <div class="social-login">
                        <button type="button" class="social-btn">
                            <span class="social-icon">G</span>
                            Google
                        </button>
                        <button type="button" class="social-btn">
                            <span class="social-icon">f</span>
                            Facebook
                        </button>
                    </div>

                    <p class="signup-text">
                        Don't have an account? <a href="register.php" class="signup-link">Sign up for free</a>
                    </p>
                </form>
            </div>

            <div class="login-info">
                <div class="info-content">
                    <h2>Track Your Progress</h2>
                    <p>
                        Access your personalized dashboard to monitor workouts, track nutrition, 
                        and connect with your trainer. Your fitness journey continues here.
                    </p>

                    <div class="info-features">
                        <div class="info-feature-item">
                            <span class="info-icon">📊</span>
                            <div>
                                <h4>Progress Analytics</h4>
                                <p>View detailed reports and insights</p>
                            </div>
                        </div>

                        <div class="info-feature-item">
                            <span class="info-icon">💪</span>
                            <div>
                                <h4>Workout Library</h4>
                                <p>Access 1000+ exercises and plans</p>
                            </div>
                        </div>

                        <div class="info-feature-item">
                            <span class="info-icon">🎯</span>
                            <div>
                                <h4>Goal Tracking</h4>
                                <p>Set and achieve your fitness goals</p>
                            </div>
                        </div>
                    </div>

                    <div class="trust-indicators">
                        <div class="trust-item">
                            <strong>50,000+</strong>
                            <span>Active Members</span>
                        </div>
                        <div class="trust-item">
                            <strong>4.8/5</strong>
                            <span>User Rating</span>
                        </div>
                        <div class="trust-item">
                            <strong>1M+</strong>
                            <span>Workouts Logged</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
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
                    <p>📞 Phone: +91 7021756855</p>
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
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            let isValid = true;

            const email = document.getElementById('email');
            const emailError = document.getElementById('emailError');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if(!emailRegex.test(email.value.trim())) {
                email.classList.add('error-input');
                email.classList.remove('success-input');
                emailError.classList.add('visible');
                isValid = false;
            } else {
                email.classList.remove('error-input');
                email.classList.add('success-input');
                emailError.classList.remove('visible');
            }

            const password = document.getElementById('password');
            const passwordError = document.getElementById('passwordError');
            
            if(password.value.length < 8) {
                password.classList.add('error-input');
                password.classList.remove('success-input');
                passwordError.classList.add('visible');
                isValid = false;
            } else {
                password.classList.remove('error-input');
                password.classList.add('success-input');
                passwordError.classList.remove('visible');
            }

            if(!isValid) {
                event.preventDefault();
            }
        });
    </script>

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