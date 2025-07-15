<?php
// The professional way: only start a session if one does not already exist.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set default values for when a user is logged out
$is_logged_in = false;
$user_initial = '';

// Check if the FINAL login session variables exist
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    $is_logged_in = true;
    $user_initial = strtoupper(substr($_SESSION['username'], 0, 1));
}
?>

<!-- This HTML is now structured EXACTLY as you specified -->
<header class="main-header">
    <div class="container">
        <a href="index.php" class="logo">KLE<span>ver</span></a>
      
        <nav>
            <!-- These are the static links -->
            <a href="index.php">Home</a>
            <a href="menu.php">Menu</a>

            <!-- The "Order Now" button is now part of the nav flow for logged-in users -->
            <a href="menu.php" class="btn-primary">Order Now</a>

            <a href="track_order.php">Track Order</a>

            <?php if ($is_logged_in): ?>
                <!-- If Logged In: Show Logout link and then the User Icon at the end -->
                <a href="logout.php">Logout</a>
                <div class="user-icon" title="Logged in as <?php echo htmlspecialchars($_SESSION['username']); ?>">
                    <?php echo htmlspecialchars($user_initial); ?>
                </div>
            <?php else: ?>
                <!-- If Logged Out: The "Login" link will appear here -->
                <a href="login.php">Login</a>
            <?php endif; ?>
        </nav>
    </div>
</header>