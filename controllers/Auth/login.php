<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Access-Control-Allow-Origin: *');

require_once "{$_SERVER['DOCUMENT_ROOT']}/classes/System.php";

$System = new System();

if (isset($_POST['email']) && isset($_POST['password'])) {
    require_once "{$_SERVER['DOCUMENT_ROOT']}/classes/Login.php";
    $Login = new Login();
    if ($Login->valid !== true) {
        // db connection error  ~~ this should never happen 
        $System->exitJsonResponse(false, "Error connecting to the database. Please try again. ERR 10112");
    }

    $email = $_POST['email'];
    $password = $_POST['password'];
    $try = $Login->tryLogin($email, $password);
    if ($try !== false) { // valid login:
        // $try = account id! 
        require_once "{$_SERVER['DOCUMENT_ROOT']}/classes/Account.php";
        $Account = new Account($try);
        if ($Account->valid !== false) { // make sure account class is constructed correctly:

            if ($Account->startSession()) {

                // Start the session if it's not already started
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                // Store the username or any other user details in session
                $_SESSION['username'] = $Account->user_name;  // Assuming $Account->user_name holds the username
                $_SESSION['email'] = $Account->email;         // Assuming $Account->email holds the email
                $redirect = '/';

                // Return response with the redirect and account information
                $System->exitJsonResponse(true, "You are now logged in!", array("redirect" => $redirect));
            } else {
                $System->exitJsonResponse(false, "We could not start a session!");
            }
        } else {
            $System->exitJsonResponse(false, "Unable to correctly log you in. Please try again!");
        }
    } else {
        $System->exitJsonResponse(false, "Login credentials are not correct!");
    }
} else {
    $System->exitJsonResponse(false, "You must provide your name or email, as well as your valid password!", $_POST);
}
