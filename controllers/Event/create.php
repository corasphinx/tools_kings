<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "{$_SERVER['DOCUMENT_ROOT']}/classes/System.php";

$System = new System();

$Event = $System->startClass("Event");
if (isset($_POST['assigneees']) && $_POST['assigneees']) {
    foreach ($_POST['assigneees'] as $key => $user_id) {
        $_POST['created_by'] = $user_id;
        $Event->create($_POST);
    }
} else {
    $Event->create($_POST);
}

$System->exitJsonResponse(true, "Successfully created a new event.", $_POST);
