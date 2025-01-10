<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get user's templates
$stmt = $pdo->prepare("SELECT * FROM templates WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$templates = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Templates - Bulk Email Sender</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <h1 class="main-title"><i class="fas fa-file-code"></i> Email Templates</h1>
            
            <div class="card">
                <div class="existing-templates-grid">
                    <?php foreach($templates as $template): ?>
                        <div class="existing-template-card">
                            <div class="template-preview-header">
                                <h3><?php echo htmlspecialchars($template['name']); ?></h3>
                            </div>
                            <div class="template-preview-content">
                                <iframe srcdoc="<?php echo htmlspecialchars($template['content']); ?>" 
                                        style="width: 100%; height: 100%; border: none;"></iframe>
                            </div>
                            <div class="template-preview-actions">
                                <button class="edit-template" onclick="editTemplate('<?php echo htmlspecialchars($template['name']); ?>', `<?php echo htmlspecialchars($template['content']); ?>`)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="delete-template" onclick="deleteTemplate(<?php echo $template['id']; ?>)">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="existing-template-card add-template" onclick="showNewTemplateModal()">
                        <div class="add-template-content">
                            <i class="fas fa-plus"></i>
                            <div>Add New Template</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Template Editor Modal -->
    <?php include 'includes/template_modal.php'; ?>

    <script>
    function editTemplate(name, content) {
        document.getElementById('templateName').value = name;
        document.getElementById('templateEditor').value = content;
        updatePreview(content);
        document.getElementById('templateModal').style.display = 'block';
    }

    function deleteTemplate(templateId) {
        if (confirm('Are you sure you want to delete this template?')) {
            fetch('delete_template.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'template_id=' + templateId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    }

    // ... rest of your JavaScript code ...
    </script>
</body>
</html> 