<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'klever_db');
if ($conn->connect_error) { die("Database connection failed."); }

$message = '';
$redirect_url = 'login.php'; // Default redirect location

// Check if the form was submitted with an OTP
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['otp'])) {
    $submitted_otp = $_POST['otp'];

    // --- LOGIC FOR NEW USER REGISTRATION ---
    if (isset($_SESSION['registration_data']) && $submitted_otp == $_SESSION['registration_data']['otp']) {
        
        // OTP is correct for registration! Now, save the user to the database.
        $username = $_SESSION['registration_data']['username'];
        $email = $_SESSION['registration_data']['email'];
        $phone = $_SESSION['registration_data']['phone'];
        $password = $_SESSION['registration_data']['password']; // Get password from session

        // Hash the password securely! This is critical.
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // Prepare the SQL to insert the new user
        $stmt = $conn->prepare("INSERT INTO user (username, email, phone, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $phone, $password_hash);
        
        if ($stmt->execute()) {
            // Success! User is saved. Now, log them in immediately.
            $_SESSION['user_id'] = $stmt->insert_id; // Get the new user's ID and store it
            $_SESSION['username'] = $username;

            // Clean up temporary registration data
            unset($_SESSION['registration_data']);
            
            // Redirect them to a success page or the menu
            header("Location: registration_success.php"); // Redirect to a success page
            exit;

        } else {
            $message = "Error: Could not create account. Please try again.";
        }
        $stmt->close();

    // --- LOGIC FOR EXISTING USER LOGIN ---
    } else if (isset($_SESSION['otp_user_data']) && $submitted_otp == $_SESSION['otp_user_data']['otp']) {

        // OTP is correct for login!
        $_SESSION['user_id'] = $_SESSION['otp_user_data']['user_id']; // You'll need to add user_id to the session in login.php
        $_SESSION['username'] = $_SESSION['otp_user_data']['username'];

        // Clean up temporary login data
        unset($_SESSION['otp_user_data']);

        // Redirect to the main menu
        header("Location: menu.php"); // Or your main dashboard
        exit;
        
    } else {
        // Submitted OTP is incorrect
        $message = "❌ Invalid OTP. Please try again.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Verify Account</title>
    <!-- Add your CSS from register.php for consistent styling -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* Paste the CSS from your other pages here */
        body { background-color: #e3dcd7; display: flex; justify-content: center; align-items: center; height: 100vh; font-family: 'Poppins', sans-serif; }
        .form-box { background: #fff; padding: 2.5rem; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 100%; max-width: 420px; text-align: center; }
        .form-box h1 { margin-bottom: 2rem; }
        .form-box input { width: 100%; padding: 1rem; margin-bottom: 1rem; border: none; background-color: #f3f3f3; border-radius: 8px; font-size: 1rem; }
        .form-box button { width: 100%; padding: 1rem; border: none; background-color: #bfa89e; color: #fff; border-radius: 8px; cursor: pointer; font-size: 1.1rem; font-weight: 600; }
        .message { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 600; color: #721c24; background-color: #f8d7da; }
    </style>
</head>
<body>
    <div class="form-box">
        <h1>Verify Your Account</h1>
        <p>An OTP has been sent to your email. Please enter it below.</p>
        <?php if ($message) echo "<p class='message'>$message</p>"; ?>
        <form method="POST" action="verify_otp.php">
            <input type="text" name="otp" placeholder="Enter OTP" required>
            <button type="submit">Verify</button>
        </form>
    </div>
</body>
</html>