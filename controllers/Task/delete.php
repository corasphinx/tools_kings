<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "{$_SERVER['DOCUMENT_ROOT']}/classes/System.php";

$System = new System();

if (!isset($_GET['id']) || !$_GET['id'])
    $System->exitJsonResponse(false, "Invalid task id.");

$Task = $System->startClass("Task");

$System->exitJsonResponse(true, "Successfully fetched.", $Task->delete_task($_GET['id']));
