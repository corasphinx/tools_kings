<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "{$_SERVER['DOCUMENT_ROOT']}/classes/System.php";

$System = new System();

$User = $System->startClass("User");

if (!isset($_POST['first_name']) || !$_POST['first_name'])
    $System->exitJsonResponse(false, "Please provide first name.");

if (!isset($_POST['last_name']) || !$_POST['last_name'])
    $System->exitJsonResponse(false, "Please provide last name.");

if (!isset($_POST['email']) || !$_POST['email'])
    $System->exitJsonResponse(false, "Please provide email.");

if (isset($_POST['id']) && $_POST['id']){
    // update
    $User->updateField($_POST['id'], 'first_name', $_POST['first_name']);
    $User->updateField($_POST['id'], 'last_name', $_POST['last_name']);
    $User->updateField($_POST['id'], 'email', $_POST['email']);
    $User->updateField($_POST['id'], 'email', $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $User->updateField($_POST['id'], 'password', $password);
    $System->exitJsonResponse(true, "Successfully updated.");
}else{
    // create
    $User->create($_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['password']);
    $System->exitJsonResponse(true, "Successfully created.");
}
