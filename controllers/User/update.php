<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "{$_SERVER['DOCUMENT_ROOT']}/classes/System.php";

$System = new System();

if (!isset($_POST['id']) || !$_POST['id'])
    $System->exitJsonResponse(false, "Please log in.");

$User = $System->startClass("User");
$user = $User->info($_POST['id']);

if (isset($_POST['firstName']) && $_POST['firstName'])
    $User->updateField($_POST['id'], 'first_name', $_POST['firstName']);

if (isset($_POST['lastName']) && $_POST['lastName'])
    $User->updateField($_POST['id'], 'last_name', $_POST['lastName']);

if (isset($_POST['email']) && $_POST['email']) {
    $email_user = $User->fetch_by_email($_POST['email']);
    if ($email_user && $email_user['id'] != $_POST['id']) {
        $System->exitJsonResponse(false, "That email is already in use.");
    } else {
        $User->updateField($_POST['id'], 'email', $_POST['email']);
    }
}

if (isset($_POST['birthDate']))
    $User->updateField($_POST['id'], 'birth_date', $_POST['birthDate']);
if (isset($_POST['ptoStartDate']))
    $User->updateField($_POST['id'], 'pto_start_date', $_POST['ptoStartDate']);
if (isset($_POST['ptoEndDate']))
    $User->updateField($_POST['id'], 'pto_end_date', $_POST['ptoEndDate']);


if (isset($_POST['address']))
    $User->updateField($_POST['id'], 'address', $_POST['address']);

if (isset($_POST['city']))
    $User->updateField($_POST['id'], 'city', $_POST['city']);

if (isset($_POST['zipCode']))
    $User->updateField($_POST['id'], 'zip_code', $_POST['zipCode']);

if (isset($_POST['state']))
    $User->updateField($_POST['id'], 'state', $_POST['state']);

if (isset($_POST['bio']))
    $User->updateField($_POST['id'], 'bio', $_POST['bio']);


if (isset($_POST['phone']))
    $User->updateField($_POST['id'], 'phone', $_POST['phone']);


$System->exitJsonResponse(true, "Successfully updated.");
