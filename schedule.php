<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Schedule - FitPro</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .schedule-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        .class-card {
            background: var(--bg-white);
            border: 1px solid var(--border-color);
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: var(--transition);
        }
        .class-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }
        .class-card h3 { color: var(--text-dark); margin-bottom: 0.5rem; }
        .class-tag { 
            display: inline-block; 
            background: var(--bg-subtle); 
            color: var(--primary-color);
            padding: 0.25rem 0.75rem; 
            border-radius: 20px; 
            font-size: 0.85rem; 
            font-weight: 600;
            margin-bottom: 1rem;
            border: 1px solid var(--border-color);
        }
        .class-detail { margin-bottom: 0.5rem; color: var(--text-body); font-size: 0.9375rem; }
        .filter-section {
            background: var(--bg-white);
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            border: 1px solid var(--border-color);
        }
    </style>
</head>
<body>
    <nav>
        <div class="container">
            <a href="index.php" class="logo">FitPro</a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="features.php">Features</a></li>
                <li><a href="membership.php">Membership</a></li>
                <li><a href="schedule.php" class="active">Schedule</a></li>
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

    <section class="overview" style="min-height: 80vh; padding-top: 120px;">
        <div class="container">
            <div class="section-header">
                <h2>Class Schedule</h2>
                <p class="section-subtitle">Find the perfect class to fit your goals and schedule.</p>
            </div>

            <div class="filter-section">
                <label for="classFilter" style="font-weight: 600; color: var(--text-dark);">Filter by Type:</label>
                <select id="classFilter" style="padding: 0.625rem 1rem; border-radius: 6px; border: 1px solid var(--border-color); outline: none; font-family: var(--font-body); font-size: 0.9375rem; min-width: 200px;">
                    <option value="All">All Classes</option>
                    <option value="Yoga">Yoga</option>
                    <option value="Cardio">Cardio</option>
                    <option value="Strength">Strength</option>
                </select>
                <span id="loadingIndicator" style="display: none; color: var(--primary-color); font-weight: 500;">â†» Loading...</span>
            </div>

            <div id="scheduleContainer" class="schedule-grid">
                <!-- Data will be loaded here via AJAX -->
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
    <!-- AJAX Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const classFilter = document.getElementById('classFilter');
            const scheduleContainer = document.getElementById('scheduleContainer');
            const loadingIndicator = document.getElementById('loadingIndicator');

            function fetchClasses(type) {
                loadingIndicator.style.display = 'inline';
                scheduleContainer.innerHTML = '';

                fetch('api/classes.php?type=' + encodeURIComponent(type))
                    .then(response => response.json())
                    .then(data => {
                        loadingIndicator.style.display = 'none';
                        if(data.status === 'success') {
                            const classes = data.data;
                            if(classes.length === 0) {
                                scheduleContainer.innerHTML = '<p style="color: var(--text-light);">No classes found for this category at the moment.</p>';
                                return;
                            }
                            
                            classes.forEach(cls => {
                                const card = document.createElement('div');
                                card.className = 'class-card';
                                card.innerHTML = `
                                    <span class="class-tag">${cls.type}</span>
                                    <h3>${cls.name}</h3>
                                    <div class="class-detail">🕒 <strong>Time:</strong> ${cls.schedule_time}</div>
                                    <div class="class-detail">👥 <strong>Trainer:</strong> ${cls.trainer}</div>
                                    <div class="class-detail">👥 <strong>Capacity:</strong> ${cls.capacity} spots</div>
                                `;
                                scheduleContainer.appendChild(card);
                            });
                        } else {
                            scheduleContainer.innerHTML = '<p style="color: #dc3545;">Error loading classes: ' + data.message + '</p>';
                        }
                    })
                    .catch(error => {
                        loadingIndicator.style.display = 'none';
                        scheduleContainer.innerHTML = '<p style="color: #dc3545;">Failed to connect to the server to load classes.</p>';
                        console.error('AJAX Error:', error);
                    });
            }

            // Initial load
            fetchClasses('All');

            // Listen for changes
            classFilter.addEventListener('change', function() {
                fetchClasses(this.value);
            });
        });
    </script>
</body>
</html>
