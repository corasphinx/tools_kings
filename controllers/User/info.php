<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "{$_SERVER['DOCUMENT_ROOT']}/classes/System.php";

$System = new System();

if (!isset($_POST['id']) || !$_POST['id'])
    $System->exitJsonResponse(false, "Invalid user id.");

$User = $System->startClass("User");

$UserDocument = $System->startClass("UserDocument");
$user = $User->info($_POST['id']);
$user['documents'] = $UserDocument->fetch_by_user_id($_POST['id']);

$System->exitJsonResponse(true, "Successfully fetched.", $user);
