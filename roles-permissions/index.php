<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "{$_SERVER['DOCUMENT_ROOT']}/controllers/Auth/user_session_check.php";
require_once("{$_SERVER['DOCUMENT_ROOT']}/inc/header.php");
?>
<!-- FullCalendar CSS -->
<link href="./style.css" rel="stylesheet">
<div class="container-fluid py-4">
    <div class="row mt-4">
        <div class="col-md-6 col-sm-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>Roles</h6>
                    <button class="btn btn-primary btn-sm float-end" id="addRoleBtn">
                        Add Role
                    </button>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0" id="rolesTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Name</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Permissions</th>
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
                    <h6>Permissions</h6>
                    <button class="btn btn-primary btn-sm float-end" id="addPermissionBtn">
                        Add Permission
                    </button>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0" id="permissionsTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Name</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Description</th>
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
    </div>

</div>

<?php require_once("{$_SERVER['DOCUMENT_ROOT']}/inc/footer.php"); ?>
<?php require_once("{$_SERVER['DOCUMENT_ROOT']}/components/edit-role-modal.php"); ?>
<?php require_once("{$_SERVER['DOCUMENT_ROOT']}/components/edit-permission-modal.php"); ?>

<script src="./script.js?v=1"></script>