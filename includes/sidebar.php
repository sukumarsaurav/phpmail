<?php
if (!isset($user)) {
    $stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
}

// Get current page for active state
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<div class="sidebar">
    <div class="sidebar-header">
        <h2><i class="fas fa-envelope"></i> Email Sender</h2>
    </div>
    <ul class="nav-menu">
        <li class="nav-item <?php echo $current_page == 'index' ? 'active' : ''; ?>">
            <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
        </li>
        <li class="nav-item <?php echo $current_page == 'upload_list' ? 'active' : ''; ?>">
            <a href="upload_list.php"><i class="fas fa-file-upload"></i> Upload List</a>
        </li>
        <li class="nav-item <?php echo $current_page == 'templates' ? 'active' : ''; ?>">
            <a href="templates.php"><i class="fas fa-file-code"></i> Templates</a>
        </li>
        <li class="nav-item <?php echo $current_page == 'history' ? 'active' : ''; ?>">
            <a href="history.php"><i class="fas fa-history"></i> History</a>
        </li>
        <li class="nav-item <?php echo $current_page == 'settings' ? 'active' : ''; ?>">
            <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
        </li>
    </ul>
    <div class="user-info">
        <p><i class="fas fa-user"></i> <?php echo htmlspecialchars($user['username']); ?></p>
        <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></p>
        <a href="logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</div> 