<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4 " id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0" href="https://invest.kingsglobalconsulting.com" target="_blank">
            <span class="ms-1 font-weight-bold">KingsGlobal Consulting</span>
        </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link active" href="/">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="bi bi-easel2 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="https://forms.gle/1C9qXQ72kdSUcs1G9" target="_blank">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="bi bi-clipboard2-pulse text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">PTO Result</span>
                </a>
            </li>
            <?php if (in_array("Manage Users", $Account->permissions)) { ?>
                <li class="nav-item">
                    <a class="nav-link active" href="/users">
                        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="bi bi-people text-dark text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">View Users</span>
                    </a>
                </li>
            <?php } ?>
            <?php if (in_array("Manage Reports", $Account->permissions)) { ?>
                <li class="nav-item">
                    <a class="nav-link active" href="/reports">
                        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="bi bi-book text-dark text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Manage Reports</span>
                    </a>
                </li>
            <?php } ?>
            <?php if (in_array("Manage Roles", $Account->permissions)) { ?>
                <li class="nav-item">
                    <a class="nav-link active" href="/roles-permissions">
                        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="bi bi-diamond-half text-dark text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Roles & Permissions</span>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>
</aside>