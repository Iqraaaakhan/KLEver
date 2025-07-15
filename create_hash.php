<?php
// The password we want to hash.
$plain_password = 'admin123';

// Generate a secure hash. The BCRYPT algorithm is the modern standard.
$hashed_password = password_hash($plain_password, PASSWORD_BCRYPT);

// Display the hash on the screen.
echo 'The correct hash for your password is:<br><br>';
echo '<strong>' . $hashed_password . '</strong>';
?>