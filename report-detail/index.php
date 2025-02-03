<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once("{$_SERVER['DOCUMENT_ROOT']}/inc/header.php");
?>
<link href="./style.css" rel="stylesheet">
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Today's Entries</p>
                                <h5 class="font-weight-bolder" id="totalEntryCount">
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                <i class="bi bi-list-check text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Today's Employees</p>
                                <h5 class="font-weight-bolder" id="totalEmployeeCount">
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-danger shadow-danger text-center rounded-circle">
                                <i class="bi bi-people text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Today's Projects</p>
                                <h5 class="font-weight-bolder" id="totalProjectCount">
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow-danger text-center rounded-circle">
                                <i class="bi bi-pass text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Today's Actions</p>
                                <h5 class="font-weight-bolder" id="totalActionCount">
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow-danger text-center rounded-circle">
                                <i class="bi bi-rocket text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-6 col-sm-12">
            <div class="card z-index-2 h-100">
                <div class="card-header pb-0 pt-3 bg-transparent">
                <h6 class="text-capitalize">Hourly Performance</h6>
                <p class="text-sm mb-0">
                    <i class="fa fa-arrow-up text-success"></i>
                    <span class="font-weight-bold"><?= date('Y-m-d') ?></span>
                </p>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="hourlyChart" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <div class="card z-index-2 h-100">
                <div class="card-header pb-0 pt-3 bg-transparent">
                <h6 class="text-capitalize">Breakdown by Project</h6>
                <p class="text-sm mb-0">
                    <i class="fa fa-arrow-up text-success"></i>
                    <span class="font-weight-bold"><?= date('Y-m-d') ?></span>
                </p>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="projectChart" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card mb-4">
                <!-- Card Header with collapse button -->
                <div class="card-header pb-0 collapsed" data-bs-toggle="collapse" data-bs-target="#parseResultBody" 
                    aria-expanded="true" aria-controls="parseResultBody" style="cursor: pointer;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Parse result</h6>
                        <i class="bi bi-chevron-down"></i> <!-- Bootstrap Icon -->
                    </div>
                </div>

                <!-- Collapsible Card Body -->
                <div id="parseResultBody" class="collapse">
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0" id="resultContainer">
                                <thead>
                                    <tr>
                                        <th class="text-secondary font-weight-bolder opacity-7">#</th>
                                        <th class="text-secondary font-weight-bolder opacity-7">Time</th>
                                        <th class="text-secondary font-weight-bolder opacity-7">Project</th>
                                        <th class="text-secondary font-weight-bolder opacity-7">Employee</th>
                                        <th class="text-secondary font-weight-bolder opacity-7">Action</th>
                                        <th class="text-secondary font-weight-bolder opacity-7">Description</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php require_once("{$_SERVER['DOCUMENT_ROOT']}/inc/footer.php"); ?>
<script src="./script.js?v=2"></script>