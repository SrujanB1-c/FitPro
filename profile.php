<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$message = '';
$messageType = ''; // 'success' or 'error'

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $plan = $_POST['plan'] ?? '';
    $height_ft = $_POST['height_ft'] ?? $user['height_ft'];
    $height_in = $_POST['height_in'] ?? $user['height_in'];
    $weight = $_POST['weight'] ?? $user['weight'];
    
    $updateQuery = "UPDATE users SET phone = ?, address = ?, plan = ?, height_ft = ?, height_in = ?, weight = ? WHERE id = ?";
    $stmt = $pdo->prepare($updateQuery);
    if ($stmt->execute([$phone, $address, $plan, $height_ft, $height_in, $weight, $userId])) {
        $message = "Profile updated successfully!";
        $messageType = "success";
    } else {
        $message = "Failed to update profile.";
        $messageType = "error";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_account'])) {
    $deleteQuery = "DELETE FROM users WHERE id = ?";
    $stmt = $pdo->prepare($deleteQuery);
    if ($stmt->execute([$userId])) {
        session_destroy();
        header("Location: index.php");
        exit();
    } else {
        $message = "Failed to delete account.";
        $messageType = "error";
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Fetch current user data (SELECT Query Implementation for existing fields)
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Age Calculation
$age = 0;
if ($user['dob']) {
    $dob = new DateTime($user['dob']);
    $today = new DateTime();
    $age = $today->diff($dob)->y;
}

// BMI Calculation
$bmi = 0;
if ($user['height_ft'] > 0 || $user['height_in'] > 0) {
    $heightInMeters = ($user['height_ft'] * 0.3048) + ($user['height_in'] * 0.0254);
    if ($heightInMeters > 0) {
        $bmi = round($user['weight'] / ($heightInMeters * $heightInMeters), 1);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - FitPro</title>
    
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard-container {
            max-width: 1000px;
            margin: 120px auto 60px;
            padding: 24px;
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 2rem;
        }
        
        .profile-sidebar {
            background: var(--bg-white);
            border-radius: 8px;
            padding: 2rem;
            border: 1px solid var(--border-color);
            text-align: center;
        }
        
        .profile-avatar {
            font-size: 4rem;
            background: var(--bg-subtle);
            width: 100px;
            height: 100px;
            line-height: 100px;
            border-radius: 50%;
            margin: 0 auto 1rem;
        }
        
        .profile-nav {
            margin-top: 2rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .profile-nav a {
            text-decoration: none;
            padding: 0.75rem 1rem;
            border-radius: 6px;
            color: var(--text-body);
            transition: var(--transition);
            text-align: left;
        }
        
        .profile-nav a:hover, .profile-nav a.active {
            background: var(--primary-color);
            color: white;
        }
        
        .dashboard-content {
            background: var(--bg-white);
            border-radius: 8px;
            padding: 2rem;
            border: 1px solid var(--border-color);
        }
        
        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .danger-zone {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px dashed #dc3545;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
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
                    <li><a href="profile.php" class="active">Dashboard</a></li>
                    <li><a href="profile.php?action=logout">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="dashboard-container">
        
        <div class="profile-sidebar">
            <div class="profile-avatar">👤</div>
            <h3><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h3>
            <p style="color: var(--text-light); font-size: 0.875rem; margin-bottom: 0.5rem;">Current Plan: <strong><?php echo htmlspecialchars($user['plan']); ?></strong></p>
            
            <div class="user-stats" style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-top: 1rem; text-align: left; background: var(--bg-main); padding: 1rem; border-radius: 6px; border: 1px solid var(--border-color);">
                <div class="stat-item">
                    <span style="display: block; font-size: 0.75rem; color: var(--text-light); text-transform: uppercase;">Age</span>
                    <strong style="font-size: 0.9375rem; color: var(--text-main);"><?php echo htmlspecialchars($age); ?> yrs</strong>
                </div>
                <div class="stat-item">
                    <span style="display: block; font-size: 0.75rem; color: var(--text-light); text-transform: uppercase;">BMI</span>
                    <strong style="font-size: 0.9375rem; color: var(--text-main);"><?php echo htmlspecialchars($bmi); ?></strong>
                </div>
                <div class="stat-item">
                    <span style="display: block; font-size: 0.75rem; color: var(--text-light); text-transform: uppercase;">Weight</span>
                    <strong style="font-size: 0.9375rem; color: var(--text-main);"><?php echo htmlspecialchars($user['weight']); ?> kg</strong>
                </div>
                <div class="stat-item">
                    <span style="display: block; font-size: 0.75rem; color: var(--text-light); text-transform: uppercase;">Height</span>
                    <strong style="font-size: 0.9375rem; color: var(--text-main);"><?php echo htmlspecialchars($user['height_ft']); ?>' <?php echo htmlspecialchars($user['height_in']); ?>"</strong>
                </div>
            </div>
            
            <div class="profile-nav">
                <a href="#" class="active">Profile Settings</a>
                <a href="schedule.php">View Class Schedule</a>
                <a href="profile.php?action=logout">Logout</a>
            </div>
        </div>
        
        
        <div class="dashboard-content">
            <h2>Account Details</h2>
            <p>Update your personal information and membership preferences.</p>
            
            <?php if(!empty($message)): ?>
                <div class="alert <?php echo $messageType === 'success' ? 'alert-success' : 'alert-error'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <form action="profile.php" method="post">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label>First Name (Read-only)</label>
                        <input type="text" value="<?php echo htmlspecialchars($user['first_name']); ?>" disabled style="background-color: var(--bg-subtle);">
                    </div>
                    <div class="form-group">
                        <label>Email (Read-only)</label>
                        <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled style="background-color: var(--bg-subtle);">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Age (Calculated)</label>
                        <input type="text" value="<?php echo htmlspecialchars($age); ?> yrs" disabled style="background-color: var(--bg-subtle);">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="weight">Weight (kg)</label>
                        <input type="number" id="weight" name="weight" value="<?php echo htmlspecialchars($user['weight']); ?>" step="0.1" required min="20" max="300">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label>Height (Feet & Inches)</label>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; flex: 1;">
                            <input type="number" name="height_ft" value="<?php echo htmlspecialchars($user['height_ft']); ?>" required min="1" max="8" style="margin-bottom: 0;">
                            <span style="font-size: 0.875rem; color: var(--text-light); font-weight: 500;">ft</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; flex: 1;">
                            <input type="number" name="height_in" value="<?php echo htmlspecialchars($user['height_in']); ?>" required min="0" max="11" style="margin-bottom: 0;">
                            <span style="font-size: 0.875rem; color: var(--text-light); font-weight: 500;">in</span>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" rows="3" required><?php echo htmlspecialchars($user['address']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="plan">Membership Plan</label>
                    <select id="plan" name="plan" required style="width: 100%; padding: 0.875rem 1rem; background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 6px;">
                        <option value="Basic" <?php echo ($user['plan'] == 'Basic') ? 'selected' : ''; ?>>Basic - ₹199/mo</option>
                        <option value="Pro" <?php echo ($user['plan'] == 'Pro') ? 'selected' : ''; ?>>Pro - ₹399/mo</option>
                        <option value="Elite" <?php echo ($user['plan'] == 'Elite') ? 'selected' : ''; ?>>Elite - ₹699/mo</option>
                    </select>
                </div>
                
                <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
            </form>
            
            
            <div class="danger-zone">
                <h3 style="color: #dc3545;">Danger Zone</h3>
                <p>Once you delete your account, there is no going back. Please be certain.</p>
                <form action="profile.php" method="post" onsubmit="return confirm('Are you sure you want to permanently delete your account?');">
                    <button type="submit" name="delete_account" class="btn" style="background-color: transparent; border: 1px solid #dc3545; color: #dc3545;">Delete Account</button>
                </form>
            </div>
        </div>
    </div>
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
