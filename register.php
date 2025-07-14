<?php // register.php - Passwordless Registration
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli('localhost', 'root', '', 'klever_db');
    if ($conn->connect_error) { die("Database connection failed."); }

    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ? OR email = ? OR phone = ?");
    $stmt->bind_param("sss", $username, $email, $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message = "❌ Username, Email, or Phone Number already in use.";
    } else {
        $otp = rand(100000, 999999);
        $_SESSION['registration_data'] = ['username' => $username, 'email' => $email, 'phone' => $phone, 'password' => $_POST['password'],'otp' => $otp];

        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'klever.cams@gmail.com';      // Your app's email
            $mail->Password = 'zkpmrhfwclzokrpj';            // YOUR NEW APP PASSWORD (no spaces)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            $mail->setFrom('klever.cams@gmail.com', 'KLEver Canteen');
            $mail->addAddress($email, $username);
            $mail->isHTML(true);
            $mail->Subject = 'Verify Your KLEver Account';
            $mail->Body    = "Welcome to KLEver! Your verification OTP is: <h3><b>$otp</b></h3>";
            $mail->send();
            
            header("Location: verify_otp.php");
            exit;
        } catch (Exception $e) {
            // This is our debugging line from before
            $message = "Mailer Error: {$mail->ErrorInfo}";
        }
    }
    $conn->close();
}
?>
<!-- Your HTML is unchanged -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - KLEver</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { background-color: #e3dcd7; display: flex; justify-content: center; align-items: center; height: 100vh; font-family: 'Poppins', sans-serif; }
        .form-box { background: #fff; padding: 2.5rem; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 100%; max-width: 420px; text-align: center; }
        .form-box h1 { display: flex; align-items: center; justify-content: center; gap: 0.75rem; margin-bottom: 2rem; color: #333; font-size: 1.8rem; }
        .form-box .icon { color: #c9ada7; }
        .form-box input { width: 100%; padding: 1rem; margin-bottom: 1rem; border: none; background-color: #f3f3f3; border-radius: 8px; font-size: 1rem; }
        .form-box button { width: 100%; padding: 1rem; border: none; background-color: #bfa89e; color: #fff; border-radius: 8px; cursor: pointer; font-size: 1.1rem; font-weight: 600; }
        .message { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 600; color: #721c24; background-color: #f8d7da; }
        .login-link { margin-top: 1.5rem; color: #555; font-size: 0.9rem; }
        .login-link a { color: #bfa89e; font-weight: 600; text-decoration: none; }
    </style>
</head>
<body>
<div class="form-box">
    <h1><span class="icon">🍴</span> Create Account</h1>
    <?php if ($message) echo "<p class='message'>$message</p>"; ?>
    <form method="POST" action="register.php">
        <input type="text" name="username" placeholder="Choose a Username" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="tel" name="phone" placeholder="Phone Number" required>
            <input type="password" name="password" placeholder="Choose a Password" required> <!-- ADD THIS LINE -->
        <button type="submit">Register</button>
    </form>
    <p class="login-link">Already have an account? <a href="login.php">Login Now</a></p>
</div>
</body>
</html>