<?php
session_start();
require_once 'config/database.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate password match
    if ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email already registered';
        } else {
            // Create new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            
            try {
                $stmt->execute([$username, $email, $hashed_password]);
                $_SESSION['user_id'] = $pdo->lastInsertId();
                header('Location: index.php');
                exit;
            } catch (PDOException $e) {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - Bulk Email Sender</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-box">
            <h1><i class="fas fa-envelope"></i> Email Sender</h1>
            <h2>Register</h2>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="post" class="auth-form">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Username:</label>
                    <input type="text" name="username" required>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email:</label>
                    <input type="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password:</label>
                    <input type="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Confirm Password:</label>
                    <input type="password" name="confirm_password" required>
                </div>
                
                <button type="submit" class="auth-button">
                    <i class="fas fa-user-plus"></i> Register
                </button>
            </form>
            
            <div class="auth-links">
                <a href="login.php">Already have an account? Login</a>
            </div>
        </div>
    </div>
</body>
</html> 