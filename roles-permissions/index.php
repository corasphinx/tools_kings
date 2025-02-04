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
    <div class="card mt-4">
        <div class="card-header">
            <!-- Tab Navigation -->
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="roles-tab" data-bs-toggle="tab" data-bs-target="#roles-panel"
                        type="button" role="tab" aria-controls="roles-panel" aria-selected="true">
                        Roles
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="permissions-tab" data-bs-toggle="tab" data-bs-target="#permissions-panel"
                        type="button" role="tab" aria-controls="permissions-panel" aria-selected="false">
                        Permissions
                    </button>
                </li>
            </ul>
        </div>

        <!-- Tab Content -->
        <div class="card-body">
            <div class="tab-content">
                <!-- Roles Tab -->
                <div class="tab-pane fade show active" id="roles-panel" role="tabpanel" aria-labelledby="roles-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Roles</h6>
                        <button class="btn btn-primary btn-sm" id="addRoleBtn">
                            Add Role
                        </button>
                    </div>
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

                <!-- Permissions Tab -->
                <div class="tab-pane fade" id="permissions-panel" role="tabpanel" aria-labelledby="permissions-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Permissions</h6>
                        <button class="btn btn-primary btn-sm" id="addPermissionBtn">
                            Add Permission
                        </button>
                    </div>
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