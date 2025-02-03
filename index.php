<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "{$_SERVER['DOCUMENT_ROOT']}/controllers/Auth/user_session_check.php";
require_once("{$_SERVER['DOCUMENT_ROOT']}/inc/header.php");
?>
<!-- FullCalendar CSS -->
<link href='/assets/vendor/fullcalendar@5.11.3/main.css' rel='stylesheet' />
<link href="./style.css" rel="stylesheet">
<div class="container-fluid py-4">
    <div class="row mt-4">
        <div class="col-md-6 col-sm-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>To Do List</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0" id="usersTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Task</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>Upcoming PTO</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0" id="usersTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Amount</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Time Off</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="card mb-4">
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="calendar-wrapper">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php require_once("{$_SERVER['DOCUMENT_ROOT']}/inc/footer.php"); ?>
<?php include "{$_SERVER['DOCUMENT_ROOT']}/components/edit-user-modal.php" ?>

<script src='/assets/vendor/fullcalendar@5.11.3/main.js'></script>
<script src="./script.js?v=7"></script>