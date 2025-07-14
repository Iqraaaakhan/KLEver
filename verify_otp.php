<?php
session_start();
$message = "";

// 1. Security Check: If the user hasn't completed step 1 (login), send them back.
if (!isset($_SESSION['otp_user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_otp = $_POST['otp'];

    // 3. Check if the entered OTP matches the one in the session
    if ($entered_otp == $_SESSION['otp_code']) {
        // --- OTP IS CORRECT, COMPLETE THE LOGIN ---

        // 4. Connect to the database to get the user's final details
        $conn = mysqli_connect("localhost", "root", "", "klever_db");
        if (!$conn) { die("Connection failed."); } // Basic error check

        $stmt = $conn->prepare("SELECT username, type FROM user WHERE id = ?");
        if (!$stmt) { die("Statement preparation failed."); } // Basic error check

        $stmt->bind_param("i", $_SESSION['otp_user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // 5. Set the FINAL session variables that show the user is logged in
        $_SESSION["userid"] = $user['username'];
        $_SESSION["user_type"] = $user['type'];

        // 6. Clean up the temporary OTP data from the session
        unset($_SESSION['otp_code']);
        unset($_SESSION['otp_user_id']);
        unset($_SESSION['otp_user_email']);
        
        // 7. Redirect the user to the homepage!
        header("Location: index.php");
        exit(); // Crucial: Always stop the script after a redirect.

    } else {
        // --- OTP IS WRONG ---
        $message = "❌ Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style1.css"> <!-- Make sure this points to your CSS file -->
</head>
<body>
<div class="form-container">
    <form method="post" action="">

      <div class="form-header">
        <i class="fa-solid fa-shield-halved icon"></i>
        <h1>Verify Your Identity</h1>
      </div>

      <p style="text-align:center; color:#555; margin-bottom: 20px;">
        An OTP has been sent to your email:<br>
        <b><?php echo htmlspecialchars($_SESSION['otp_user_email']); ?></b>
      </p>

      <?php if (!empty($message)): ?>
        <div class="error-message"><?php echo $message; ?></div>
      <?php endif; ?>

      <div class="input-group">
        <input type="text" name="otp" placeholder="Enter 6-Digit OTP" required pattern="\d{6}" title="Please enter a 6-digit number">
      </div>

      <button type="submit" class="btn">Verify & Login</button>

    </form>
</div>
</body>
</html>