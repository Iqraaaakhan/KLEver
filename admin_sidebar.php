<!-- This is the code for admin_sidebar.php -->
<div class="sidebar">
    <h2 class="sidebar-header">KLEver</h2>
    <ul class="sidebar-nav">
        <!-- We will use PHP to dynamically set the 'active' class -->
        <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
        <li class="<?php if($current_page == 'admin_dashboard.php') echo 'active'; ?>"><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a></li>
        <li class="<?php if($current_page == 'admin_menu.php') echo 'active'; ?>"><a href="admin_menu.php"><i class="fas fa-utensils"></i>Menu Management</a></li>
        <li class="<?php if($current_page == 'admin_users.php') echo 'active'; ?>"><a href="admin_users.php"><i class="fas fa-users"></i>Users</a></li>
    </ul>
    <ul class="sidebar-nav logout-link">
          <li class="<?php if($current_page == 'admin_reports.php') echo 'active'; ?>"><a href="admin_reports.php"><i class="fas fa-chart-line"></i>Reports</a></li>
         <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
         
    </ul>
</div>