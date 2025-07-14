<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Canteen Automation System</title>
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

    .hero {
      background-color: #AF8260;
      padding: 60px 20px;
      text-align: center;
      color: white;
    }

    .hero h2 {
      font-size: 36px;
      margin-bottom: 20px;
    }

    .hero p {
      font-size: 18px;
    }

    .features {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      margin: 40px auto;
      max-width: 1000px;
    }

    .feature {
      background-color: #803D3B;
      color: white;
      margin: 15px;
      padding: 25px;
      border-radius: 10px;
      flex: 1 1 300px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      transition: 0.3s;
    }

    .feature:hover {
      background-color: #AF8260;
    }

    .menu-section, .order-section {
      padding: 40px 20px;
      text-align: center;
    }

    .menu-section h2, .order-section h2 {
      margin-bottom: 20px;
    }

    .menu-images {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 20px;
      margin-top: 20px;
    }

    .menu-images .item {
      border: 1px solid #ccc;
      border-radius: 8px;
      padding: 10px;
      width: 200px;
      background-color: #fff;
    }

    .menu-images img {
      width: 100%;
      height: auto;
      border-radius: 8px;
    }

    .menu-images p {
      margin-top: 10px;
      font-weight: bold;
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
   
    
    .cart-icon {
      width: 28px;
      height: 28px;
      filter: invert(1);
      cursor: pointer;
    }
    h1 {
      color: #333;
      text-align: center;
      margin: 20px 0;
    }
    .menu,
    .order-summary {
      max-width: 1200px;
      margin: 20px auto;
      background: #ffffff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    }
    .menu-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
      gap: 20px;
    }
    .menu-item {
  background-color: #fafafa;
      padding: 10px;
      border-radius: 10px;
      text-align: center;
      background-color: #fafafa;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .menu-item:hover {
      transform: translateY(-3px);
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }
    .menu-item img {
      width: 100%;
      height: 120px;
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
      <a href="index.html">Home</a>
      <a href="menu.html">Menu</a>
      <a href="order.php">Order</a>
      <a href="order_details.php">Order Details</a>
      <a href="login.php">Login</a>
    </nav>
  </header>
  <div class="menu">
    <h2>Menu</h2>
    <div class="menu-grid" id="menuGrid"></div>
    <button onclick="placeOrder()">Place Order</button>
  </div>

  <div class="order-summary">
    <h2>Order Summary</h2>
    <div id="summary"></div>
    <div class="total" id="total">Total: ₹0</div>
  </div>

  <script>
    const items = [
      { id: 'burger', name: 'Burger', price: 50, image: 'https://cdn.pixabay.com/photo/2014/10/23/18/05/burger-500054_960_720.jpg' },
      { id: 'pizza', name: 'Pizza', price: 120, image: 'https://cdn.pixabay.com/photo/2017/12/09/08/18/pizza-3007395_960_720.jpg' },
      { id: 'coffee', name: 'Coffee', price: 30, image: 'https://tse4.mm.bing.net/th?id=OIP.4WUjqZXl4tBdfKEwgN9FKAHaE7&pid=Api&P=0&h=180' },
      { id: 'upma', name: 'Upma', price: 40, image: 'https://tse4.mm.bing.net/th?id=OIP.boBBw90ShLIHl5l9pQvbQgHaE7&pid=Api&P=0&h=180' },
      { id: 'idli', name: 'Idli', price: 30, image: 'https://static.toiimg.com/photo/68631114.cms' },
      { id: 'dosa', name: 'Dosa', price: 60, image: 'https://media.cntraveller.in/wp-content/uploads/2020/05/dosa-recipes-1366x768.jpg' },
      { id: 'samosa', name: 'Samosa', price: 15, image: 'https://cdn.pixabay.com/photo/2024/02/04/20/02/ai-generated-8553025_1280.jpg' },
      { id: 'pasta', name: 'Pasta', price: 90, image: 'https://images.pexels.com/photos/1438672/pexels-photo-1438672.jpeg?cs=srgb&dl=food-photography-of-pasta-1438672.jpg&fm=jpg' },
      { id: 'noodles', name: 'Noodles', price: 80, image: 'https://tse4.mm.bing.net/th?id=OIP.bAi4BAmtk6OyVLcGA1fu0wHaHa&pid=Api&P=0&h=180' },
      { id: 'vada', name: 'Vada', price: 25, image: 'https://images.slurrp.com/prod/recipe_images/transcribe/breakfast/Medu-Vada.webp' },
      { id: 'tea', name: 'Tea', price: 10, image: 'https://tse2.mm.bing.net/th?id=OIP.HqjHWcbGojYGGDJBu9ETJQHaEK&pid=Api&P=0&h=180' },
      { id: 'juice', name: 'Juice', price: 35, image: 'https://images.ctfassets.net/yixw23k2v6vo/2oidT8ZVeVYwE7L7ksr0hE/614f7592570de77b4a0260e5c80c113a/large-GettyImages-825882916-3000x2000.jpg' },
      { id: 'pavbhaji', name: 'Pav Bhaji', price: 50, image: 'https://www.thestatesman.com/wp-content/uploads/2019/07/pav-bhaji.jpg' },
      { id: 'poha', name: 'Poha', price: 25, image: 'https://st2.depositphotos.com/5653638/11810/i/450/depositphotos_118105520-stock-photo-poha-or-aalu-poha-or.jpg' },
      { id: 'chapati', name: 'Chapati', price: 15, image: 'https://recipes.timesofindia.com/thumb/61203720.cms?imgsize=670417&width=800&height=800' },
      { id: 'paratha', name: 'Paratha', price: 35, image: 'https://www.whiskaffair.com/wp-content/uploads/2020/06/Lachha-Paratha-2-1.jpg' },
      { id: 'friedrice', name: 'Fried Rice', price: 70, image: 'https://wallpaperaccess.com/full/2175404.jpg' },
      { id: 'springrolls', name: 'Spring Rolls', price: 60, image: 'https://wallpaperaccess.com/full/6905828.jpg' },
      { id: 'cutlet', name: 'Cutlet', price: 30, image: 'https://1.bp.blogspot.com/-8MPWfPnCZTc/VB1Y40TRuqI/AAAAAAAAAPY/XluQZxXKKng/s1600/Vegetable-cutlet.jpg' },
      { id: 'momos', name: 'Momos', price: 45, image: 'https://www.holidify.com/images/cmsuploads/compressed/8341992509_906d197b80_k_20190912175503.jpg' }
    ];

    const menuGrid = document.getElementById('menuGrid');
    items.forEach(item => {
      menuGrid.innerHTML += `
        <div class="menu-item">
          <img src="${item.image}" alt="${item.name}">
          <p>${item.name} - ₹${item.price}</p>
          <input type="number" id="${item.id}" min="0" value="0">
        </div>
      `;
    });

    const prices = Object.fromEntries(items.map(i => [i.id, i.price]));

    function placeOrder() {
      const summaryDiv = document.getElementById("summary");
      summaryDiv.innerHTML = "";
      let total = 0;

      for (let item in prices) {
        let quantity = parseInt(document.getElementById(item).value);
        if (quantity > 0) {
          let cost = quantity * prices[item];
          summaryDiv.innerHTML += `<p>${item.charAt(0).toUpperCase() + item.slice(1)} x ${quantity} = ₹${cost}</p>`;
          total += cost;
        }
      }
      document.getElementById("total").innerText = `Total: ₹${total}`;
    }
  </script>
</body>
</html>