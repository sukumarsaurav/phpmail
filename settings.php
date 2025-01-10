<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

// Get user information
$stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Verify current password
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $stored_password = $stmt->fetchColumn();

    if (!password_verify($current_password, $stored_password)) {
        $error = 'Current password is incorrect';
    } elseif ($new_password !== $confirm_password) {
        $error = 'New passwords do not match';
    } else {
        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed_password, $_SESSION['user_id']]);
        $success = 'Password updated successfully';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Settings - Bulk Email Sender</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="sidebar">
        <!-- Include your sidebar here -->
    </div>
    
    <div class="main-content">
        <div class="container">
            <h1 class="main-title"><i class="fas fa-cog"></i> Settings</h1>
            
            <div class="card">
                <h2>Account Information</h2>
                <div class="account-info">
                    <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                </div>
            </div>

            <div class="card">
                <h2>Change Password</h2>
                
                <?php if ($error): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <form method="post" class="settings-form">
                    <div class="form-group">
                        <label>Current Password:</label>
                        <input type="password" name="current_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label>New Password:</label>
                        <input type="password" name="new_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Confirm New Password:</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" class="save-button">
                        <i class="fas fa-save"></i> Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 