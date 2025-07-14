<?php // login.php - Passwordless OTP Login
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['identifier'])) {
    $conn = new mysqli('localhost', 'root', '', 'klever_db');
    if ($conn->connect_error) { die("Database connection failed."); }

    $identifier = $_POST['identifier'];

    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ? OR phone = ?");
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $otp = rand(100000, 999999);

        $_SESSION['otp_user_data'] = [
            'id' => $user['id'], // <-- ADD THIS LINE
            'username' => $user['username'],
            'email' => $user['email'],
            'type' => $user['type'],
            'otp' => $otp
        ];

        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'klever.cams@gmail.com';  // Your app's email
            $mail->Password   = 'zkpmrhfwclzokrpj';         // YOUR NEW APP PASSWORD (no spaces)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            //Recipients
            $mail->setFrom('klever.cams@gmail.com', 'KLEver Canteen');
            $mail->addAddress($user['email'], $user['username']);

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Your Login Code for KLEver';
            $mail->Body    = "Your One-Time Password (OTP) for KLEver is: <h3><b>$otp</b></h3>";
            $mail->send();
            
            header("Location: verify_otp.php");
            exit;
        } catch (Exception $e) {
            $message = "Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $message = "No account found with that username or phone number.";
    }
    $conn->close();
}
?>
<!-- Your HTML is unchanged -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - KLEver</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background-color: #e3dcd7; display: flex; justify-content: center; align-items: center; height: 100vh; font-family: 'Poppins', sans-serif; }
        .form-box { background: #fff; padding: 2.5rem; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 100%; max-width: 420px; text-align: center; }
        .form-box h1 { display: flex; align-items: center; justify-content: center; gap: 0.75rem; margin-bottom: 2rem; color: #333; font-size: 1.8rem; }
        .form-box .icon { color: #c9ada7; }
        .form-box input { width: 100%; padding: 1rem; margin-bottom: 1rem; border: none; background-color: #f3f3f3; border-radius: 8px; font-size: 1rem; transition: box-shadow 0.3s; }
        .form-box input:focus { box-shadow: 0 0 0 2px #c9ada7; outline: none; }
        .form-box button { width: 100%; padding: 1rem; border: none; background-color: #bfa89e; color: #fff; border-radius: 8px; cursor: pointer; font-size: 1.1rem; font-weight: 600; transition: background-color 0.3s; }
        .form-box button:hover { background-color: #a99185; }
        .message { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 600; color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; }
        .register-link { margin-top: 1.5rem; color: #555; font-size: 0.9rem; }
        .register-link a { color: #bfa89e; font-weight: 600; text-decoration: none; }
    </style>
</head>
<body>
<div class="form-box">
    <h1><span class="icon">🍴</span> Login</h1>
    <?php if ($message) echo "<p class='message'>$message</p>"; ?>
    <form method="POST" action="login.php">
        <input type="text" name="identifier" placeholder="Username or Phone Number" required>
        <button type="submit">Send OTP</button>
    </form>
    <p class="register-link">Don't have an account? <a href="register.php">Register Now</a></p>
</div>
</body>
</html>