<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="./assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="/assets/img/favicon.png">
    <title>
        Tools Kings Global Consulting
    </title>
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link href="/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="/assets/vendor/simple-datatables/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- CSS Files -->
    <link id="pagestyle" href="/assets/css/argon-dashboard.css?v=5" rel="stylesheet" />
</head>

<body class="g-sidenav-show   bg-gray-100">
    <div class="min-height-300 bg-dark position-absolute w-100"></div>
    <?php
    // Get current URL path
    $current_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    // Check if the current path is NOT login
    if ($current_path !== '/login/') {
        require_once("{$_SERVER['DOCUMENT_ROOT']}/inc/sidenav.php");
    }
    ?>
    <main class="main-content position-relative border-radius-lg ">
        <?php
        require_once("{$_SERVER['DOCUMENT_ROOT']}/inc/topnav.php");
        ?>