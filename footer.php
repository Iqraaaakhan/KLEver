<footer>
    <div class="container">
        <!-- Your existing copyright and social links -->
        <p>© <?php echo date("Y"); ?> KLEver Canteen Automation • Contact: +91‑12345‑67890 • vidyanagar, Hubballi</p>
        <div class="social">
            <a href="#">Facebook</a> • 
            <a href="#">Instagram</a> • 
            <!-- THIS IS THE NEW, DISCREET ADMIN LINK -->
            <a href="admin_login.php" class="admin-link">Admin</a>
        </div>
    </div>
</footer>

<!-- This is an optional but recommended style for the link -->
<style>
    .admin-link {
        opacity: 0.6; /* Makes it slightly faded */
        transition: opacity 0.3s ease;
    }
    .admin-link:hover {
        opacity: 1; /* Becomes fully visible on hover */
    }
</style>