<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "{$_SERVER['DOCUMENT_ROOT']}/classes/System.php";

$System = new System();

$Report = $System->startClass("Report");
$report_info = $Report->info($_POST['report_id']);
$lines = $Report->fetch_report_lines($_POST['report_id']);
$System->exitJsonResponse(true, "Successfully fetched.", [
    'reportInfo' => $report_info,
    'lines' => $lines,
]);
