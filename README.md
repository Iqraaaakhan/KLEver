# KLEver - A Smart Canteen Automation System

This is a complete, end-to-end web application designed to modernize the canteen experience at KLE Technological University. It provides a seamless ordering and payment process for students and a powerful management dashboard for canteen administrators.

---

## Key Features

### For Customers:
*   **Secure User Authentication:** Passwordless, OTP-based login and registration system using email.
*   **Dynamic Food Menu:** A searchable, user-friendly menu that only displays items currently available.
*   **Live Search Suggestions:** Zomato-style live search bar on the homepage.
*   **Complete Ordering System:** A persistent shopping cart and a streamlined checkout process.
*   **Real Payment Integration:** Secure online payments powered by Razorpay, accepting UPI, cards, and more.
*   **Personal Account Hub:** A dedicated "My Orders" page for users to view their order history and track the status of current orders.
*   **Convenient Reordering:** A "Reorder" button allows users to instantly add items from a past order to their cart.

### For Admins:
*   **Secure Admin Login:** Separate, password-protected login for administrators.
*   **Real-Time Dashboard:** A live-updating dashboard with sound and banner notifications for new orders.
*   **Business Analytics:** At-a-glance stat cards showing daily sales, order counts, top-selling items, and total users.
*   **Full Menu Management (CRUD):** Admins can add, view, update, and "soft delete" (deactivate) menu items.
*   **Order Management:** A comprehensive view of all orders, with the ability to update the status (Pending, Preparing, Completed).
*   **Historical Reports:** A dedicated reports page with a 30-day sales chart and a "Top Customers" leaderboard.

---

## Built With

*   **Backend:** PHP
*   **Frontend:** HTML, CSS, JavaScript
*   **Database:** MySQL
*   **Key Libraries:**
    *   PHPMailer (for OTP emails)
    *   Razorpay (for payments)
    *   Chart.js (for reports)

---

## Getting Started

To get a local copy up and running, follow these simple steps.

### Prerequisites

*   A local server environment like XAMPP or MAMP.
*   [Composer](https://getcomposer.org/) installed for managing PHP dependencies.

### Installation

1.  **Clone the repo:**
    ```sh
    git clone https://github.com/Iqraaaakhan/KLEver.git
    ```
2.  **Set up the database:**
    *   Create a new MySQL database named `klever_db`.
    *   Import the `Klever_db.sql` file into your new database.
3.  **Install PHP dependencies:**
    ```sh
    composer install
    ```
4.  **Configure API Keys:**
    *   Add your Razorpay Test API keys to `cart.php`, `create_razorpay_order.php`, and `verify_payment.php`.
    *   Add your Gmail App Password to `login.php` and `register.php` for PHPMailer.