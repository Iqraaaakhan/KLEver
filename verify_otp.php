<?php
// This MUST be the very first line to access session data.
session_start();

// Establish the database connection.
$conn = new mysqli('localhost', 'root', '', 'klever_db');
if ($conn->connect_error) { 
    die("Connection failed: " . $conn->connect_error); 
}

$message = '';

// Check if the form was submitted with an OTP.
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['otp'])) {
    $submitted_otp = $_POST['otp'];

    // --- SCENARIO 1: NEW USER REGISTRATION ---
    if (isset($_SESSION['registration_data']) && $submitted_otp == $_SESSION['registration_data']['otp']) {
        // This part works correctly.
        $username = $_SESSION['registration_data']['username'];
        $email = $_SESSION['registration_data']['email'];
        $phone = $_SESSION['registration_data']['phone'];
        $password = $_SESSION['registration_data']['password'];
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        
        $stmt = $conn->prepare("INSERT INTO user (username, email, phone, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $phone, $password_hash);
        
        if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id; 
            $_SESSION['username'] = $username;
            unset($_SESSION['registration_data']);
            header("Location: registration_success.php");
            exit;
        } else {
            $message = "Error: Could not create account.";
        }
        $stmt->close();
    
    // --- SCENARIO 2: EXISTING USER LOGIN ---
    } else if (isset($_SESSION['otp_user_data']) && $submitted_otp == $_SESSION['otp_user_data']['otp']) {
        
        // OTP is correct for login. THIS IS THE CRITICAL PART.
        // We now create the FINAL login session variables that the header needs.
        
        // CRITICAL LINE 1: Set the permanent user ID for this session.
        $_SESSION['user_id'] = $_SESSION['otp_user_data']['id']; 
        
        // CRITICAL LINE 2: Set the permanent username for this session.
        $_SESSION['username'] = $_SESSION['otp_user_data']['username'];

        // Now that the final session is created, we create a success message.
        $_SESSION['flash_message'] = "Welcome back, " . htmlspecialchars($_SESSION['username']) . "!";
        
        // IMPORTANT: Clean up the temporary OTP data.
        unset($_SESSION['otp_user_data']);

        // Redirect to the homepage. The header will now see the session and show the icon.
        header("Location: index.php"); 
        exit;
        
    } else {
        $message = "❌ Invalid OTP. Please try again.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Verify Account</title>
    <!-- Your beautiful UI for this page is preserved -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css"/> <!-- Assuming you might want to link your main stylesheet -->
    <style>
        /* This keeps the verification page looking consistent with your login/register pages */
        body { background-color: #f4f4f9; display: flex; justify-content: center; align-items: center; height: 100vh; font-family: 'Poppins', sans-serif; }
        .form-box { background: #fff; padding: 2.5rem; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 100%; max-width: 420px; text-align: center; }
        .form-box h1 { margin-bottom: 1rem; }
        .form-box p.subtitle { color: #555; margin-bottom: 2rem; }
        .form-box input { width: 100%; padding: 1rem; margin-bottom: 1rem; border: 1px solid #ddd; background-color: #f9f9f9; border-radius: 8px; font-size: 1.5rem; text-align: center; letter-spacing: 0.5rem; }
        .form-box button { width: 100%; padding: 1rem; border: none; background-color: #AF8260; color: #fff; border-radius: 8px; cursor: pointer; font-size: 1.1rem; font-weight: 600; }
        .message { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 600; color: #721c24; background-color: #f8d7da; }
    </style>
</head>
<body>
    <div class="form-box">
        <h1>Verify Your Account</h1>
        <p class="subtitle">An OTP has been sent to your email.</p>
        <?php if ($message) echo "<p class='message'>$message</p>"; ?>
        <form method="POST" action="verify_otp.php">
            <input type="text" name="otp" placeholder="_ _ _ _ _ _" maxlength="6" required>
            <button type="submit">Verify & Login</button>
        </form>
    </div>
</body>
</html>