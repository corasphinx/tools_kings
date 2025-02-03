<?php
// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "{$_SERVER['DOCUMENT_ROOT']}/classes/System.php";

$System = new System();

require_once "{$_SERVER['DOCUMENT_ROOT']}/controllers/Auth/get_login_account.php";
$created_by = $Account->id;

$UserDocument = $System->startClass("UserDocument");

try {
    // Define upload directory
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/upload/user_documents/';
    $results = [];

    // Check if files were uploaded
    if (!isset($_FILES['file'])) {
        throw new Exception('No files uploaded');
    }

    // Handle multiple files
    $files = $_FILES['file'];

    // Reformat files array if multiple files
    $fileCount = is_array($files['name']) ? count($files['name']) : 1;

    for ($i = 0; $i < $fileCount; $i++) {
        $currentFile = [
            'name' => is_array($files['name']) ? $files['name'][$i] : $files['name'],
            'type' => is_array($files['type']) ? $files['type'][$i] : $files['type'],
            'tmp_name' => is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'],
            'error' => is_array($files['error']) ? $files['error'][$i] : $files['error'],
            'size' => is_array($files['size']) ? $files['size'][$i] : $files['size']
        ];

        // Basic error checking
        if ($currentFile['error'] !== UPLOAD_ERR_OK) {
            $results[] = [
                'status' => 'error',
                'originalName' => $currentFile['name'],
                'message' => 'File upload error: ' . $currentFile['error']
            ];
            continue;
        }

        // Validate file size (e.g., 5MB max)
        $maxFileSize = 5 * 1024 * 1024; // 5MB in bytes
        if ($currentFile['size'] > $maxFileSize) {
            $results[] = [
                'status' => 'error',
                'originalName' => $currentFile['name'],
                'message' => 'File size exceeds limit'
            ];
            continue;
        }

        // Get file extension
        $fileExtension = strtolower(pathinfo($currentFile['name'], PATHINFO_EXTENSION));

        // Generate unique filename
        $newFilename = uniqid() . '_' . date('Ymd_His') . '.' . $fileExtension;
        $uploadPath = $uploadDir . $newFilename;

        // Move uploaded file
        if (!move_uploaded_file($currentFile['tmp_name'], $uploadPath)) {
            $results[] = [
                'status' => 'error',
                'originalName' => $currentFile['name'],
                'message' => 'Failed to move uploaded file'
            ];
            continue;
        }

        $path = $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["SERVER_NAME"] . '/upload/user_documents/' . $newFilename;
        $UserDocument->create(
            $_POST['user_id'],
            $currentFile['name'],
            $newFilename,
            $path,
            $created_by
        );

        // Add success result
        $results[] = [
            'status' => 'success',
            'message' => 'File uploaded successfully',
            'originalName' => $currentFile['name'],
            'filename' => $newFilename,
            'path' => $path
        ];
    }

    // Return response with all results
    echo json_encode([
        'status' => 'completed',
        'files' => $results
    ]);
    $System->exitJsonResponse(true, 'Uploaded successfully', $results);
} catch (Exception $e) {
    $System->exitJsonResponse(false, $e->getMessage());
}
