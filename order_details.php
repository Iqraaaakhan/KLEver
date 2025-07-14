<!-- order_details.php -->
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Details - Canteen Automation System</title>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f4f1ee;
      margin: 0;
      padding: 0;
      color: #333;
    }

    header {
      background-color: #322C2B;
      padding: 20px;
      color: #E4C59E;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    header h1 {
      font-size: 28px;
    }

    nav a {
      color: #E4C59E;
      margin-left: 20px;
      text-decoration: none;
      font-weight: bold;
    }

    .order-details {
      max-width: 1000px;
      margin: 40px auto;
      padding: 20px;
      background-color: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #AF8260;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 12px 15px;
      border-bottom: 1px solid #ddd;
      text-align: center;
    }

    th {
      background-color: #AF8260;
      color: white;
    }

    tr:hover {
      background-color: #f1f1f1;
    }

    footer {
      background-color: #322C2B;
      color: #E4C59E;
      text-align: center;
      padding: 15px;
      margin-top: 40px;
    }

    .btn {
      display: inline-block;
      background-color: #AF8260;
      color: white;
      padding: 10px 20px;
      margin-top: 15px;
      text-decoration: none;
      border-radius: 5px;
    }
  </style>
</head>

<body>
  <header>
    <h1>KLEver</h1>
    <nav>
      <a href="index.html">Home</a>
      <a href="menu.html">Menu</a>
      <a href="order.php">Order</a>
      <a href="order_details.php">Order Details</a>
      <a href="login.php">Login</a>
    </nav>
  </header>

  <div class="order-details">
    <h2>Order Details</h2>
    <table>
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Item Name</th>
          <th>Quantity</th>
          <th>Total Price (₹)</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <!-- Sample Static Data (replace with dynamic PHP later) -->
        <tr>
          <td>1001</td>
          <td>Burger</td>
          <td>2</td>
          <td>₹100</td>
          <td>Completed</td>
        </tr>
        <tr>
          <td>1002</td>
          <td>Pizza</td>
          <td>1</td>
          <td>₹120</td>
          <td>Pending</td>
        </tr>
        <tr>
          <td>1003</td>
          <td>Tea</td>
          <td>3</td>
          <td>₹30</td>
          <td>Completed</td>
        </tr>
      </tbody>
    </table>
  </div>

  <footer>
    <p>&copy; 2025 Canteen Automation System. All rights reserved.</p>
  </footer>
</body>

</html>
