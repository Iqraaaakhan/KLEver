<?php
session_start();
// If user is not logged in, redirect them away
if (!isset($_SESSION['user_id'])) {
header("Location: login.php");
exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Success!</title>
<!-- This meta tag will automatically redirect to menu.php after 3 seconds -->
<meta http-equiv="refresh" content="3;url=menu.php">
<!-- Add your CSS for styling -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
body { background-color: #e3dcd7; display: flex; justify-content: center; align-items: center; height: 100vh; font-family: 'Poppins', sans-serif; text-align: center; }
.success-box { background: #fff; padding: 3rem; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
h1 { color: #2d6a4f; }
</style>
</head>
<body>
<div class="success-box">
<h1>✅ Account Created!</h1>
<p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
<p>Redirecting you to the menu shortly...</p>
</div>
</body>
</html>