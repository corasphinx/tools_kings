<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "{$_SERVER['DOCUMENT_ROOT']}/classes/System.php";

$System = new System();

$Role = $System->startClass("Role");
$role = $Role->info($_GET['id']);
$role['permissions'] = $Role->get_permissions($_GET['id']);
$System->exitJsonResponse(true, "Successfully fetched the role.", $role);
