<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "{$_SERVER['DOCUMENT_ROOT']}/classes/System.php";

$System = new System();
$Role = $System->startClass("Role");
$roles = $System->fetch_all_rows('roles');
foreach ($roles as $key => $role) {
    $roles[$key]['permissions'] = $Role->get_permissions($role['id']);
}
$System->exitJsonResponse(true, "Successfully fetched all roles.", $roles);
