<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "{$_SERVER['DOCUMENT_ROOT']}/classes/System.php";

$System = new System();

require_once "{$_SERVER['DOCUMENT_ROOT']}/controllers/Auth/get_login_account.php";
$created_by = $Account->id;

$Event = $System->startClass("Event");

$System->exitJsonResponse(true, "Successfully fetched.", $Event->fetch_by_user_id($created_by));
