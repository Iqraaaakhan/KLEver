<?php
// 1. Start the session to be able to access it.
session_start();

// 2. Unset all session variables.
session_unset();

// 3. Destroy the session completely.
session_destroy();

// 4. Redirect the user back to the homepage.
header("Location: index.php");
exit(); // Always exit after a header redirect
?>