<?php
session_start();
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email_column = $_POST['email_column'];
    $name_column = $_POST['name_column'];
    $template = $_POST['template'];
    $interval = $_POST['interval'];
    
    // Read template
    $template_content = file_get_contents("templates/$template");
    
    // Handle preview request
    if (isset($_POST['action']) && $_POST['action'] == 'preview') {
        // Get first row of data for preview
        $first_row = $_SESSION['file_data'][0];
        $preview_email = $first_row[$email_column];
        $preview_name = $name_column ? $first_row[$name_column] : '';
        
        $message = $template_content;
        if ($preview_name) {
            $message = str_replace('{name}', $preview_name, $message);
        }
        $message = str_replace('{email}', $preview_email, $message);
        
        echo "<div class='preview-details'>";
        echo "<p><strong>To:</strong> $preview_email</p>";
        echo "<p><strong>Subject:</strong> Your Subject</p>";
        echo "<hr>";
        echo "<div class='preview-body'>$message</div>";
        echo "</div>";
        exit;
    }
    
    // Configure PHP mailer
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'localhost';
    $mail->Port = 25;
    
    // Create progress file
    $progress_id = uniqid();
    $progress_file = "progress_{$progress_id}.json";
    $total_emails = count($_SESSION['file_data']);
    
    $progress_data = [
        'total' => $total_emails,
        'current' => 0,
        'success' => 0,
        'failed' => 0,
        'status' => 'running'
    ];
    
    file_put_contents("uploads/$progress_file", json_encode($progress_data));
    
    // Return progress ID to client
    if (!headers_sent()) {
        header('Content-Type: application/json');
        echo json_encode(['progress_id' => $progress_id]);
        flush();
    }
    
    foreach ($_SESSION['file_data'] as $index => $row) {
        $email = $row[$email_column];
        $name = $name_column ? $row[$name_column] : '';
        
        // Replace variables in template
        $message = $template_content;
        if ($name) {
            $message = str_replace('{name}', $name, $message);
        }
        $message = str_replace('{email}', $email, $message);
        
        // Send email
        try {
            $mail->clearAddresses();
            $mail->addAddress($email);
            $mail->Subject = 'Your Subject';
            $mail->Body = $message;
            $mail->send();
            
            $progress_data['success']++;
        } catch (Exception $e) {
            $progress_data['failed']++;
        }
        
        $progress_data['current']++;
        file_put_contents("uploads/$progress_file", json_encode($progress_data));
        
        // Wait for specified interval
        if ($index < count($_SESSION['file_data']) - 1) {
            sleep($interval * 60);
        }
    }
    
    $progress_data['status'] = 'completed';
    file_put_contents("uploads/$progress_file", json_encode($progress_data));
    exit;
}

// Handle progress check requests
if (isset($_GET['check_progress'])) {
    $progress_id = $_GET['check_progress'];
    $progress_file = "uploads/progress_{$progress_id}.json";
    
    if (file_exists($progress_file)) {
        header('Content-Type: application/json');
        echo file_get_contents($progress_file);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Progress file not found']);
    }
    exit;
} 