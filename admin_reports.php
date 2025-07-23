<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'klever_db');
if ($conn->connect_error) { 
    die("Connection Failed: " . $conn->connect_error); 
}

// --- DATA FOR SALES CHART ---
// Fetch the last 30 days of summaries, ensuring we have a complete date range.
$sales_result = $conn->query(
    "SELECT summary_date, total_sales 
     FROM daily_summary 
     WHERE summary_date >= CURDATE() - INTERVAL 30 DAY 
     ORDER BY summary_date ASC"
);

$chart_labels = [];
$chart_data = [];
while($row = $sales_result->fetch_assoc()) {
    // Format the date nicely for the chart label (e.g., "Jul 23")
    $chart_labels[] = date("M j", strtotime($row['summary_date']));
    $chart_data[] = $row['total_sales'];
}

// --- DATA FOR TOP CUSTOMERS ---
$top_customers_result = $conn->query(
    "SELECT u.username, SUM(o.total) as total_spent, COUNT(o.id) as order_count
     FROM orders o
     JOIN user u ON o.user_id = u.id
     GROUP BY o.user_id
     ORDER BY total_spent DESC
     LIMIT 5"
);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reports - KLEver Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="admin_dashboard_styles.css"> 
</head>
<body>
<div class="admin-wrapper">
    
    <?php include 'admin_sidebar.php'; ?>

    <div class="main-content">
        <div class="header-bar">
            <h1>Reports & Analytics</h1>
        </div>
        
        <div class="reports-grid">
            <!-- Card for the Sales Chart -->
            <div class="report-card chart-container">
                <h3>Sales Over Last 30 Days</h3>
                <canvas id="salesChart"></canvas>
            </div>

            <!-- Card for the Top Customers List -->
            <div class="report-card top-customers-list">
                <h3>Top 5 Customers</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Orders</th>
                            <th>Total Spent</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($top_customers_result->num_rows > 0): ?>
                            <?php while($customer = $top_customers_result->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($customer['username']); ?></strong></td>
                                    <td><?php echo $customer['order_count']; ?></td>
                                    <td>₹<?php echo number_format($customer['total_spent'], 2); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="3">No customer data available yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Include the Chart.js library from a CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('salesChart').getContext('2d');
    
    const salesChart = new Chart(ctx, {
        type: 'line', // We are creating a line chart
        data: {
            // Use PHP to inject the dates we fetched from the database
            labels: <?php echo json_encode($chart_labels); ?>,
            datasets: [{
                label: 'Daily Sales (₹)',
                // Use PHP to inject the sales data
                data: <?php echo json_encode($chart_data); ?>,
                backgroundColor: 'rgba(40, 167, 69, 0.1)', // A light green fill under the line
                borderColor: 'rgba(40, 167, 69, 1)', // A solid green line
                borderWidth: 2,
                tension: 0.3, // Makes the line slightly curved
                pointBackgroundColor: 'rgba(40, 167, 69, 1)'
            }]
        },
        options: {
            responsive: true, // Makes the chart responsive
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        // Format the y-axis labels to include the Rupee symbol
                        callback: function(value, index, values) {
                            return '₹' + value;
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false // We don't need a legend for a single dataset
                }
            }
        }
    });
});
</script>

</body>
</html>