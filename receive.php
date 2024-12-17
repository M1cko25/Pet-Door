<?php
date_default_timezone_set('Asia/Manila');
session_start();
require_once("database.php");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $device = $_POST['device'];
    $stmt = $conn->prepare("SELECT * FROM `users` WHERE device = ?");
    $stmt->bind_param("s", $device);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $email = $row['email'];
        $userId = $row['id'];
        $username = $row['username'];
        $receive_date = date("Y-m-d");
        $receive_time = date("H:i:s");
        $desc = "Your pet got inside";
        $sql = $conn->prepare("INSERT INTO `logs`(`description`, `date`, `time`, `user_id`) VALUES (?, ?, ?, ?)");
        $sql->bind_param("sssi", $desc, $receive_date, $receive_time, $userId);
        $sql->execute();
    } else {
        echo json_encode(['status' => 'error']);
    }
}
?>