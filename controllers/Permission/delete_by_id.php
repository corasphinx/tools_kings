<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "{$_SERVER['DOCUMENT_ROOT']}/classes/System.php";

$System = new System();

$System->exitJsonResponse(true, "Successfully deleted the permission.", $System->delete_row('permissions', $_GET['id']));
