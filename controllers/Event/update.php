<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "{$_SERVER['DOCUMENT_ROOT']}/classes/System.php";

$System = new System();

if (!isset($_GET['id']) || !$_GET['id'])
    $System->exitJsonResponse(false, "Invalid event id.");

// Get PUT data
$putData = file_get_contents("php://input");
$eventData = json_decode($putData, true);

// Now you can access the task data
$subject = $eventData['subject'];
$start_at = $eventData['start_at'];
$end_at = $eventData['end_at'];
$status = $eventData['status'];
$description = $eventData['description'];

$Event = $System->startClass("Event");

$Event->updateField($_GET['id'], 'subject', $subject);
$Event->updateField($_GET['id'], 'start_at', $start_at);
$Event->updateField($_GET['id'], 'end_at', $end_at);
$Event->updateField($_GET['id'], 'status', $status);
$Event->updateField($_GET['id'], 'description', $description);

$System->exitJsonResponse(true, "Successfully updated.", [
    'id' => $_GET['id'],
    'subject' => $subject,
    'start_at' => $start_at,
    'end_at' => $end_at,
    'status' => $status,
    'description' => $description,
]);
