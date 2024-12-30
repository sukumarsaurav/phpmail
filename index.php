<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Bulk Email Sender</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="main-title"><i class="fas fa-envelope"></i> Bulk Email Sender</h1>
        
        <!-- File Upload Form -->
        <div class="card upload-section">
            <h2><i class="fas fa-file-upload"></i> Upload Email List</h2>
            <form action="upload.php" method="post" enctype="multipart/form-data">
                <div class="file-input-container">
                    <input type="file" name="file" id="file" accept=".csv,.xlsx,.xls" required>
                    <label for="file" class="file-label">
                        <i class="fas fa-cloud-upload-alt"></i> Choose File
                    </label>
                    <span id="file-name">No file chosen</span>
                </div>
                <button type="submit" class="upload-button"><i class="fas fa-upload"></i> Upload</button>
            </form>
        </div>

        <!-- Previous Uploads -->
        <div class="card previous-uploads">
            <h2><i class="fas fa-history"></i> Previous Uploads</h2>
            <div class="uploads-grid">
                <?php
                $uploads = glob('uploads/*_{*.csv,*.xlsx,*.xls}', GLOB_BRACE);
                foreach($uploads as $upload) {
                    $filename = basename($upload);
                    $timestamp = strtotime(explode('_', $filename)[0]);
                    $original_name = implode('_', array_slice(explode('_', $filename), 1));
                    echo "<div class='upload-card'>";
                    echo "<div class='upload-icon'><i class='fas fa-file-alt'></i></div>";
                    echo "<div class='upload-details'>";
                    echo "<h3>$original_name</h3>";
                    echo "<p>Uploaded: " . date('Y-m-d H:i', $timestamp) . "</p>";
                    echo "<button class='use-file' onclick='useFile(\"$filename\")'>Use This File</button>";
                    echo "</div>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>

        <!-- Column Mapping Section -->
        <?php if(isset($_SESSION['file_data'])): ?>
        <div class="card mapping-section">
            <h2><i class="fas fa-columns"></i> Map Columns</h2>
            <form action="send_mail.php" method="post" id="emailForm">
                <div class="mapping-fields">
                    <div class="field-group">
                        <label>
                            <i class="fas fa-envelope"></i> Email Column:
                            <select name="email_column" required>
                                <?php
                                foreach($_SESSION['headers'] as $index => $header) {
                                    echo "<option value='$index'>$header</option>";
                                }
                                ?>
                            </select>
                        </label>
                    </div>
                    <div class="field-group">
                        <label>
                            <i class="fas fa-user"></i> Name Column:
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
                </div>

                <!-- Template Selection -->
                <div class="template-section">
                    <h2><i class="fas fa-file-code"></i> Email Template</h2>
                    <div class="template-grid">
                        <?php
                        $templates = glob('templates/*.html');
                        foreach($templates as $template) {
                            $name = basename($template);
                            $content = file_get_contents($template);
                            echo "<div class='template-card' onclick='selectTemplate(this, \"$name\")'>";
                            echo "<div class='template-preview'>" . substr(strip_tags($content), 0, 100) . "...</div>";
                            echo "<div class='template-name'>$name</div>";
                            echo "</div>";
                        }
                        ?>
                        <div class="template-card add-template" onclick="showNewTemplateModal()">
                            <i class="fas fa-plus"></i>
                            <div>Add New Template</div>
                        </div>
                    </div>
                    <input type="hidden" name="template" id="selected-template" required>
                </div>

                <!-- Sending Pattern -->
                <div class="pattern-section">
                    <h2><i class="fas fa-clock"></i> Sending Pattern</h2>
                    <div class="field-group">
                        <label>
                            <i class="fas fa-hourglass-half"></i> Interval between emails (minutes):
                            <input type="number" name="interval" min="1" value="5">
                        </label>
                    </div>
                </div>

                <div class="action-buttons">
                    <button type="button" class="preview-button" onclick="previewEmail()">
                        <i class="fas fa-eye"></i> Preview
                    </button>
                    <button type="submit" class="send-button">
                        <i class="fas fa-paper-plane"></i> Start Sending
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <!-- Preview Modal -->
    <div id="previewModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2><i class="fas fa-eye"></i> Email Preview</h2>
            <div id="previewContent"></div>
        </div>
    </div>

    <!-- New Template Modal -->
    <div id="newTemplateModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeNewTemplateModal()">&times;</span>
            <h2><i class="fas fa-file-code"></i> Create New Template</h2>
            <form action="templates/manage_templates.php" method="post" class="template-form">
                <div class="field-group">
                    <label>Template Name:
                        <input type="text" name="template_name" required>
                    </label>
                </div>
                <div class="field-group">
                    <label>Content:
                        <textarea name="template_content" rows="10" required></textarea>
                    </label>
                </div>
                <div class="template-variables">
                    <p>Available variables: {name}, {email}</p>
                </div>
                <button type="submit" class="save-template-button">
                    <i class="fas fa-save"></i> Save Template
                </button>
            </form>
        </div>
    </div>

    <script>
    // File input handling
    document.getElementById('file').addEventListener('change', function(e) {
        document.getElementById('file-name').textContent = e.target.files[0].name;
    });

    // Template selection
    function selectTemplate(element, templateName) {
        document.querySelectorAll('.template-card').forEach(card => {
            card.classList.remove('selected');
        });
        element.classList.add('selected');
        document.getElementById('selected-template').value = templateName;
    }

    // New template modal
    function showNewTemplateModal() {
        document.getElementById('newTemplateModal').style.display = 'block';
    }

    function closeNewTemplateModal() {
        document.getElementById('newTemplateModal').style.display = 'none';
    }

    // Preview functionality
    function previewEmail() {
        const formData = new FormData(document.getElementById('emailForm'));
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
    document.querySelectorAll('.close').forEach(closeBtn => {
        closeBtn.onclick = function() {
            this.closest('.modal').style.display = 'none';
        }
    });

    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }

    // Use previous file
    function useFile(filename) {
        fetch('upload.php?use_file=' + encodeURIComponent(filename))
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    window.location.reload();
                }
            });
    }
    </script>
</body>
</html> 