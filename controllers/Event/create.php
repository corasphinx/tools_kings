<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "{$_SERVER['DOCUMENT_ROOT']}/classes/System.php";

$System = new System();
require_once "{$_SERVER['DOCUMENT_ROOT']}/controllers/Auth/get_login_account.php";
$_POST['created_by'] = $Account->id;

$Event = $System->startClass("Event");
$_POST['id'] = $Event->create($_POST);
$System->exitJsonResponse(true, "Successfully created a new event.", $_POST);
