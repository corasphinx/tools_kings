<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "{$_SERVER['DOCUMENT_ROOT']}/classes/System.php";

$System = new System();
require_once "{$_SERVER['DOCUMENT_ROOT']}/controllers/Auth/get_login_account.php";
$_POST['created_by'] = $Account->id;

$Role = $System->startClass("Role");


if ($_POST['id']) {
    $System->updateTableField('roles', $_POST['id'], 'name', $_POST['name']);
    $role_id = $_POST['id'];
} else {
    $role_id = $Role->create($_POST);
}

$permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];
$Role->wipe_permissions($role_id);
if (is_string($permissions)) {
    $permissions = explode(',', $permissions);
}
if (!empty($permissions)) {
    foreach ($permissions as $permissionId) {
        $Role->assign_permission($role_id, $permissionId);
    }
}

$System->exitJsonResponse(true, "Successfully saved the role.", $role_id);
