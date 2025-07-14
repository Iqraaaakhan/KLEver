<?php
// 1. Start the session to be able to access it.
session_start();

// 2. Unset all session variables.
session_unset();

// 3. Destroy the session completely.
session_destroy();

// 4. Redirect the user back to the login page.
header("Location: login.php");
exit();
?>


<?php // logout.php
session_start();
session_unset();
session_destroy();
// It should redirect to the admin login page
header("Location: admin_login.php"); 
exit;
?>