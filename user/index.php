<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "{$_SERVER['DOCUMENT_ROOT']}/controllers/Auth/user_session_check.php";
require_once("{$_SERVER['DOCUMENT_ROOT']}/inc/header.php");
?>
<link href="./style.css?v=2" rel="stylesheet">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header">
                    <h4 class="mb-0">User Profile</h4>
                </div>
                <div class="card-body">
                    <!-- Tabs -->
                    <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="general-tab" data-bs-toggle="tab"
                                data-bs-target="#general" type="button" role="tab"
                                aria-controls="general" aria-selected="true">General</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="attachments-tab" data-bs-toggle="tab"
                                data-bs-target="#attachments" type="button" role="tab"
                                aria-controls="attachments" aria-selected="false">Attachments</button>
                        </li>
                    </ul>

                    <!-- Tab Contents -->
                    <div class="tab-content" id="profileTabsContent">
                        <!-- General Tab -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel"
                            aria-labelledby="general-tab">
                            <form id="profileForm">
                                <!-- Personal Information -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="firstName" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="firstName" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="lastName" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="lastName" required>
                                    </div>
                                </div>

                                <!-- Contact Information -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone">
                                    </div>
                                </div>

                                <!-- Address -->
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="address">
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="city" class="form-label">City</label>
                                        <input type="text" class="form-control" id="city">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="state" class="form-label">State</label>
                                        <input type="text" class="form-control" id="state">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="zipCode" class="form-label">Zip Code</label>
                                        <input type="text" class="form-control" id="zipCode">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="birthDate" class="form-label">Birth Date</label>
                                        <input type="date" class="form-control" id="birthDate">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="ptoStartDate" class="form-label">PTO Start Date</label>
                                        <input type="date" class="form-control" id="ptoStartDate">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="ptoEndDate" class="form-label">PTO End Date</label>
                                        <input type="date" class="form-control" id="ptoEndDate">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="bio" class="form-label">Bio</label>
                                    <textarea class="form-control" id="bio" rows="4"></textarea>
                                </div>

                                <!-- Submit Button -->
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-1"></i> Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Attachments Tab -->
                        <div class="tab-pane fade" id="attachments" role="tabpanel"
                            aria-labelledby="attachments-tab">
                            <div class="card">
                                <div class="card-body pt-3">
                                    <!-- Uploaded Area -->
                                    <div id="documentsContainer">
                                        <div class="d-flex justify-content-center">
                                            <span class="spinner-border spinner-border-sm text-bos" role="status" aria-hidden="true"></span>
                                        </div>
                                    </div>
                                    <!-- File List -->
                                    <div class="file-list" id="fileList"></div>
                                    <!-- Upload Area -->
                                    <div class="upload-area" id="uploadArea">
                                        <input type="file" id="fileInput" multiple>
                                        <i class="bi bi-cloud-arrow-up upload-icon"></i>
                                        <h5>Drag & Drop files here</h5>
                                        <p class="text-muted">or</p>
                                        <button class="btn btn-primary">
                                            <i class="bi bi-folder2-open me-2"></i>Browse Files
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Don't forget to include Bootstrap CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


<?php require_once("{$_SERVER['DOCUMENT_ROOT']}/inc/footer.php"); ?>
<script src="./script.js?v=1"></script>