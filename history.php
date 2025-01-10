<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Add a sent_emails table to track email history
$stmt = $pdo->prepare("
    SELECT e.*, t.name as template_name, l.original_filename 
    FROM sent_emails e 
    LEFT JOIN templates t ON e.template_id = t.id 
    LEFT JOIN email_lists l ON e.list_id = l.id 
    WHERE e.user_id = ? 
    ORDER BY e.sent_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$sent_emails = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>History - Bulk Email Sender</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <h1 class="main-title"><i class="fas fa-history"></i> Sending History</h1>
            
            <div class="card">
                <div class="history-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Template</th>
                                <th>List</th>
                                <th>Recipients</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($sent_emails as $email): ?>
                                <tr>
                                    <td><?php echo date('Y-m-d H:i', strtotime($email['sent_at'])); ?></td>
                                    <td><?php echo htmlspecialchars($email['template_name']); ?></td>
                                    <td><?php echo htmlspecialchars($email['original_filename']); ?></td>
                                    <td><?php echo $email['recipient_count']; ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $email['status']; ?>">
                                            <?php echo ucfirst($email['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 