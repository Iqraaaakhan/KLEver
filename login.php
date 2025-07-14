<?php
session_start();

// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';

$message = "";

if (isset($_SESSION['userid'])) {
    if ($_SESSION['user_type'] == 1) {
        header("Location: admin.html");
    } else {
        header("Location: index.php");
    }
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = mysqli_connect("localhost", "root", "", "klever_db");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $username = mysqli_real_escape_string($conn, $_POST["userName"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, username, email, password, type FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $otp = random_int(100000, 999999); 
            $_SESSION['otp_code'] = $otp;
            $_SESSION['otp_user_id'] = $user['id'];
            $_SESSION['otp_user_email'] = $user['email'];

            $mail = new PHPMailer(true);
            try {
                // Keep this debug line ON until it works!
                $mail->SMTPDebug = SMTP::DEBUG_SERVER; 
                
                //Server settings - CONFIGURED WITH YOUR EMAIL
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;

                // --- YOUR EMAIL ADDRESS IS NOW HERE ---
                $mail->Username   = 'klevercanteen@gmail.com'; 

                // --- PASTE YOUR APP PASSWORD ON THE NEXT LINE ---
                $mail->Password   = 'pfdemlfbnxktssqy'; 

                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465;

                // --- YOUR EMAIL ADDRESS IS NOW HERE ---
                $mail->setFrom('klevercanteen@gmail.com', 'Klever Canteen System');
                $mail->addAddress($user['email'], $user['username']);

                $mail->isHTML(true);
                $mail->Subject = 'Your Login OTP for Canteen System';
                $mail->Body    = "Hello {$user['username']},<br><br>Your One-Time Password to log in is: <h1>$otp</h1><br>This code is valid for 5 minutes.<br><br>Thank you!";

                $mail->send();
                header("Location: verify_otp.php");
                exit();

            } catch (Exception $e) {
                echo "<h3>Something went wrong. The OTP email could not be sent.</h3>";
                echo "<strong>Mailer Error:</strong> {$mail->ErrorInfo}";
                echo "<hr><p>This is the debug output. The error is likely 'Username and Password not accepted'. Follow the App Password instructions to fix it.</p>";
                exit(); 
            }
        } else {
            $message = "❌ Invalid Username or Password!";
        }
    } else {
        $message = "❌ Invalid Username or Password!";
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<!-- Your HTML code continues below... it is correct. -->
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Canteen Login</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style1.css">
</head>
<body>

  <div class="form-container">
    <form method="post" action="">
      <div class="form-header">
        <i class="fa-solid fa-utensils icon"></i>
        <h1>Login</h1>
      </div>
      
      <?php if (!empty($message)): ?>
        <div class="error-message"><?php echo $message; ?></div>
      <?php endif; ?>

      <div class="input-group">
        <input type="text" name="userName" placeholder="Username" required>
      </div>
      <div class="input-group">
        <input type="password" name="password" placeholder="Password" required>
      </div>

      <button type="submit" name="submit" class="btn">Sign In</button>

      <div class="switch-form">
        Don't have an account? <a href="register.php">Register Now</a>
      </div>
    </form>
  </div>
  
  <script>
    // This script will cause an error because there is no element with id="togglePassword"
    // It's safe to remove it or fix your HTML if you need the checkbox.
  </script>
</body>
</html>