<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "{$_SERVER['DOCUMENT_ROOT']}/classes/System.php";

$System = new System();
require_once "{$_SERVER['DOCUMENT_ROOT']}/controllers/Auth/get_login_account.php";
$_POST['created_by'] = $Account->id;

$Task = $System->startClass("Task");
$_POST['id'] = $Task->create($_POST);
$System->exitJsonResponse(true, "Successfully created a new task.", $_POST);
