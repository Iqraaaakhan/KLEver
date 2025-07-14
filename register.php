<?php
// Added for good practice, standard for user-based applications.
session_start(); 

$message = "";
$message_class = ""; // To hold the CSS class for the message box

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = mysqli_connect("localhost", "root", "", "klever_db");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $stmt = $conn->prepare("INSERT INTO user (username, email, password, type) VALUES (?, ?, ?, ?)");
    
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    
    $username = $_POST["userName"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $user_type = 0;

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $stmt->bind_param("sssi", $username, $email, $hashed_password, $user_type);

    if ($stmt->execute()) {
        // This is a success
        $message = "✅ Registration successful!";
        $message_class = 'success-message'; // Use the green success style
        
        // This line tells the browser to wait 2 seconds, then go to index.php
        header("refresh:2;url=index.php"); 
        
    } else {
        // This is an error
        $message = "❌ Error: " . $stmt->error;
        $message_class = 'error-message'; // Use the red error style
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <!-- Make sure your CSS file is named style1.css -->
    <link rel="stylesheet" href="style1.css"> 
</head>
<body>
<div class="form-container">
    <form method="post" action="" autocomplete="off">

      <div class="form-header">
        <i class="fa-solid fa-utensils icon"></i>
        <h1>Create Account</h1>
      </div>

      <?php if (!empty($message)): ?>
        <!-- This now uses a dynamic class to be either red or green -->
        <div class="<?php echo $message_class; ?>">
            <?php echo $message; ?>
        </div>
      <?php endif; ?>

      <div class="input-group">
        <input type="text" name="userName" placeholder="Choose a Username" required>
      </div>
      <div class="input-group">
        <input type="email" name="email" placeholder="Your Email Address" required>
      </div>
      <div class="input-group">
        <!-- This 'autocomplete' attribute prevents the password breach popup -->
        <input type="password" name="password" placeholder="Create a Password" required autocomplete="new-password">
      </div>

      <button type="submit" name="submit" class="btn">Register</button>

      <div class="switch-form">
        Already have an account? <a href="login.php">Login Here</a>
      </div>
    </form>
  </div>
</body>
</html>