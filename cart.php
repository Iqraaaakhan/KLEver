<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Cart - Canteen Automation System</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #EAEBD0;
      margin: 0;
      padding: 0;
    }

    .main {
      max-width: 600px;
      margin: 40px auto;
      padding: 30px;
      background-color: #2b2b2b;
      border-radius: 15px;
      color: white;
      box-shadow: 0 0 20px rgba(174, 200, 164, 0.4);
    }

    h2 {
      text-align: center;
      margin-bottom: 25px;
    }

    .summary p {
      margin: 8px 0;
      color: #AEC8A4;
    }

    .total {
      font-weight: bold;
      font-size: 18px;
      text-align: right;
      margin-top: 15px;
      color: #fff;
    }

    label {
      display: block;
      margin-top: 20px;
      color: #ccc;
    }

    input, select {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      background: transparent;
      border: none;
      border-bottom: 2px solid #AEC8A4;
      color: white;
      font-size: 16px;
      outline: none;
    }

    select option {
      color: black;
    }

    button {
      width: 100%;
      padding: 12px;
      margin-top: 25px;
      background-color: #901E3E;
      border: none;
      border-radius: 30px;
      color: white;
      font-size: 18px;
      cursor: pointer;
    }

    button:hover {
      background-color: #AEC8A4;
      color: #2b2b2b;
    }
  </style>
</head>
<body>

  <div class="main">
    <h2>Cart Summary</h2>
    <div id="summary" class="summary"></div>
    <div id="total" class="total">Total: ₹0</div>

    <label for="name">Name:</label>
    <input type="text" id="name" required>

    <label for="email">College Email ID:</label>
    <input type="email" id="email" required>

    <label for="payment">Payment Method:</label>
    <select id="payment" required>
      <option value="">Select</option>
      <option value="UPI">UPI</option>
      <option value="Card">Card</option>
      <option value="COD">Cash on Delivery</option>
    </select>
<button id="confirmBtn" onclick="submitOrder()" disabled>Confirm Order</button>
  </div>


<script>
  const summaryDiv = document.getElementById("summary");
  const totalDiv = document.getElementById("total");
  const confirmBtn = document.getElementById("confirmBtn"); // Get the button

  const orderItems = JSON.parse(localStorage.getItem("orderItems")) || [];
  const orderTotal = localStorage.getItem("orderTotal") || 0;

  if (orderItems.length === 0) {
    summaryDiv.innerHTML = "<p>Your cart is empty. Please add items from the menu first.</p>";
    totalDiv.style.display = 'none'; // Hide the total if cart is empty
    confirmBtn.disabled = true; // Keep button disabled
    confirmBtn.style.cursor = 'not-allowed'; // Show a "not allowed" cursor
    confirmBtn.style.backgroundColor = '#ccc'; // Gray out the button
  } else {
    orderItems.forEach(item => {
      summaryDiv.innerHTML += `<p>${item.name} x ${item.quantity} = ₹${item.cost}</p>`;
    });
    totalDiv.innerText = `Total: ₹${orderTotal}`;
    confirmBtn.disabled = false; // ✅ Enable the button if there are items
  }

  function submitOrder() {
    // This function will now only run if the button is enabled
    const name = document.getElementById("name").value.trim();
    const email = document.getElementById("email").value.trim();
    const payment = document.getElementById("payment").value;

    if (!name || !email || !payment) {
      alert("⚠️ Please fill all the details.");
      return;
    }

    const orderData = {
      name: name,
      email: email,
      payment: payment,
      items: orderItems,
      total: orderTotal
    };

    fetch('process_order.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(orderData)
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        localStorage.clear();
        window.location.href = 'order-confirmed.php?code=' + data.order_code;
      } else {
        alert("❌ Order failed: " + data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert("❌ Something went wrong while placing the order.");
    });
  }
</script>
</body>
</html>
