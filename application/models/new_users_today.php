<?php

$servername = "localhost";
$username = "root";
$password = "Around1234!@#$";
$dbname = "boloaaka";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT name, phone_number FROM `users` WHERE create_date >= CURDATE() AND is_verified = 1";
$result = $conn->query($sql);

echo "Verified Users: " . PHP_EOL;
if ($result->num_rows > 0) {
    // output data of each row
    while ($row = $result->fetch_assoc()) {
        echo "Name: " . $row["name"] . ", Phone: " . $row["phone_number"] . PHP_EOL;
    }
} else {
    echo "No new users added today";
}

$sql = "SELECT name, phone_number FROM `users` WHERE create_date >= CURDATE() AND is_verified = 0";
$result = $conn->query($sql);

echo PHP_EOL;
echo "Non-verified Users: " . PHP_EOL;
if ($result->num_rows > 0) {
    // output data of each row
    while ($row = $result->fetch_assoc()) {
        echo "Name: " . $row["name"] . ", Phone: " . $row["phone_number"] . PHP_EOL;
    }
} else {
    echo "No new non-verified users added today";
}

$conn->close();

?>