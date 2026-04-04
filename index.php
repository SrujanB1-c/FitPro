<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Transform your fitness journey with our comprehensive online fitness management system">
    <title>FitPro - Online Fitness Management System</title>
    
    
    <link rel="stylesheet" href="style.css">
    
    <style>
        .video-section { text-align: center; background-color: var(--bg-main); padding: var(--section-padding); }
        .video-wrapper { max-width: 800px; margin: 0 auto; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-radius: 12px; overflow: hidden; }
    </style>
</head>
<body>
    <nav>
        <div class="container">
            <a href="index.php" class="logo">FitPro</a>
            <ul class="nav-links">
                <li><a href="index.php" class="active">Home</a></li>
                <li><a href="features.php">Features</a></li>
                <li><a href="membership.php">Membership</a></li>
                <li><a href="schedule.php">Schedule</a></li>
                <li><a href="contact.php">Contact</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="profile.php">Dashboard</a></li>
                    <li><a href="profile.php?action=logout">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    <section class="hero parallax parallax-hero">
        <div class="hero-content">
            <h1>Transform Your Body,<br>Elevate Your Life</h1>
            <p class="hero-tagline">
                Join thousands of fitness enthusiasts in achieving their health goals with our 
                comprehensive online fitness management platform. Track workouts, manage nutrition, 
                and connect with expert trainers—all in one place.
            </p>
            <div class="hero-buttons">
                <a href="membership.php" class="btn btn-primary btn-large">Get Started Today</a>
                <a href="features.php" class="btn btn-secondary btn-large" style="color: white; border-color: white;">Explore Features</a>
            </div>
        </div>
    </section>
    <section class="overview">
        <div class="container">
            <div class="section-header">
                <h2>Why Choose FitPro?</h2>
                <p class="section-subtitle">
                    Everything you need to succeed on your fitness journey, powered by cutting-edge technology 
                    and backed by expert guidance.
                </p>
            </div>
            
            <div class="overview-grid">
                
                <div class="overview-card">
                    <span class="overview-icon">📊</span>
                    <h3>Smart Tracking</h3>
                    <p>
                        Monitor every aspect of your fitness journey with our intelligent tracking system. 
                        From workout logs to nutrition intake, visualize your progress with detailed analytics 
                        and personalized insights.
                    </p>
                </div>

                
                <div class="overview-card">
                    <span class="overview-icon">👨‍🏫</span>
                    <h3>Expert Guidance</h3>
                    <p>
                        Connect with certified fitness trainers and nutritionists who understand your unique 
                        goals. Get personalized workout plans, dietary recommendations, and real-time feedback 
                        to maximize results.
                    </p>
                </div>

                
                <div class="overview-card">
                    <span class="overview-icon">📱</span>
                    <h3>Flexible Access</h3>
                    <p>
                        Access your fitness dashboard anytime, anywhere. Whether you're at the gym, at home, 
                        or on the go, your personalized fitness plan is always at your fingertips across all 
                        your devices.
                    </p>
                </div>

                
                <div class="overview-card">
                    <span class="overview-icon">🤝</span>
                    <h3>Community Support</h3>
                    <p>
                        Join a vibrant community of fitness enthusiasts. Share achievements, participate in 
                        challenges, and stay motivated with like-minded individuals who inspire you to push 
                        your limits every day.
                    </p>
                </div>

                
                <div class="overview-card">
                    <span class="overview-icon">🎯</span>
                    <h3>Personalized Plans</h3>
                    <p>
                        Receive workout and meal plans tailored specifically to your fitness level, goals, 
                        and preferences. Our AI-powered system adapts to your progress, ensuring continuous 
                        improvement and sustainable results.
                    </p>
                </div>

                
                <div class="overview-card">
                    <span class="overview-icon">📈</span>
                    <h3>Progress Analytics</h3>
                    <p>
                        Transform data into actionable insights with comprehensive progress reports. Track 
                        body measurements, strength gains, endurance improvements, and celebrate every 
                        milestone on your fitness journey.
                    </p>
                </div>
            </div>
        </div>
    </section>
    <section class="overview">
        <div class="container">
            <div class="section-header">
                <h2>Ready to Start Your Journey?</h2>
                <p class="section-subtitle">
                    Choose a membership plan that fits your lifestyle and take the first step toward 
                    a healthier, stronger you.
                </p>
                <div class="hero-buttons mt-4">
                    <a href="membership.php" class="btn btn-primary btn-large">View Membership Plans</a>
                    <a href="contact.php" class="btn btn-secondary btn-large">Contact Us</a>
                </div>
            </div>
        </div>
    </section>
    <footer>
        <div class="footer-content">
            
            <div class="footer-section">
                <h3>FitPro</h3>
                <p>
                    Your comprehensive online fitness management platform. Empowering individuals 
                    to achieve their health and wellness goals through intelligent tracking, 
                    expert guidance, and community support.
                </p>
            </div>

            
            <div class="footer-section">
                <h3>Quick Links</h3>
                <a href="index.php">Home</a>
                <a href="features.php">Features</a>
                <a href="membership.php">Membership Plans</a>
                <a href="contact.php">Contact Us</a>
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
    </footer>
</body>
</html>
