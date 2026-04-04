                    </div>
                </div>

                <div class="mdk-drawer js-mdk-drawer" id="default-drawer" data-align="start">
                    <div class="mdk-drawer__content">
                        <div class="sidebar sidebar-light sidebar-left simplebar" data-simplebar="">
                            <div class="sidebar-heading sidebar-m-t">Menu</div>
                            <ul class="sidebar-menu">
                                <li class="sidebar-menu-item <?php echo strpos($_SERVER['PHP_SELF'], 'dashboard.php') !== false ? 'active' : ''; ?>">
                                    <a class="sidebar-menu-button" href="dashboard.php">
                                        <i class="sidebar-menu-icon sidebar-menu-icon--left fas fa-chart-pie"></i>
                                        <span class="sidebar-menu-text">Dashboard</span>
                                    </a>
                                </li>
                                <li class="sidebar-menu-item <?php echo strpos($_SERVER['PHP_SELF'], 'categories.php') !== false ? 'active' : ''; ?>">
                                    <a class="sidebar-menu-button" href="categories.php">
                                        <i class="sidebar-menu-icon sidebar-menu-icon--left fas fa-folder"></i>
                                        <span class="sidebar-menu-text">Categories</span>
                                    </a>
                                </li>
                                <li class="sidebar-menu-item <?php echo strpos($_SERVER['PHP_SELF'], 'products.php') !== false ? 'active' : ''; ?>">
                                    <a class="sidebar-menu-button" href="products.php">
                                        <i class="sidebar-menu-icon sidebar-menu-icon--left fas fa-box"></i>
                                        <span class="sidebar-menu-text">Products</span>
                                    </a>
                                </li>
                                <li class="sidebar-menu-item <?php echo strpos($_SERVER['PHP_SELF'], 'pv_settings.php') !== false ? 'active' : ''; ?>">
                                    <a class="sidebar-menu-button" href="pv_settings.php">
                                        <i class="sidebar-menu-icon sidebar-menu-icon--left fas fa-cog"></i>
                                        <span class="sidebar-menu-text">PV Settings</span>
                                    </a>
                                </li>
                                <li class="sidebar-menu-item <?php echo strpos($_SERVER['PHP_SELF'], 'withdrawals.php') !== false ? 'active' : ''; ?>">
                                    <a class="sidebar-menu-button" href="withdrawals.php">
                                        <i class="sidebar-menu-icon sidebar-menu-icon--left fas fa-money-bill-wave"></i>
                                        <span class="sidebar-menu-text">Withdrawals</span>
                                    </a>
                                </li>
                                <li class="sidebar-menu-item <?php echo strpos($_SERVER['PHP_SELF'], 'network.php') !== false ? 'active' : ''; ?>">
                                    <a class="sidebar-menu-button" href="network.php">
                                        <i class="sidebar-menu-icon sidebar-menu-icon--left fas fa-users"></i>
                                        <span class="sidebar-menu-text">Member Network</span>
                                    </a>
                                </li>
                                <li class="sidebar-menu-item">
                                    <a class="sidebar-menu-button" href="<?php echo $base_url; ?>index.php">
                                        <i class="sidebar-menu-icon sidebar-menu-icon--left fas fa-external-link-alt"></i>
                                        <span class="sidebar-menu-text">View Frontend</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts from Template -->
    <script src="js/jquery.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/simplebar.min.js"></script>
    <script src="js/dom-factory.js"></script>
    <script src="js/material-design-kit.js"></script>
    <script src="js/app.js"></script>
</body>
</html>
