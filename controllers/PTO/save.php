<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "{$_SERVER['DOCUMENT_ROOT']}/classes/System.php";

$System = new System();
require_once "{$_SERVER['DOCUMENT_ROOT']}/controllers/Auth/get_login_account.php";

$PTO = $System->startClass("PTO");

$PTO->wipe_user_pto($_POST['user_id']);
if ($_POST['pto_data']) {
    foreach ($_POST['pto_data'] as $pto) {
        $PTO->create([
            'user_id' => $_POST['user_id'],
            'amount' => $pto['amount'],
            'time_off' => $pto['time_off'],
            'created_by' => $Account->id
        ]);
    }
}

$System->exitJsonResponse(true, "Successfully updated PTO data.");
