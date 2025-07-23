<?php
// Enter the password you want to use for your admin account here.
$new_admin_password = 'admin123'; // Or choose a new, secure password

// Generate a secure hash for the new password.
$hashed_password = password_hash($new_admin_password, PASSWORD_BCRYPT);

// Display the hash and the SQL command to run.
echo '<h1>Admin Password Reset Tool</h1>';
echo '<p>Your new password is: <strong>' . $new_admin_password . '</strong></p>';
echo '<p>Copy the SQL command below and run it in MySQL Workbench to update your admin password.</p>';
echo '<hr>';
echo '<h3>SQL Command to Run:</h3>';
echo '<pre style="background:#f4f4f4; padding:1rem; border-radius:5px; word-wrap:break-word;">';
echo "UPDATE user SET password = '" . $hashed_password . "' WHERE username = 'admin';";
echo '</pre>';
?>