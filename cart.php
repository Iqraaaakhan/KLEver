<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Cart - Canteen Automation System</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <style>
    /* YOUR ORIGINAL CSS IS PRESERVED HERE - NO CHANGES */
    body { font-family: 'Poppins', sans-serif; background-color: #EAEBD0; margin: 0; padding: 0; }
    .main { max-width: 600px; margin: 40px auto; padding: 30px; background-color: #2b2b2b; border-radius: 15px; color: white; box-shadow: 0 0 20px rgba(174, 200, 164, 0.4); }
    h2 { text-align: center; margin-bottom: 25px; }
    .summary p { margin: 8px 0; color: #AEC8A4; }
    .total { font-weight: bold; font-size: 18px; text-align: right; margin-top: 15px; color: #fff; }
    label { display: block; margin-top: 20px; color: #ccc; }
    input, select { width: 100%; padding: 10px; margin-top: 5px; background: transparent; border: none; border-bottom: 2px solid #AEC8A4; color: white; font-size: 16px; outline: none; }
    select option { color: black; }
    button { width: 100%; padding: 12px; margin-top: 25px; background-color: #901E3E; border: none; border-radius: 30px; color: white; font-size: 18px; cursor: pointer; }
    button:hover { background-color: #AEC8A4; color: #2b2b2b; }
  </style>
  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
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
      <option value="Online">UPI / Card / Online</option>
      <option value="COD">Cash on Delivery</option>
    </select>
    <!-- THE FIX: The onclick attribute has been removed. -->
    <button id="confirmBtn" disabled>Confirm Order</button>
  </div>

<script>
  // This wrapper ensures the script runs only after the HTML is fully loaded.
  document.addEventListener('DOMContentLoaded', function() {
    const summaryDiv = document.getElementById("summary");
    const totalDiv = document.getElementById("total");
    const confirmBtn = document.getElementById("confirmBtn");

    const orderItems = JSON.parse(localStorage.getItem("orderItems")) || [];
    const orderTotal = parseFloat(localStorage.getItem("orderTotal")) || 0;

    // This function correctly sets up the initial state of the page.
    function initializeCartView() {
        if (orderItems.length === 0) {
            summaryDiv.innerHTML = "<p>Your cart is empty.</p>";
            totalDiv.style.display = 'none';
            confirmBtn.disabled = true;
            confirmBtn.style.cursor = 'not-allowed';
            confirmBtn.style.backgroundColor = '#ccc';
        } else {
            summaryDiv.innerHTML = ''; // Clear previous summary
            orderItems.forEach(item => {
                summaryDiv.innerHTML += `<p>${item.name} x ${item.quantity} = ₹${item.cost.toFixed(2)}</p>`;
            });
            totalDiv.innerText = `Total: ₹${orderTotal.toFixed(2)}`;
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Confirm Order';
            confirmBtn.style.cursor = 'pointer';
            confirmBtn.style.backgroundColor = '#901E3E';
        }
    }
    
    initializeCartView(); // Run this when the page first loads.

    // This is the main event listener for the order button.
    confirmBtn.addEventListener('click', function() {
      const name = document.getElementById("name").value.trim();
      const email = document.getElementById("email").value.trim();
      const paymentMethod = document.getElementById("payment").value;

      if (!name || !email || !paymentMethod) {
        alert("⚠️ Please fill all the details, including payment method.");
        return;
      }

      if (paymentMethod === 'COD') {
        submitOrderCOD(name, email);
      } else {
        initiateRazorpay(name, email);
      }
    });

    function submitOrderCOD(name, email) {
      const orderData = { name: name, email: email, payment: 'COD', items: orderItems, total: orderTotal };
      fetch('process_order.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(orderData) })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          localStorage.clear();
          window.location.href = 'order-confirmed.php?code=' + data.order_code;
        } else {
          alert("❌ Order failed: " + data.message);
        }
      });
    }

    function initiateRazorpay(name, email) {
      confirmBtn.disabled = true;
      confirmBtn.textContent = 'Initializing Payment...';

      fetch('create_razorpay_order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ total: orderTotal })
      })
      .then(response => {
        if (!response.ok) {
            return response.text().then(text => { throw new Error('Server Error: ' + text) });
        }
        return response.json();
      })
      .then(data => {
        if (data.success) {
          payWithRazorpay(data, name, email);
        } else {
          alert("Could not initialize payment: " + (data.message || 'Unknown error.'));
          confirmBtn.disabled = false;
          confirmBtn.textContent = 'Confirm Order';
        }
      })
      .catch(error => {
        console.error('Fetch Error:', error);
        alert('A critical error occurred. Please check the developer console (F12) for details.');
        confirmBtn.disabled = false;
        confirmBtn.textContent = 'Confirm Order';
      });
    }

    function payWithRazorpay(orderData, name, email) {
      var options = {
          "key": orderData.key,
          "amount": orderTotal * 100,
          "currency": "INR",
          "name": "KLEver Canteen",
          "description": "Canteen Food Order",
          "order_id": orderData.order_id,
          "handler": function (response) {
              const paymentData = {
                  razorpay_payment_id: response.razorpay_payment_id,
                  razorpay_order_id: response.razorpay_order_id,
                  razorpay_signature: response.razorpay_signature,
                  order_items: orderItems,
                  total: orderTotal,
                  customer_name: name,
                  customer_email: email
              };
              fetch('verify_payment.php', {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/json' },
                  body: JSON.stringify(paymentData)
              })
              .then(res => res.json())
              .then(data => {
                  if (data.success) {
                      localStorage.clear();
                      window.location.href = 'order-confirmed.php?code=' + data.order_code;
                  } else {
                      alert("Payment verification failed: " + data.message);
                      confirmBtn.disabled = false;
                      confirmBtn.textContent = 'Confirm Order';
                  }
              });
          },
          "prefill": { "name": name, "email": email },
          "theme": { "color": "#803D3B" }
      };
      var rzp1 = new Razorpay(options);
      rzp1.open();
      
      // --- THIS IS THE CRITICAL FIX FOR WHEN A USER CLOSES THE POP-UP ---
      rzp1.on('payment.failed', function (response){
          alert("Payment was cancelled or failed. Please try again.");
          // Re-enable the button so the user can try again without refreshing.
          confirmBtn.disabled = false;
          confirmBtn.textContent = 'Confirm Order';
      });
    }

    // --- THIS IS THE CRITICAL FIX FOR THE BROWSER BACK BUTTON ---
    // This event fires every time the page becomes visible, including on "back" navigation.
    window.addEventListener('pageshow', function(event) {
        // We reset the button to its original state, just in case it was left "stuck".
        if (confirmBtn.disabled) {
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Confirm Order';
        }
    });
  });
</script>
</body>
</html>