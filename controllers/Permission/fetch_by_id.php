<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "{$_SERVER['DOCUMENT_ROOT']}/classes/System.php";

$System = new System();

$Permission = $System->startClass("Permission");

$System->exitJsonResponse(true, "Successfully fetched the permission.", $Permission->info($_GET['id']));
