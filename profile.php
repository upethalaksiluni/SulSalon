<?php
session_start();
require_once 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?login=true');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user data
try {
    $stmt = $conn->prepare("SELECT * FROM user WHERE id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        // User not found
        session_destroy();
        header('Location: index.php?login=true');
        exit();
    }
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

// Initialize user data with default values if not set
$user['fullname'] = $user['fullname'] ?? '';
$user['gender'] = $user['gender'] ?? '';
$user['birthdate'] = $user['birthdate'] ?? '';
$user['phone'] = $user['phone'] ?? '';
$user['email'] = $user['email'] ?? '';
$user['address'] = $user['address'] ?? '';
$user['profile_image'] = $user['profile_image'] ?? '';
$user['preferred_stylist'] = $user['preferred_stylist'] ?? '';
$user['frequent_services'] = $user['frequent_services'] ?? '';
$user['preferred_time'] = $user['preferred_time'] ?? '';
$user['allergies'] = $user['allergies'] ?? '';
$user['medical_conditions'] = $user['medical_conditions'] ?? '';
$user['username'] = $user['username'] ?? '';

// Sanitize input function - Modified to not use $conn parameter
function sanitize_input($input) {
    if (is_string($input)) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    return $input;
}

// Handle profile update
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    // Validate required fields
    $requiredFields = ['fullname', 'gender', 'birthdate', 'phone', 'email'];
    $missingFields = [];
    
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $missingFields[] = $field;
        }
    }
    
    if (!empty($missingFields)) {
        $message = "Please fill in all required fields: " . implode(', ', $missingFields);
        $messageType = 'error';
    } else {
        // Validate email format
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $message = "Invalid email format";
            $messageType = 'error';
        } else {
            // Check if email is already used by another user
            try {
                $email = sanitize_input($_POST['email']);
                $checkEmail = $conn->prepare("SELECT id FROM user WHERE email = :email AND id != :user_id");
                $checkEmail->bindParam(':email', $email);
                $checkEmail->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $checkEmail->execute();
                
                if ($checkEmail->rowCount() > 0) {
                    $message = "Email already in use by another account";
                    $messageType = 'error';
                } else {
                    // Process profile image if uploaded
                    $profile_image = $user['profile_image']; // Keep existing image by default
                    
                    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['size'] > 0) {
                        $target_dir = "uploads/";
                        
                        // Create directory if it doesn't exist
                        if (!file_exists($target_dir)) {
                            mkdir($target_dir, 0777, true);
                        }
                        
                        $file_extension = strtolower(pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION));
                        $new_filename = "profile_" . $user_id . "_" . time() . "." . $file_extension;
                        $target_file = $target_dir . $new_filename;
                        
                        // Check file type
                        $allowed_types = ["jpg", "jpeg", "png", "gif"];
                        if (!in_array($file_extension, $allowed_types)) {
                            $message = "Only JPG, JPEG, PNG & GIF files are allowed";
                            $messageType = 'error';
                        } else if ($_FILES["profile_image"]["size"] > 5000000) { // 5MB max
                            $message = "File is too large (max 5MB)";
                            $messageType = 'error';
                        } else if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                            // Delete old profile image if exists and not default
                            if ($profile_image && $profile_image != "default.jpg" && file_exists($target_dir . $profile_image)) {
                                unlink($target_dir . $profile_image);
                            }
                            $profile_image = $new_filename;
                        } else {
                            $message = "Error uploading file";
                            $messageType = 'error';
                        }
                    }
                    
                    // If no errors, update profile
                    if (empty($message)) {
                        $fullname = sanitize_input($_POST['fullname']);
                        $gender = sanitize_input($_POST['gender']);
                        $birthdate = sanitize_input($_POST['birthdate']);
                        $phone = sanitize_input($_POST['phone']);
                        $address = sanitize_input($_POST['address'] ?? '');
                        $preferred_stylist = sanitize_input($_POST['preferred_stylist'] ?? '');
                        $frequent_services = sanitize_input($_POST['frequent_services'] ?? '');
                        $preferred_time = sanitize_input($_POST['preferred_time'] ?? '');
                        $allergies = sanitize_input($_POST['allergies'] ?? '');
                        $medical_conditions = sanitize_input($_POST['medical_conditions'] ?? '');
                        
                        try {
                            $updateStmt = $conn->prepare("UPDATE user SET 
                                fullname = :fullname, 
                                gender = :gender, 
                                birthdate = :birthdate, 
                                phone = :phone, 
                                email = :email, 
                                address = :address, 
                                profile_image = :profile_image,
                                preferred_stylist = :preferred_stylist,
                                frequent_services = :frequent_services,
                                preferred_time = :preferred_time,
                                allergies = :allergies,
                                medical_conditions = :medical_conditions
                                WHERE id = :user_id");
                            
                            $updateStmt->bindParam(':fullname', $fullname);
                            $updateStmt->bindParam(':gender', $gender);
                            $updateStmt->bindParam(':birthdate', $birthdate);
                            $updateStmt->bindParam(':phone', $phone);
                            $updateStmt->bindParam(':email', $email);
                            $updateStmt->bindParam(':address', $address);
                            $updateStmt->bindParam(':profile_image', $profile_image);
                            $updateStmt->bindParam(':preferred_stylist', $preferred_stylist);
                            $updateStmt->bindParam(':frequent_services', $frequent_services);
                            $updateStmt->bindParam(':preferred_time', $preferred_time);
                            $updateStmt->bindParam(':allergies', $allergies);
                            $updateStmt->bindParam(':medical_conditions', $medical_conditions);
                            $updateStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                            
                            if ($updateStmt->execute()) {
                                $message = "Profile updated successfully";
                                $messageType = 'success';
                                
                                // Refresh user data
                                $stmt->execute();
                                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                                
                                // Initialize refreshed user data with default values if not set
                                $user['fullname'] = $user['fullname'] ?? '';
                                $user['gender'] = $user['gender'] ?? '';
                                $user['birthdate'] = $user['birthdate'] ?? '';
                                $user['phone'] = $user['phone'] ?? '';
                                $user['email'] = $user['email'] ?? '';
                                $user['address'] = $user['address'] ?? '';
                                $user['profile_image'] = $user['profile_image'] ?? '';
                                $user['preferred_stylist'] = $user['preferred_stylist'] ?? '';
                                $user['frequent_services'] = $user['frequent_services'] ?? '';
                                $user['preferred_time'] = $user['preferred_time'] ?? '';
                                $user['allergies'] = $user['allergies'] ?? '';
                                $user['medical_conditions'] = $user['medical_conditions'] ?? '';
                                $user['username'] = $user['username'] ?? '';
                            } else {
                                $message = "Error updating profile";
                                $messageType = 'error';
                            }
                        } catch (PDOException $e) {
                            $message = "Error updating profile: " . $e->getMessage();
                            $messageType = 'error';
                        }
                    }
                }
            } catch (PDOException $e) {
                $message = "Error checking email: " . $e->getMessage();
                $messageType = 'error';
            }
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $message = "All password fields are required";
        $messageType = 'error';
    } else if ($new_password !== $confirm_password) {
        $message = "New passwords do not match";
        $messageType = 'error';
    } else if (!password_verify($current_password, $user['password'])) {
        $message = "Current password is incorrect";
        $messageType = 'error';
    } else if (!preg_match('/^(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*])(?=.{8,})/', $new_password)) {
        $message = "New password must be 8+ chars, include 1 uppercase, 1 number, and 1 special char";
        $messageType = 'error';
    } else {
        // Hash new password and update
        try {
            $new_hash = password_hash($new_password, PASSWORD_BCRYPT);
            $updatePwdStmt = $conn->prepare("UPDATE user SET password = :password WHERE id = :user_id");
            $updatePwdStmt->bindParam(':password', $new_hash);
            $updatePwdStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            
            if ($updatePwdStmt->execute()) {
                $message = "Password changed successfully";
                $messageType = 'success';
            } else {
                $message = "Error changing password";
                $messageType = 'error';
            }
        } catch (PDOException $e) {
            $message = "Error changing password: " . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Get image URL with default fallback
$profileImageUrl = "uploads/" . ($user['profile_image'] ?: 'default.jpg');
if (!file_exists($profileImageUrl)) {
    $profileImageUrl = "uploads/default.jpg";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Salon App</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>My Profile</h1>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>
        
        <div class="profile-container">
            <div class="sidebar">
                <div class="profile-image">
                    <img src="<?php echo htmlspecialchars($profileImageUrl); ?>" alt="Profile picture">
                    <button type="button" class="change-photo-btn" id="change-photo-btn">Change Photo</button>
                </div>
                <div class="sidebar-nav">
                    <button type="button" class="nav-item active" data-tab="personal-info">Personal Information</button>
                    <button type="button" class="nav-item" data-tab="service-preferences">Service Preferences</button>
                    <button type="button" class="nav-item" data-tab="health-info">Health Information</button>
                    <button type="button" class="nav-item" data-tab="account-settings">Account Settings</button>
                </div>
            </div>
            
            <div class="main-content">
                <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
                <?php endif; ?>
                
                <form id="profile-form" method="post" enctype="multipart/form-data">
                    <input type="file" id="profile-image-input" name="profile_image" accept="image/*" style="display: none;">
                    
                    <!-- Personal Information Tab -->
                    <div class="tab-content active" id="personal-info">
                        <h2>Personal Information</h2>
                        <div class="form-group">
                            <label for="fullname">Full Name *</label>
                            <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="gender">Gender *</label>
                            <select id="gender" name="gender" required>
                                <option value="" <?php echo $user['gender'] === '' ? 'selected' : ''; ?>>Select Gender</option>
                                <option value="Male" <?php echo $user['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo $user['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="birthdate">Date of Birth *</label>
                            <input type="date" id="birthdate" name="birthdate" value="<?php echo htmlspecialchars($user['birthdate']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Home Address</label>
                            <textarea id="address" name="address"><?php echo htmlspecialchars($user['address']); ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Service Preferences Tab -->
                    <div class="tab-content" id="service-preferences">
                        <h2>Service Preferences</h2>
                        <div class="form-group">
                            <label for="preferred_stylist">Preferred Stylist</label>
                            <input type="text" id="preferred_stylist" name="preferred_stylist" value="<?php echo htmlspecialchars($user['preferred_stylist']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="frequent_services">Frequent Services</label>
                            <input type="text" id="frequent_services" name="frequent_services" value="<?php echo htmlspecialchars($user['frequent_services']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="preferred_time">Preferred Time</label>
                            <select id="preferred_time" name="preferred_time">
                                <option value="">Select Preferred Time</option>
                                <option value="Morning" <?php echo $user['preferred_time'] === 'Morning' ? 'selected' : ''; ?>>Morning</option>
                                <option value="Afternoon" <?php echo $user['preferred_time'] === 'Afternoon' ? 'selected' : ''; ?>>Afternoon</option>
                                <option value="Evening" <?php echo $user['preferred_time'] === 'Evening' ? 'selected' : ''; ?>>Evening</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Health Information Tab -->
                    <div class="tab-content" id="health-info">
                        <h2>Health Information</h2>
                        <div class="form-group">
                            <label for="allergies">Allergies</label>
                            <textarea id="allergies" name="allergies"><?php echo htmlspecialchars($user['allergies']); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="medical_conditions">Medical Conditions</label>
                            <textarea id="medical_conditions" name="medical_conditions"><?php echo htmlspecialchars($user['medical_conditions']); ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Account Settings Tab -->
                    <div class="tab-content" id="account-settings">
                        <h2>Account Settings</h2>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                            <p class="form-hint">Username cannot be changed</p>
                        </div>
                        
                        <h3>Change Password</h3>
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password">
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password">
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password">
                        </div>
                        
                        <div class="password-requirements">
                            <p>Password must contain:</p>
                            <ul>
                                <li>At least 8 characters</li>
                                <li>At least one uppercase letter</li>
                                <li>At least one number</li>
                                <li>At least one special character</li>
                            </ul>
                        </div>
                        
                        <button type="submit" name="change_password" class="btn">Change Password</button>
                    </div>
                    
                    <div class="action-buttons">
                        <button type="submit" name="update_profile" class="btn save-btn">Save Changes</button>
                        <button type="button" id="cancel-btn" class="btn cancel-btn">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="profile.js"></script>
</body>
</html>