<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Bulk Email Sender</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Bulk Email Sender</h1>
        
        <!-- File Upload Form -->
        <div class="upload-section">
            <h2>Upload Email List</h2>
            <form action="upload.php" method="post" enctype="multipart/form-data">
                <input type="file" name="file" accept=".csv,.xlsx,.xls" required>
                <button type="submit">Upload</button>
            </form>
        </div>

        <!-- Column Mapping Section -->
        <?php if(isset($_SESSION['file_data'])): ?>
        <div class="mapping-section">
            <h2>Map Columns</h2>
            <form action="send_mail.php" method="post">
                <div class="mapping-fields">
                    <label>Email Column:
                        <select name="email_column" required>
                            <?php
                            foreach($_SESSION['headers'] as $index => $header) {
                                echo "<option value='$index'>$header</option>";
                            }
                            ?>
                        </select>
                    </label>
                    <label>Name Column:
                        <select name="name_column">
                            <option value="">None</option>
                            <?php
                            foreach($_SESSION['headers'] as $index => $header) {
                                echo "<option value='$index'>$header</option>";
                            }
                            ?>
                        </select>
                    </label>
                </div>

                <!-- Template Selection -->
                <div class="template-section">
                    <h2>Select Template</h2>
                    <select name="template" required>
                        <?php
                        $templates = glob('templates/*.html');
                        foreach($templates as $template) {
                            $name = basename($template);
                            echo "<option value='$name'>$name</option>";
                        }
                        ?>
                    </select>
                    <a href="templates/manage_templates.php" class="button">Manage Templates</a>
                </div>

                <!-- Sending Pattern -->
                <div class="pattern-section">
                    <h2>Sending Pattern</h2>
                    <label>Interval between emails (minutes):
                        <input type="number" name="interval" min="1" value="5">
                    </label>
                </div>

                <div class="preview-section">
                    <button type="button" class="preview-button" onclick="previewEmail()">Preview Email</button>
                </div>

                <button type="submit" class="send-button">Start Sending</button>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <div id="previewModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Email Preview</h2>
            <div id="previewContent"></div>
        </div>
    </div>

    <script>
    function previewEmail() {
        const formData = new FormData(document.querySelector('.mapping-section form'));
        formData.append('action', 'preview');
        
        fetch('send_mail.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(html => {
            document.getElementById('previewContent').innerHTML = html;
            document.getElementById('previewModal').style.display = 'block';
        });
    }

    // Modal close functionality
    document.querySelector('.close').onclick = function() {
        document.getElementById('previewModal').style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target == document.getElementById('previewModal')) {
            document.getElementById('previewModal').style.display = 'none';
        }
    }
    </script>
</body>
</html> 