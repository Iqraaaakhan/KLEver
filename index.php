<?php
// THIS MUST BE THE VERY FIRST LINE OF THE FILE.
// We only need to start the session once. The header will use this same session.
session_start(); 

// =======================================================
//  DATABASE CONNECTION 
// =======================================================
$db_host = 'localhost';
$db_user = 'root';
$db_pass = ''; // Your MySQL password here
$db_name = 'klever_db';
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Database Connection failed: " . $conn->connect_error);
}

// =======================================================
//  FETCH SPECIALS QUERY
// =======================================================
$specials_query = "SELECT * FROM menu_items WHERE is_special = 1 AND is_available = 1 
                    ORDER BY 
                        CASE 
                            WHEN name = 'Classic Veg Biryani' THEN 0 
                            WHEN name = 'Spicy Paneer Wrap'   THEN 1 
                            WHEN name = 'Fruit Smoothie'      THEN 2 
                            WHEN name = 'Masala Dosa'         THEN 3 
                            WHEN name = 'Gobi Manchuri'       THEN 4 
                            WHEN name = 'Puri Bhaji'          THEN 5 
                            ELSE 99 
                        END 
                    LIMIT 6";
$specials_result = $conn->query($specials_query);

// =======================================================
//  FLASH MESSAGE LOGIC
// =======================================================
$flash_message = '';
if (isset($_SESSION['flash_message'])) {
    $flash_message = $_SESSION['flash_message'];
    // Unset the flash message so it only shows once
    unset($_SESSION['flash_message']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>KLEver – Smart Canteen Automation</title>
    
    <!-- Your <link> tags for fonts and CSS go here -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="style.css"/>
</head>
<body>

    <?php 
    // THIS IS THE ONE AND ONLY PLACE THE HEADER SHOULD BE INCLUDED.
    include 'header.php'; 
    ?>
    <section class="hero-section">

  <div class="swiper-container hero-slider">
      <div class="swiper-wrapper">
          <div class="swiper-slide" style="background-image: url('images/carousel-1.jpg');"></div>
          <div class="swiper-slide" style="background-image: url('images/carousel-2.jpg');"></div>
          <div class="swiper-slide" style="background-image: url('images/carousel-3.jpg');"></div>
      </div>
      <div class="swiper-button-prev"></div>
      <div class="swiper-button-next"></div>
  </div>
  
  <div class="hero-content">
      <h2>Smart & Easy Food Ordering</h2>
      <p>Because standing in line for food isn’t a skill you can add to your Resume...!</p>
      
      <!-- We will now anchor the suggestions box to the form itself -->
      <form action="menu.php" method="GET" class="hero-search-form" style="position: relative;">
          <div class="hero-search-container">
              <input type="text" name="search_query" id="searchInput" placeholder="What are you craving today?" autocomplete="off">
              <button type="submit"><i class="fas fa-search"></i></button>
          </div>
          
          <!-- ✅ THE SUGGESTIONS BOX IS NOW A DIRECT CHILD OF THE POSITIONED FORM -->
          <div id="suggestions-box"></div>
      </form>
      
      <a href="menu.php" class="btn-secondary">Browse Menu →</a>
  </div>
</section>
<div class="container">
        <?php if ($flash_message): ?>
            <div class="flash-message">
                <?php echo $flash_message; ?>
            </div>
        <?php endif; ?>
    </div>
  <section class="features-section-new">
    <div class="container">
        <div class="feature-item-new">
            <i class="fas fa-mobile-alt"></i>
            <h3>Order Anywhere</h3>
            <p>Use your phone or desktop to order in seconds.</p>
        </div>
        <div class="feature-item-new">
            <i class="fas fa-credit-card"></i>
            <h3>Easy Payments</h3>
            <p>Securely pay online with UPI, cards, or wallets.</p>
        </div>
        <div class="feature-item-new">
            <i class="fas fa-clock"></i>
            <h3>Save Time</h3>
            <p>No more long queues. Pick up your order on time.</p>
        </div>
    </div>
  </section>

  <section class="specials container">
    <h2>Today's Specials</h2>
    <div class="menu-cards">
      <?php
        if ($specials_result && $specials_result->num_rows > 0) {
            while ($item = $specials_result->fetch_assoc()) {
                echo '<div class="card">';
                echo '  <img src="' . htmlspecialchars($item['image_url']) . '" alt="' . htmlspecialchars($item['name']) . '">';
                echo '  <h4>' . htmlspecialchars($item['name']) . '</h4>';
                echo '  <p>Just ₹' . htmlspecialchars(number_format($item['price'], 2)) . '</p>';
                echo '  <a href="order.php?item_id=' . $item['id'] . '" class="btn-card-order">Order Now</a>'; //BUTTON
                echo '</div>';
            }
        } else {
            echo '<p>No specials are available today. Check back later!</p>';
        }
      ?>
    </div>
  </section>

  <footer>
    <div class="container">
        <p>© <?php echo date("Y"); ?> KLEver Canteen Automation • Contact: +91‑12345‑67890 • vidyanagar, Hubballi</p>
        <div class="social">
            <a href="#">Facebook</a> • 
            <a href="#">Instagram</a>
        </div>
    </div>
  </footer>
  
  <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
  <script>
    var swiper = new Swiper('.hero-slider', {
      loop: true,
      effect: 'fade',
      fadeEffect: { crossFade: true },
      autoplay: { delay: 2500, disableOnInteraction: false },
      // ADD THIS NAVIGATION OBJECT
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },

    });
  </script>

    <!-- Add this new script tag before </body> -->
  <script>
    const header = document.querySelector('.main-header');
    window.addEventListener('scroll', () => {
      if (window.scrollY > 50) { // When scrolled more than 50 pixels
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
    });
  </script>
    <!-- In index.php, replace the IntersectionObserver script with this -->
  <script>
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('show');
        }
      });
    }, { 
      rootMargin: '-80px' // ✅ THIS IS THE FIX: Start animation earlier
    });

    const hiddenElements = document.querySelectorAll('.feature-item-new, .card');
    hiddenElements.forEach((el) => observer.observe(el));
  </script>

<!-- In index.php, replace the entire live search script with this -->
<script>
document.addEventListener("DOMContentLoaded", () => {
  const searchForm = document.querySelector('.hero-search-form'); // Target the form
  const input = document.getElementById("searchInput");
  const suggestionBox = document.getElementById("suggestions-box");

  input.addEventListener("input", async () => {
    const query = input.value.trim();

    if (query.length < 1) { // Hide if empty or just one character
      suggestionBox.style.display = "none";
      return;
    }

    try {
      const res = await fetch(`get_suggestions.php?q=${encodeURIComponent(query)}`);
      const data = await res.json();

      if (data.length > 0) {
        // ✅ THIS IS THE UPDATED HTML STRUCTURE
        suggestionBox.innerHTML = data.map(item => `
          <div class="suggestion-item" onclick="window.location.href='menu.php?search_query=${encodeURIComponent(item.name)}'">
            <img class="suggestion-item__image" src="${item.image}" alt="${item.name}" />
            <div class="suggestion-item__info">
              <span class="suggestion-item__name">${item.name}</span>
              <span class="suggestion-item__price">₹${parseFloat(item.price).toFixed(2)}</span>
            </div>
          </div>
        `).join('');
        suggestionBox.style.display = "block";
      } else {
        suggestionBox.innerHTML = "<div class='no-suggestions'>No items found matching your search.</div>";
        suggestionBox.style.display = "block";
      }
    } catch (err) {
      console.error("Error fetching suggestions:", err);
      suggestionBox.innerHTML = "<div class='no-suggestions'>Could not fetch results.</div>";
      suggestionBox.style.display = "block";
    }
  });

  // Hide suggestions when clicking outside the search form
  document.addEventListener("click", (e) => {
    if (!searchForm.contains(e.target)) {
      suggestionBox.style.display = "none";
    }
  });

  // Also hide suggestions if the user clicks the browser's back/forward buttons
  window.addEventListener('pageshow', () => {
    suggestionBox.style.display = "none";
    input.value = '';
  });
});
</script>

</body>
</html> 
