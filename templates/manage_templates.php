<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['template_content'])) {
        $template_name = $_POST['template_name'];
        $content = $_POST['template_content'];
        $user_id = $_SESSION['user_id'];

        $stmt = $pdo->prepare("INSERT INTO templates (user_id, name, content) VALUES (?, ?, ?) 
                              ON DUPLICATE KEY UPDATE content = ?");
        $stmt->execute([$user_id, $template_name, $content, $content]);
        
        header('Location: ../index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Templates</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Manage Email Templates</h1>
        
        <!-- Add New Template -->
        <div class="template-form">
            <h2>Add New Template</h2>
            <form method="post">
                <input type="text" name="template_name" placeholder="Template Name" required>
                <textarea name="template_content" rows="10" required></textarea>
                <p>Available variables: {name}, {email}</p>
                <button type="submit">Save Template</button>
            </form>
        </div>
        
        <!-- Existing Templates -->
        <div class="existing-templates">
            <h2>Existing Templates</h2>
            <?php
            $templates = glob('*.html');
            foreach($templates as $template) {
                echo "<div class='template-item'>";
                echo "<h3>" . basename($template) . "</h3>";
                echo "<pre>" . htmlspecialchars(file_get_contents($template)) . "</pre>";
                echo "</div>";
            }
            ?>
        </div>
    </div>
</body>
</html> 