<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

if (!isset($SESSION_CHECK_SETTINGS)) {
    $SESSION_CHECK_SETTINGS = array();
}
function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}
if (function_exists('redirectNowOrQuit') !== true) {
    function redirectNowOrQuit($link = NULL)
    {
        // echo "redirecting";
        if ($link == NULL) {
            $actual_link = urlencode("https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");
            if (isset($_GET['unauthorized_redirect'])) {
                $loc = $_GET['unauthorized_redirect'] . "?ref_url=$actual_link";
            } else {
                $loc = "/login?ref_url=$actual_link";
            }
        } else {
            $loc = $link;
        }
        // header("Location: $loc");
        echo "<script>window.location.href='{$loc}'</script>";
?>
        <meta http-equiv="refresh" content="0;URL='<?php echo $loc ?>'" />
<?php
        echo "Access Denied!";
        die();
    }
} // func exists check 

require_once "{$_SERVER['DOCUMENT_ROOT']}/classes/System.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/classes/Account.php";
if (!isset($System)) {
    $System = new System();
}
if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
}
$Login = $System->startClass("Login");
$Account = $Login->logged_in_account_data();

if ($Account == false) {
    if (!isset($SESSION_CHECK_SETTINGS['force_json_response'])) {
        redirectNowOrQuit();
    } else {
        $System->exitJsonResponse(false, "You're not logged in! For your security, you must login to your account again. If you are not automatically redirected, please refresh the page.", array("logout" => true));
    }
} else {
    $User = $System->startClass("User");
    $Account->permissions = $User->get_permissions($Account->id);
}
?>