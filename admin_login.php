<?php
session_start();

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin_dashboard.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli('localhost', 'root', '', 'klever_db');
    if ($conn->connect_error) { 
        die("Database connection failed: " . $conn->connect_error); 
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ? AND type = 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $admin_user = $result->fetch_assoc();
        if (password_verify($password, $admin_user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $admin_user['username'];
            header("Location: admin_dashboard.php");
            exit;
        }
    }
    
    $message = "Invalid Admin Credentials.";
    $stmt->close();
    $conn->close();
}
?>
<!-- The original HTML for the login form -->
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
      <input type="text" name="username" placeholder="Username" required value="admin">
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
  </div>
</body>
</html>