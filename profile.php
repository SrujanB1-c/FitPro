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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_preferences'])) {
    $trainer = $_POST['trainer'] ?? '';
    $fitness_goal = $_POST['fitness_goal'] ?? '';
    $preferred_time = $_POST['preferred_time'] ?? '';

    $upsertQuery = "INSERT INTO user_preferences (user_id, trainer, fitness_goal, preferred_time) VALUES (?, ?, ?, ?) 
                    ON DUPLICATE KEY UPDATE trainer = VALUES(trainer), fitness_goal = VALUES(fitness_goal), preferred_time = VALUES(preferred_time)";
    $stmt = $pdo->prepare($upsertQuery);
    if ($stmt->execute([$userId, $trainer, $fitness_goal, $preferred_time])) {
        $message = "Preferences updated successfully!";
        $messageType = "success";
    } else {
        $message = "Failed to update preferences.";
        $messageType = "error";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_account'])) {
    $userStmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $userStmt->execute([$userId]);
    $userHash = $userStmt->fetchColumn();

    $delPass = $_POST['delete_password'] ?? '';
    if (password_verify($delPass, $userHash)) {
        $emailStmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
        $emailStmt->execute([$userId]);
        $userEmail = $emailStmt->fetchColumn();

        $pdo->prepare("DELETE FROM user_classes WHERE user_id = ?")->execute([$userId]);
        $pdo->prepare("DELETE FROM user_preferences WHERE user_id = ?")->execute([$userId]);
        if ($userEmail) {
            $pdo->prepare("DELETE FROM contact_messages WHERE email = ?")->execute([$userEmail]);
        }

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
    } else {
        $message = "Incorrect password. Account not deleted.";
        $messageType = "error";
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: login.php");
    exit();
}

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

// Fetch User Preferences
$prefStmt = $pdo->prepare("SELECT trainer, fitness_goal, preferred_time FROM user_preferences WHERE user_id = ?");
$prefStmt->execute([$userId]);
$pref = $prefStmt->fetch(PDO::FETCH_ASSOC);
if (!$pref) {
    $pref = ['trainer' => '', 'fitness_goal' => '', 'preferred_time' => ''];
}

// Fetch Booked Classes
$bookedClassesStmt = $pdo->prepare("
    SELECT uc.class_id, c.type, uc.class_name as name, uc.schedule_time, uc.trainer, uc.booked_at 
    FROM user_classes uc 
    LEFT JOIN classes c ON c.id = uc.class_id 
    WHERE uc.user_id = ?
    ORDER BY uc.booked_at DESC
");
$bookedClassesStmt->execute([$userId]);
$myClasses = $bookedClassesStmt->fetchAll(PDO::FETCH_ASSOC);
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

        .profile-nav a:hover,
        .profile-nav a.active {
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
                <?php if (isset($_SESSION['user_id'])): ?>
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
            <p style="color: var(--text-light); font-size: 0.875rem; margin-bottom: 0.5rem;">Current Plan:
                <strong><?php echo htmlspecialchars($user['plan']); ?></strong>
            </p>

            <div class="user-stats"
                style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-top: 1rem; text-align: left; background: var(--bg-main); padding: 1rem; border-radius: 6px; border: 1px solid var(--border-color);">
                <div class="stat-item">
                    <span
                        style="display: block; font-size: 0.75rem; color: var(--text-light); text-transform: uppercase;">Age</span>
                    <strong style="font-size: 0.9375rem; color: var(--text-main);"><?php echo htmlspecialchars($age); ?>
                        yrs</strong>
                </div>
                <div class="stat-item">
                    <span
                        style="display: block; font-size: 0.75rem; color: var(--text-light); text-transform: uppercase;">BMI</span>
                    <strong
                        style="font-size: 0.9375rem; color: var(--text-main);"><?php echo htmlspecialchars($bmi); ?></strong>
                </div>
                <div class="stat-item">
                    <span
                        style="display: block; font-size: 0.75rem; color: var(--text-light); text-transform: uppercase;">Weight</span>
                    <strong
                        style="font-size: 0.9375rem; color: var(--text-main);"><?php echo htmlspecialchars($user['weight']); ?>
                        kg</strong>
                </div>
                <div class="stat-item">
                    <span
                        style="display: block; font-size: 0.75rem; color: var(--text-light); text-transform: uppercase;">Height</span>
                    <strong
                        style="font-size: 0.9375rem; color: var(--text-main);"><?php echo htmlspecialchars($user['height_ft']); ?>'
                        <?php echo htmlspecialchars($user['height_in']); ?>"</strong>
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

            <?php if (!empty($message)): ?>
                <div class="alert <?php echo $messageType === 'success' ? 'alert-success' : 'alert-error'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form action="profile.php" method="post">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" value="<?php echo htmlspecialchars($user['first_name']); ?>" disabled
                            style="background-color: var(--bg-subtle);">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled
                            style="background-color: var(--bg-subtle);">
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>"
                        required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Age </label>
                        <input type="text" value="<?php echo htmlspecialchars($age); ?> yrs" disabled
                            style="background-color: var(--bg-subtle);">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="weight">Weight (kg)</label>
                        <input type="number" id="weight" name="weight"
                            value="<?php echo htmlspecialchars($user['weight']); ?>" step="0.1" required min="20"
                            max="300">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label>Height (Feet & Inches)</label>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; flex: 1;">
                            <input type="number" name="height_ft"
                                value="<?php echo htmlspecialchars($user['height_ft']); ?>" required min="1" max="8"
                                style="margin-bottom: 0;">
                            <span style="font-size: 0.875rem; color: var(--text-light); font-weight: 500;">ft</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; flex: 1;">
                            <input type="number" name="height_in"
                                value="<?php echo htmlspecialchars($user['height_in']); ?>" required min="0" max="11"
                                style="margin-bottom: 0;">
                            <span style="font-size: 0.875rem; color: var(--text-light); font-weight: 500;">in</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" rows="3"
                        required><?php echo htmlspecialchars($user['address']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="plan">Membership Plan</label>
                    <select id="plan" name="plan" required
                        style="width: 100%; padding: 0.875rem 1rem; background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 6px;">
                        <option value="Basic" <?php echo ($user['plan'] == 'Basic') ? 'selected' : ''; ?>>Basic - ₹199/mo
                        </option>
                        <option value="Pro" <?php echo ($user['plan'] == 'Pro') ? 'selected' : ''; ?>>Pro - ₹399/mo
                        </option>
                        <option value="Elite" <?php echo ($user['plan'] == 'Elite') ? 'selected' : ''; ?>>Elite - ₹699/mo
                        </option>
                    </select>
                </div>

                <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
            </form>


            <div class="preferences-section"
                style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                <h2>My Preferences</h2>
                <p>Customize your tracking experience across FitPro features.</p>

                <form action="profile.php" method="post" style="margin-top: 1.5rem;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="trainer">Preferred Trainer</label>
                            <select id="trainer" name="trainer"
                                style="width: 100%; padding: 0.875rem 1rem; background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 6px;">
                                <option value="">No preference</option>
                                <option value="Sara Khan" <?php echo ($pref['trainer'] == 'Sara Khan') ? 'selected' : ''; ?>>Sara Khan (Yoga)</option>
                                <option value="Nitish Gupta" <?php echo ($pref['trainer'] == 'Nitish Gupta') ? 'selected' : ''; ?>>Nitish Gupta (Cardio)</option>
                                <option value="Chris Hemsworth" <?php echo ($pref['trainer'] == 'Chris Hemsworth') ? 'selected' : ''; ?>>Chris Hemsworth (Strength)</option>
                                <option value="Jay Shetty" <?php echo ($pref['trainer'] == 'Jay Shetty') ? 'selected' : ''; ?>>Jay Shetty (Dance/Cardio)</option>
                                <option value="Siddhi Deshmukh" <?php echo ($pref['trainer'] == 'Siddhi Deshmukh') ? 'selected' : ''; ?>>Siddhi Deshmukh (Dance/Cardio)</option>

                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="fitness_goal">Primary Fitness Goal</label>
                            <select id="fitness_goal" name="fitness_goal"
                                style="width: 100%; padding: 0.875rem 1rem; background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 6px;">
                                <option value="Weight Loss" <?php echo ($pref['fitness_goal'] == 'Weight Loss') ? 'selected' : ''; ?>>Weight Loss</option>
                                <option value="Muscle Gain" <?php echo ($pref['fitness_goal'] == 'Muscle Gain') ? 'selected' : ''; ?>>Muscle Gain</option>
                                <option value="Endurance" <?php echo ($pref['fitness_goal'] == 'Endurance') ? 'selected' : ''; ?>>Endurance</option>
                                <option value="Flexibility" <?php echo ($pref['fitness_goal'] == 'Flexibility') ? 'selected' : ''; ?>>Flexibility</option>
                                <option value="General Health" <?php echo ($pref['fitness_goal'] == 'General Health') ? 'selected' : ''; ?>>General Health</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="preferred_time">Preferred Time</label>
                            <select id="preferred_time" name="preferred_time"
                                style="width: 100%; padding: 0.875rem 1rem; background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 6px;">
                                <option value="06:00 AM" <?php echo ($pref['preferred_time'] == '06:00 AM') ? 'selected' : ''; ?>>06:00 AM</option>
                                <option value="08:00 AM" <?php echo ($pref['preferred_time'] == '08:00 AM') ? 'selected' : ''; ?>>08:00 AM</option>
                                <option value="10:00 AM" <?php echo ($pref['preferred_time'] == '10:00 AM') ? 'selected' : ''; ?>>10:00 AM</option>
                                <option value="05:00 PM" <?php echo ($pref['preferred_time'] == '05:00 PM') ? 'selected' : ''; ?>>05:00 PM</option>
                                <option value="07:00 PM" <?php echo ($pref['preferred_time'] == '07:00 PM') ? 'selected' : ''; ?>>07:00 PM</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" name="update_preferences" class="btn btn-primary"
                        style="background-color: var(--text-dark);">Save Preferences</button>
                </form>
            </div>

            <div class="booked-classes-section"
                style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                <h2>My Booked Classes</h2>
                <p>Classes you have confirmed attendance for.</p>
                <div style="margin-top: 1.5rem; display: grid; gap: 1rem;">
                    <?php if (empty($myClasses)): ?>
                        <div
                            style="padding: 1rem; background: var(--bg-main); border-radius: 8px; border: 1px solid var(--border-color); color: var(--text-light); text-align: center;">
                            You have no upcoming booked classes. <br><br>
                            <a href="schedule.php" class="btn btn-primary">Browse Schedule</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($myClasses as $bc): ?>
                            <div id="class-row-<?php echo $bc['class_id']; ?>"
                                style="padding: 1rem; background: var(--bg-main); border-radius: 8px; border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <h4 style="margin-bottom: 0.25rem;"><?php echo htmlspecialchars($bc['name']); ?></h4>
                                    <p style="font-size: 0.875rem; color: var(--text-light);">📅
                                        <?php echo htmlspecialchars($bc['schedule_time']); ?> &nbsp;|&nbsp; 👥
                                        <?php echo htmlspecialchars($bc['trainer']); ?>
                                    </p>
                                </div>
                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                    <span
                                        style="font-size: 0.75rem; padding: 0.25rem 0.5rem; background: var(--primary-color); color: white; border-radius: 4px;"><?php echo htmlspecialchars($bc['type']); ?></span>
                                    <button class="btn" onclick="cancelBooking(<?php echo $bc['class_id']; ?>, this)"
                                        style="padding: 0.25rem 0.75rem; font-size: 0.75rem; background: #fff; border: 1px solid #dc3545; color: #dc3545;">Cancel</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="danger-zone">
                <h3 style="color: #dc3545;">Danger Zone</h3>
                <p>Once you delete your account, there is no going back. Please be certain.</p>
                <form id="deleteAccountForm" action="profile.php" method="post">
                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label for="delete_password" style="color: #dc3545;">Enter Password to confirm</label>
                        <input type="password" id="delete_password" name="delete_password" required
                            style="border-color: #dc3545;">
                    </div>
                    <button type="button" onclick="showDeleteModal()" class="btn"
                        style="background-color: transparent; border: 1px solid #dc3545; color: #dc3545;">Delete
                        Account</button>
                    <input type="hidden" name="delete_account" value="1">
                </form>
            </div>
        </div>
    </div>
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>FitPro</h3>
                    <p>Your ultimate destination for fitness management. Track, schedule, and transform your life with
                        our comprehensive platform.</p>
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

    <!-- Custom Cancellation Modal -->
    <div id="cancelConfirmModal"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
        <div
            style="background: var(--bg-white, #fff); padding: 2rem; border-radius: 8px; max-width: 400px; width: 90%; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center;">
            <h3 style="margin-bottom: 1rem; color: var(--text-dark, #333);">Cancel Booking?</h3>
            <p style="margin-bottom: 1.5rem; color: var(--text-body, #666);">Are you sure you want to cancel your spot
                in this
                class?</p>
            <div style="display: flex; gap: 1rem; justify-content: center;">
                <button id="closeCancelModal" class="btn"
                    style="background: var(--bg-subtle, #f0f0f0); color: var(--text-main, #333); flex: 1; border: 1px solid var(--border-color, #ddd);">Keep
                    It</button>
                <button id="confirmCancelBtn" class="btn"
                    style="flex: 1; border: none; background-color: #dc3545; color: white;">Yes, Cancel</button>
            </div>
        </div>
    </div>

    <div id="deleteConfirmModal"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
        <div
            style="background: var(--bg-white, #fff); padding: 2rem; border-radius: 8px; max-width: 400px; width: 90%; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center;">
            <h3 style="margin-bottom: 1rem; color: #dc3545;">Delete Account?</h3>
            <p style="margin-bottom: 1.5rem; color: var(--text-body, #666);">This will permanently delete your FitPro
                account and all your tracked data. This action cannot be undone.</p>
            <div style="display: flex; gap: 1rem; justify-content: center;">
                <button id="closeDeleteModal" class="btn"
                    style="background: var(--bg-subtle, #f0f0f0); color: var(--text-main, #333); flex: 1; border: 1px solid var(--border-color, #ddd);">Go
                    Back</button>
                <button id="confirmDeleteBtn" class="btn"
                    style="flex: 1; border: none; background-color: #dc3545; color: white;">Delete Forever</button>
            </div>
        </div>
    </div>

    <script>
        let pendingCancelId = null;
        let pendingCancelBtn = null;

        function cancelBooking(classId, btnElement) {
            pendingCancelId = classId;
            pendingCancelBtn = btnElement;
            document.getElementById('cancelConfirmModal').style.display = 'flex';
        }

        function showDeleteModal() {
            // Check if password field is empty
            const pass = document.getElementById('delete_password').value;
            if (!pass) {
                showToast('Please enter your password first to confirm deletion', 'error');
                return;
            }
            document.getElementById('deleteConfirmModal').style.display = 'flex';
        }

        document.addEventListener('DOMContentLoaded', function () {
            const menuToggle = document.querySelector('.menu-toggle');
            const navLinks = document.querySelector('.nav-links');
            if (menuToggle && navLinks) {
                menuToggle.addEventListener('click', () => {
                    navLinks.classList.toggle('active');
                });
            }

            const closeCancelModal = document.getElementById('closeCancelModal');
            if (closeCancelModal) {
                closeCancelModal.onclick = () => {
                    document.getElementById('cancelConfirmModal').style.display = 'none';
                }
            }

            const closeDeleteModal = document.getElementById('closeDeleteModal');
            if (closeDeleteModal) {
                closeDeleteModal.onclick = () => {
                    document.getElementById('deleteConfirmModal').style.display = 'none';
                }
            }

            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            if (confirmDeleteBtn) {
                confirmDeleteBtn.onclick = () => {
                    const pass = document.getElementById('delete_password').value;
                    if (!pass) {
                        document.getElementById('deleteConfirmModal').style.display = 'none';
                        showToast('Password required', 'error');
                        return;
                    }
                    document.getElementById('deleteAccountForm').submit();
                }
            }

            const confirmCancelBtn = document.getElementById('confirmCancelBtn');
            if (confirmCancelBtn) {
                confirmCancelBtn.onclick = () => {
                    const classId = pendingCancelId;
                    const btnElement = pendingCancelBtn;
                    document.getElementById('cancelConfirmModal').style.display = 'none';

                    if (!btnElement) return;

                    btnElement.textContent = '...';
                    btnElement.disabled = true;

                    const formData = new URLSearchParams();
                    formData.append('class_id', classId);

                    fetch('api/cancel_booking.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: formData
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                const row = document.getElementById('class-row-' + classId);
                                if (row) row.style.opacity = '0';
                                setTimeout(() => {
                                    if (row) row.remove();
                                    showToast('Class cancelled successfully!', 'success');

                                    const container = document.querySelector('.booked-classes-section div[style*="grid"]');
                                    if (container && container.children.length === 0) {
                                        location.reload();
                                    }
                                }, 500);
                            } else {
                                btnElement.textContent = 'Cancel';
                                btnElement.disabled = false;
                                showToast(data.message || 'Error cancelling booking', 'error');
                            }
                        })
                        .catch(err => {
                            btnElement.textContent = 'Cancel';
                            btnElement.disabled = false;
                            showToast('Network error while cancelling', 'error');
                        });
                }
            }
        });
    </script>
    <script src="toast.js"></script>
    <script src="logout.js"></script>
</body>

</html>