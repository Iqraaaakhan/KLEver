<?php
// This ensures we can access session variables on any page that includes this header.
// The @ symbol is a small trick to prevent an error if a session is already started.
@session_start();

// --- PHP Logic Block ---
// Here, we determine if the user is logged in and get their initial for the icon.

// We start by assuming the user is a logged-out visitor
$is_logged_in = false;
$user_initial = '';

// Now, we check if the special session variables for a logged-in user exist
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    // If they exist, the user IS logged in
    $is_logged_in = true;
    // We grab the first letter of their username for the icon
    $user_initial = strtoupper(substr($_SESSION['username'], 0, 1));
}
// --- End of PHP Logic Block ---
?>

<!-- This HTML structure is designed to work perfectly with your existing style.css -->
<header class="main-header">
    <div class="container">
        <a href="index.php" class="logo">KLE<span>ver</span></a>
      
        <nav>
            <a href="index.php">Home</a>
            <a href="menu.php">Menu</a>
            <a href="track_order.php">Track Order</a>

            <?php if ($is_logged_in): ?>
                <!-- If the user IS logged in, we show these two items -->
                <a href="logout.php">Logout</a>
                <div class="user-icon" title="Logged in as <?php echo htmlspecialchars($_SESSION['username']); ?>">
                    <?php echo htmlspecialchars($user_initial); ?>
                </div>

            <?php else: ?>
                <!-- If the user IS NOT logged in, we show these two items -->
                <a href="login.php">Login</a>
                <a href="menu.php" class="btn-primary">Order Now</a>
            <?php endif; ?>
        </nav>
    </div>
</header>