<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "{$_SERVER['DOCUMENT_ROOT']}/classes/System.php";

$System = new System();
require_once "{$_SERVER['DOCUMENT_ROOT']}/controllers/Auth/get_login_account.php";
$_POST['created_by'] = $Account->id;

$Permission = $System->startClass("Permission");

if ($_POST['id']) {
    $System->updateTableField('permissions', $_POST['id'], 'name', $_POST['name']);
    $System->updateTableField('permissions', $_POST['id'], 'description', $_POST['description']);
    $System->exitJsonResponse(true, "Successfully updated the permission.");
} else {
    $System->exitJsonResponse(true, "Successfully created a new permission.", $Permission->create($_POST));
}
