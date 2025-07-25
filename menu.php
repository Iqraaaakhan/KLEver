<?php // menu.php - FINAL CORRECTED VERSION

// =======================================
// DATABASE CONNECTION
// =======================================
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'klever_db';
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Database Connection failed: " . $conn->connect_error);
}

// =======================================
// FETCH MENU ITEMS (WITH CORRECTED LOGIC)
// =======================================
$search_term = '';
// This is the base query. It is now correct for ALL cases.
$menu_query = "SELECT * FROM products WHERE is_available = 1 AND is_active = 1";

// Check if a search query has been sent
if (isset($_GET['search_query']) && !empty($_GET['search_query'])) {
    $search_term = $conn->real_escape_string($_GET['search_query']);
    // If there is a search term, we simply ADD the search condition to the end.
    $menu_query .= " AND name LIKE '%$search_term%'";
}

// Now the query is always correct, whether there is a search or not.
$menu_result = $conn->query($menu_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Menu – Canteen Automation</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="style.css" rel="stylesheet">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f1ee;
      margin: 0;
      padding: 0;
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

    .menu {
      max-width: 1200px;
      margin: 40px auto;
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    }

    .menu h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    .menu-grid {
     display: grid;
     grid-template-columns: repeat(3, 1fr); /* Fixed 3 columns */
     gap: 20px;
    }


    .menu-item {
      background-color: #fafafa;
      padding: 10px;
      border-radius: 10px;
      text-align: center;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .menu-item:hover {
      transform: translateY(-3px);
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    .menu-item img {
     width: 100%;
     height: 180px; 
     object-fit: cover; 
     border-radius: 8px;
   } 


    .menu-item input {
      width: 60px;
      text-align: center;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 5px;
      padding: 3px;
    }

    .order-summary {
      max-width: 1200px;
      margin: 20px auto;
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    }

    button {
      margin-top: 20px;
      padding: 10px 25px;
      background-color: #6b4f4f;
      color: white;
      font-weight: bold;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: #523c3c;
    }

    .total {
      font-weight: bold;
      text-align: right;
      margin-top: 10px;
      font-size: 18px;
      color: #444;
    }
  </style>
</head>
<body>
<header>
  <h1 style="color: #E4C59E;">KLEver</h1>
  <nav>
    <a href="index.php">Home</a>
    <a href="menu.php" class="active">Menu</a>
    <a href="order.php">Order</a>
    <a href="login.php">Login</a>
  </nav>
</header>

<div class="menu">
  <h2></h2>
  <?php if (!empty($search_term)): ?>
  <p class="search-result-info">
    Showing results for: <strong>"<?php echo htmlspecialchars($search_term); ?>"</strong>
  </p>
<?php endif; ?>
  <div class="menu-grid" id="menuGrid">
    <?php
    $menuItemsJS = [];
    if ($menu_result && $menu_result->num_rows > 0) {
        while ($item = $menu_result->fetch_assoc()) {
            $itemId = 'item_' . $item['id'];
            echo '<div class="menu-item">';
            echo '  <img src="' . htmlspecialchars($item['image_url']) . '" alt="' . htmlspecialchars($item['name']) . '">';
            echo '  <p>' . htmlspecialchars($item['name']) . ' - ₹' . number_format($item['price'], 2) . '</p>';
            echo '  <input type="number" id="' . $itemId . '" min="0" value="0">';
            echo '</div>';

            $menuItemsJS[] = [
                'id' => $itemId,
                'name' => $item['name'],
                'price' => $item['price']
            ];
        }
    } else {
        echo '<p>No items found.</p>';
    }
    ?>
  </div>
  <button onclick="placeOrder()">Confirm Items</button>
</div>

<div class="order-summary">
  <h2>Order Summary</h2>
  <div id="summary"></div>
  <div class="total" id="total">Total: ₹0</div>
</div>
<?php include 'footer.php'; ?>
<script>
  const items = <?php echo json_encode($menuItemsJS); ?>;

  function placeOrder() {
    const summaryDiv = document.getElementById("summary");
    summaryDiv.innerHTML = "";
    let total = 0;
    let orderItems = [];

    items.forEach(item => {
      let quantity = parseInt(document.getElementById(item.id).value);
      if (quantity > 0) {
        let cost = quantity * item.price;
        summaryDiv.innerHTML += `<p>${item.name} x ${quantity} = ₹${cost.toFixed(2)}</p>`;
        total += cost;
        
        // --- THIS IS THE CRITICAL UPGRADE ---
        // We now find the original item to get its image URL and add it to the object we save.
        const originalItem = <?php echo json_encode($menu_result->fetch_all(MYSQLI_ASSOC)); ?>.find(i => 'item_' + i.id === item.id);
        
        orderItems.push({
          id: item.id,
          name: item.name,
          quantity: quantity,
          price: item.price,
          cost: cost,
          image: originalItem ? originalItem.image_url : '' // Add the image URL
        });
      }
    });

    document.getElementById("total").innerText = `Total: ₹${total.toFixed(2)}`;
    localStorage.setItem("orderItems", JSON.stringify(orderItems));
    localStorage.setItem("orderTotal", total);

    // This part for creating the "Go to Cart" button remains the same.
    if (document.querySelector('#summary button')) return; // Prevent multiple buttons
    const cartButton = document.createElement("button");
    cartButton.innerHTML = '🛒 Go to Cart';
    cartButton.style.marginTop = "10px";
    cartButton.onclick = () => {
      window.location.href = 'cart.php';
    };
    summaryDiv.appendChild(cartButton);
  }
</script>

<?php $conn->close(); ?>
</body>
</html>
