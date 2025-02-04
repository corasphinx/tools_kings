<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "{$_SERVER['DOCUMENT_ROOT']}/classes/System.php";

$System = new System();
require_once "{$_SERVER['DOCUMENT_ROOT']}/controllers/Auth/get_login_account.php";

$PTO = $System->startClass("PTO");

$ptos = $PTO->fetch_by_user_id($_POST['user_id']);

$System->exitJsonResponse(true, "Successfully fetched PTO data.", $ptos);
