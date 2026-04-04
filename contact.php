<?php 
session_start(); 
$successMsg = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // In a real app, you would send an email or save to DB here
    $successMsg = "Thank you for your message! We'll get back to you within 24 hours.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Get in touch with FitPro - We're here to help you on your fitness journey">
    <title>Contact Us - FitPro Fitness Management</title>
    
    
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <div class="container">
            <a href="index.php" class="logo">FitPro</a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="features.php">Features</a></li>
                <li><a href="membership.php">Membership</a></li>
                <li><a href="schedule.php">Schedule</a></li>
                <li><a href="contact.php" class="active">Contact</a></li>
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
    <section class="hero">
        <div class="hero-content">
            <h1>Let's Connect</h1>
            <p class="hero-tagline">
                Have questions about our platform? Want to learn more about our membership plans? 
                Our friendly team is here to help you get started on your fitness journey.
            </p>
        </div>
    </section>
    <section class="overview">
        <div class="container">
            <div class="section-header">
                <h2>Get In Touch</h2>
                <p class="section-subtitle">
                    We're committed to providing exceptional support. Reach out to us through any 
                    of these channels, and we'll respond within 24 hours.
                </p>
            </div>
            
            <div class="contact-wrapper">
                <!-- Left Side: Contact Information -->
                <div class="contact-info">
                    <h3>Contact Information</h3>
                    
                    <div class="info-item">
                        <div class="info-icon">📧</div>
                        <div class="info-content">
                            <h4>Email Us</h4>
                            <p><a href="mailto:support@fitpro.com">support@fitpro.com</a></p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">📞</div>
                        <div class="info-content">
                            <h4>Call Us</h4>
                            <p>Main: +1 (555) 123-4567</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">📍</div>
                        <div class="info-content">
                            <h4>Visit Us</h4>
                            <p>123 Fitness Avenue, Health City, HC 12345</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">🕐</div>
                        <div class="info-content">
                            <h4>Business Hours</h4>
                            <p>Mon - Fri: 6:00 AM - 10:00 PM</p>
                            <p>Sat - Sun: 7:00 AM - 8:00 PM</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">🌐</div>
                        <div class="info-content">
                            <h4>Follow Us</h4>
                            <div class="social-links">
                                <a href="#" class="social-link" title="Facebook">f</a>
                                <a href="#" class="social-link" title="Instagram">📷</a>
                                <a href="#" class="social-link" title="Twitter">🐦</a>
                                <a href="#" class="social-link" title="YouTube">▶</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side: Contact Form -->
                <div class="contact-form-container">
                    <div class="contact-form-card">
                        <h3>Send us a Message</h3>
                        <?php if ($successMsg): ?>
                            <div class="alert alert-success">
                                <?php echo $successMsg; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form action="contact.php" method="POST" class="contact-form">
                            <div class="form-group">
                                <label for="name">Full Name</label>
                                <input type="text" id="name" name="name" placeholder="Nitish Gupta" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" placeholder="nitish@example.com" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="subject">Subject</label>
                                <select id="subject" name="subject" required>
                                    <option value="" disabled selected>Select a topic</option>
                                    <option value="membership">Membership Inquiry</option>
                                    <option value="technical">Technical Support</option>
                                    <option value="billing">Billing Question</option>
                                    <option value="feedback">General Feedback</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="message">Message</label>
                                <textarea id="message" name="message" rows="5" placeholder="How can we help you?" required></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Send Message</button>
                        </form>
                    </div>
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