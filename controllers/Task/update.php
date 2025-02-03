<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "{$_SERVER['DOCUMENT_ROOT']}/classes/System.php";

$System = new System();

if (!isset($_GET['id']) || !$_GET['id'])
    $System->exitJsonResponse(false, "Invalid task id.");

// Get PUT data
$putData = file_get_contents("php://input");
$taskData = json_decode($putData, true);

// Now you can access the task data
$subject = $taskData['subject'];
$description = $taskData['description'];
$due_by = $taskData['due_by'];

$Task = $System->startClass("Task");

$Task->updateField($_GET['id'], 'subject', $subject);
$Task->updateField($_GET['id'], 'description', $description);
$Task->updateField($_GET['id'], 'due_by', $due_by);

$System->exitJsonResponse(true, "Successfully updated.", [
    'id' => $_GET['id'],
    'subject' => $subject,
    'description' => $description,
    'due_by' => $due_by,
]);
