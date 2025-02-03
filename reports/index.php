<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once("{$_SERVER['DOCUMENT_ROOT']}/inc/header.php");
?>
<link href="./style.css" rel="stylesheet">
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12 text-end">
            <button class="btn btn-success" id="uploadBtn"><i class="bi bi-arrow-up"></i> Upload Today TXT</button>
            <input type="file" id="fileInput" accept=".txt" style="display: none;">
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="card mb-4">
                <!-- Card Header with collapse button -->
                <div class="card-header pb-0" data-bs-toggle="collapse" data-bs-target="#reportsContainer" 
                    aria-expanded="true" aria-controls="reportsContainer" style="cursor: pointer;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Parse result</h6>
                        <i class="bi bi-chevron-down"></i> <!-- Bootstrap Icon -->
                    </div>
                </div>

                <!-- Collapsible Card Body -->
                <div id="reportsContainer" class="collapse show">
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0" id="reportsTable">
                                <thead>
                                    <tr>
                                        <th class="text-secondary font-weight-bolder opacity-7">Date</th>
                                        <th class="text-secondary font-weight-bolder opacity-7">Lines(#)</th>
                                        <th class="text-secondary font-weight-bolder opacity-7">Projects(#)</th>
                                        <th class="text-secondary font-weight-bolder opacity-7">Employees(#)</th>
                                        <th class="text-secondary font-weight-bolder opacity-7">Actions(#)</th>
                                        <th class="text-secondary font-weight-bolder opacity-7"></th>
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
<script src="./script.js?v=5"></script>