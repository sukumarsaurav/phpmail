<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get user's uploaded files
$stmt = $pdo->prepare("SELECT * FROM email_lists WHERE user_id = ? ORDER BY uploaded_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$email_lists = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Upload List - Bulk Email Sender</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <h1 class="main-title"><i class="fas fa-file-upload"></i> Upload Email List</h1>
            
            <!-- File Upload Form -->
            <div class="card upload-section">
                <h2><i class="fas fa-cloud-upload-alt"></i> Upload New List</h2>
                <form action="upload.php" method="post" enctype="multipart/form-data">
                    <div class="file-input-container">
                        <input type="file" name="file" id="file" accept=".csv,.xlsx,.xls" required>
                        <label for="file" class="file-label">
                            <i class="fas fa-cloud-upload-alt"></i> Choose File
                        </label>
                        <span id="file-name">No file chosen</span>
                    </div>
                    <button type="submit" class="upload-button">
                        <i class="fas fa-upload"></i> Upload
                    </button>
                </form>
            </div>

            <!-- Previous Uploads -->
            <div class="card">
                <h2><i class="fas fa-history"></i> Previous Uploads</h2>
                <div class="uploads-grid">
                    <?php foreach($email_lists as $list): ?>
                        <div class="upload-card">
                            <div class="upload-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="upload-details">
                                <h3><?php echo htmlspecialchars($list['original_filename']); ?></h3>
                                <p>Uploaded: <?php echo date('Y-m-d H:i', strtotime($list['uploaded_at'])); ?></p>
                                <button class="use-file" onclick="useFile('<?php echo htmlspecialchars($list['stored_filename']); ?>')">
                                    Use This File
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('file').addEventListener('change', function(e) {
        document.getElementById('file-name').textContent = e.target.files[0].name;
    });

    function useFile(filename) {
        fetch('upload.php?use_file=' + encodeURIComponent(filename))
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    window.location.href = 'index.php';
                }
            });
    }
    </script>
</body>
</html> 