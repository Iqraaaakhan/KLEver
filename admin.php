<?php
// admin.php - The Admin Dashboard

// 1. Start the session. This is ALWAYS the first line.
session_start();

// 2. The Security Check
//    Check if the user is logged in by looking for the session variable.
//    Also, check if the user's 'type' is '1' (which means they are an admin).
//    The '||' means "OR", so if either of these checks fails, the code inside the 'if' will run.
if (!isset($_SESSION['userid']) || $_SESSION['user_type'] != 1) {
    // If the user is not an admin, we redirect them to the login page.
    header("Location: index.php"); 
    
    // It's very important to exit() after a header redirect to stop the script.
    exit();
}

// 3. If the script gets to this point, it means the user is a logged-in admin.
//    We can now safely display the admin dashboard content below.

?>
<?php
// admin.php - The Admin Dashboard
session_start();
if (!isset($_SESSION['userid']) || $_SESSION['user_type'] != 1) {
    header("Location: index.php"); 
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - KLEver</title>
    <!-- We can link to a new CSS file for the admin area later if we want -->
    <link rel="stylesheet" href="style.css"> 
    <style>
        /* Some simple styles to make the admin page look nice */
        body { background-color: #f4f7f6; }
        .admin-container { 
            max-width: 1000px; 
            margin: 2rem auto; 
            padding: 2rem; 
            background: #fff; 
            border-radius: 8px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }
        .admin-header h1 { color: #333; }
        .logout-link { 
            background-color: #d9534f;
            color: white;
            padding: 0.5rem 1rem;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>

    <div class="admin-container">
        <div class="admin-header">
            <h1>Admin Dashboard</h1>
            <!-- The logout link will use the same logout.php file, which works perfectly! -->
            <a href="logout.php" class="logout-link">Logout</a>
        </div>
        
        <!-- We use the session variable to welcome the specific admin who logged in. -->
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['userid']); ?>!</h2>
        <p>This is the control panel for the KLEver Canteen. From here you can manage orders, update the menu, and view reports.</p>
        
        <hr style="margin: 2rem 0;">

        <!-- This is where we will add the Order Management and Menu Management sections in the next steps -->
        <p><i>Order and Menu Management sections will be added here soon.</i></p>

    </div>

</body>
</html>
