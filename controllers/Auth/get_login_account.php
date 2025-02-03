<?php
$Login = $System->startClass("Login");
$Account = $Login->logged_in_account_data();
if (!$Account)
    $System->exitJsonResponse(false, "This is an invalid account. Please reach out to support. ERR 33991.");
