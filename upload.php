<?php
session_start();
require 'vendor/autoload.php'; // For Excel file handling

if ($_FILES['file']['error'] == 0) {
    $filename = $_FILES['file']['name'];
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    $target_path = "uploads/" . time() . "_" . $filename;
    
    move_uploaded_file($_FILES['file']['tmp_name'], $target_path);
    
    // Read file data
    if ($ext == 'csv') {
        $file = fopen($target_path, 'r');
        $_SESSION['headers'] = fgetcsv($file);
        $_SESSION['file_data'] = array();
        while ($row = fgetcsv($file)) {
            $_SESSION['file_data'][] = $row;
        }
        fclose($file);
    } else {
        // Handle Excel files using PhpSpreadsheet
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($target_path);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        $_SESSION['headers'] = array_shift($rows);
        $_SESSION['file_data'] = $rows;
    }
    
    header('Location: index.php');
} else {
    echo "Error uploading file";
} 