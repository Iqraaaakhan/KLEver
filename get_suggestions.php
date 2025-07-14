<?php
header('Content-Type: application/json');
$mysqli = new mysqli("localhost", "root", "", "klever_db");

if ($mysqli->connect_error) {
    echo json_encode([]);
    exit();
}

$q = isset($_GET['q']) ? $mysqli->real_escape_string($_GET['q']) : '';

if (!$q) {
    echo json_encode([]);
    exit();
}

$sql = "SELECT name, image_url AS image, price FROM full_menu 
        WHERE name LIKE '%$q%' AND is_available = 1 LIMIT 6";


$result = $mysqli->query($sql);

$suggestions = [];

if ($result && $result->num_rows > 0) {
   // ----- AFTER (This is the correct code) -----
while ($row = $result->fetch_assoc()) {
    // The path modification line has been removed.
    $suggestions[] = $row;
}
}

echo json_encode($suggestions);
?>
