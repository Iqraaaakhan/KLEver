<?php
$order_code = isset($_GET['code']) ? htmlspecialchars($_GET['code']) : 'XXXX';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order Confirmed</title>
  <style>
    body {
      background-color: #EAEBD0;
      font-family: Arial, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .confirmation-box {
      background-color: #2b2b2b;
      color: white;
      padding: 40px;
      border-radius: 15px;
      text-align: center;
      box-shadow: 0 0 20px rgba(174, 200, 164, 0.4);
      width: 90%;
      max-width: 500px;
    }

    .confirmation-box h1 {
      color: #AEC8A4;
      font-size: 32px;
      margin-bottom: 20px;
    }

    .order-code {
      background-color: #901E3E;
      padding: 10px 20px;
      display: inline-block;
      border-radius: 10px;
      margin-top: 15px;
      font-size: 22px;
      letter-spacing: 2px;
    }

    .quote {
      margin-top: 25px;
      font-style: italic;
      color: #f4f4f4;
    }

    .back-link {
      display: inline-block;
      margin-top: 30px;
      background-color: #AF8260;
      color: white;
      text-decoration: none;
      padding: 10px 20px;
      border-radius: 30px;
      font-weight: bold;
      transition: 0.3s ease;
    }

    .back-link:hover {
      background-color: #803D3B;
    }
        /* In order-confirmed.php, add these styles inside the <style> tag */

    /* The new container for the order code and track link */
    .order-info-box {
      background-color: rgba(255, 255, 255, 0.05); /* Very subtle background */
      border: 1px solid rgba(255, 255, 255, 0.1);
      padding: 1.5rem;
      border-radius: 10px;
      margin-top: 1.5rem;
      margin-bottom: 1.5rem;
    }

    /* The main order code text */
    .order-code {
      font-size: 1.5rem; /* Slightly smaller for better balance */
      letter-spacing: 1px;
      color: #fff;
    }
    
    .order-code strong {
      color: #AEC8A4; /* Your theme's accent color */
    }

    /* The new subtle track link */
    .track-link-subtle {
      display: block; /* Puts it on its own line */
      margin-top: 0.75rem;
      color: #AEC8A4; /* Your theme's accent color */
      text-decoration: underline;
      font-size: 0.9rem;
      font-weight: 600;
      opacity: 0.8;
      transition: opacity 0.3s ease;
    }
    
    .track-link-subtle:hover {
      opacity: 1;
    }

    /* Adjust the main "Back to Menu" button for better spacing */
    .back-link {
      margin-top: 1rem;
    }
  </style>
  <!-- In order-confirmed.php, add this inside the <head> -->
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>
</head>
<body>

  <div class="confirmation-box">
    <h1>🎉 Order Confirmed!</h1>
    <p>Your food is being prepared. Please wait patiently...</p>

          <!-- This is the NEW, elegant block -->
      <div class="order-info-box">
          <div class="order-code">Order Code: <strong>#<?php echo $order_code; ?></strong></div>
          <a href="track_order.php?order_code=<?php echo $order_code; ?>" class="track-link-subtle">Track your order</a>
      </div>

    <p class="quote">"Good food takes time – thank you for waiting just 5 minutes!" 🍽️</p>

    <a href="menu.php" class="back-link">Back to Menu</a>
  </div>
<!-- In order-confirmed.php, add this before </body> -->
<script>
  // This runs when the page is fully loaded
  document.addEventListener('DOMContentLoaded', () => {
    // A simple, centered burst
    confetti({
      particleCount: 150,
      spread: 90,
      origin: { y: 0.6 }
    });
  });
</script>
</body>
</html>
