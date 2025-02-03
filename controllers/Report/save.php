<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "{$_SERVER['DOCUMENT_ROOT']}/classes/System.php";

$System = new System();

$Report = $System->startClass("Report");
$Report->wipe_report_by_date(date('Y-m-d'));
$report_id = $Report->create($_POST);
$Report->create_lines($report_id, json_decode($_POST['parsedData'], true));
$System->exitJsonResponse(true, "Successfully generated a new report.");
