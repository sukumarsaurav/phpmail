<?php
session_start();
require 'vendor/autoload.php'; // For Excel file handling

// Handle request to use previous file
if (isset($_GET['use_file'])) {
    $filename = $_GET['use_file'];
    $filepath = "uploads/" . $filename;
    
    if (file_exists($filepath)) {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        
        try {
            if ($ext == 'csv') {
                $file = fopen($filepath, 'r');
                $_SESSION['headers'] = fgetcsv($file);
                $_SESSION['file_data'] = array();
                while ($row = fgetcsv($file)) {
                    $_SESSION['file_data'][] = $row;
                }
                fclose($file);
            } else {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filepath);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();
                $_SESSION['headers'] = array_shift($rows);
                $_SESSION['file_data'] = $rows;
            }
            
            echo json_encode(['success' => true]);
            exit;
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }
}

// Handle new file upload
if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
    try {
        $filename = $_FILES['file']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $target_path = "uploads/" . time() . "_" . $filename;
        
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }
        
        if (move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
            if ($ext == 'csv') {
                $file = fopen($target_path, 'r');
                $_SESSION['headers'] = fgetcsv($file);
                $_SESSION['file_data'] = array();
                while ($row = fgetcsv($file)) {
                    $_SESSION['file_data'][] = $row;
                }
                fclose($file);
            } else {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($target_path);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();
                $_SESSION['headers'] = array_shift($rows);
                $_SESSION['file_data'] = $rows;
            }
            
            header('Location: index.php');
            exit;
        } else {
            throw new Exception("Failed to move uploaded file");
        }
    } catch (Exception $e) {
        error_log("Upload error: " . $e->getMessage());
        echo "Error uploading file: " . $e->getMessage();
        exit;
    }
} else {
    echo "No file uploaded or error in upload";
    exit;
} 