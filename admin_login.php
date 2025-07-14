<?php // admin_login.php
session_start();

// If the admin is already logged in, send them straight to the dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin_dashboard.php");
    exit;
}

$message = "";

// This code runs when the admin submits the login form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // --- Connect to your one true database ---
    $conn = new mysqli('localhost', 'root', '', 'klever_db');
    if ($conn->connect_error) { die("Connection failed"); }

    // --- We will use the hardcoded admin user for now for simplicity ---
    // In a bigger project, you would query the 'user' table where type=1
    define('ADMIN_USER', 'admin');
    define('ADMIN_PASS', 'password123'); // This is the password from your 'user' table

    $username = $_POST['username'];
    $password = $_POST['password'];

    // --- Check if the login details are correct ---
    if ($username === ADMIN_USER && $password === ADMIN_PASS) {
        // If correct, set the session variable to mark them as a logged-in admin
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        // Redirect to the dashboard
        header("Location: admin_dashboard.php");
        exit;
    } else {
        $message = "Invalid Admin Credentials.";
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login - KLEver</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body { background-color: #322C2B; display: flex; justify-content: center; align-items: center; height: 100vh; font-family: 'Poppins', sans-serif; }
    .login-container { background: #fff; padding: 3rem; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); width: 100%; max-width: 400px; text-align: center; }
    .login-container h1 { margin-bottom: 0.5rem; color: #322C2B; font-size: 2rem; }
    .login-container p { margin-bottom: 2rem; color: #777; }
    .login-container input { width: 100%; padding: 0.8rem; margin-bottom: 1rem; border: 1px solid #ccc; border-radius: 5px; font-size: 1rem; }
    .login-container button { width: 100%; padding: 0.8rem; border: none; background-color: #803D3B; color: white; border-radius: 5px; cursor: pointer; font-size: 1.1rem; font-weight: 600; transition: background-color 0.3s; }
    .login-container button:hover { background-color: #523c3c; }
    .error { color: #D8000C; font-weight: bold; margin-bottom: 1rem; }
  </style>
</head>
<body>
  <div class="login-container">
    <h1>KLEver Admin Panel</h1>
    <p>Please log in to manage the canteen.</p>
    <?php if ($message) echo "<p class='error'>$message</p>"; ?>
    <form method="POST" action="admin_login.php">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
  </div>
</body>
</html>