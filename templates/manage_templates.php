<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['template_content'])) {
        $template_name = $_POST['template_name'];
        file_put_contents("$template_name.html", $_POST['template_content']);
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