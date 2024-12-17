<?php
session_start();
date_default_timezone_set('Asia/Manila');

require_once("database.php");
if (isset($_SESSION['email'])){
    header("Location: home.php");
    exit();
}
$error = "";
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
if (isset($_POST['email']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = hash("sha256", $_POST['password']);
    $device = $_POST['device'];
    $fetch = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
    $fetch->bind_param("s", $email);
    $fetch->execute();
    $result = $fetch->get_result();
    if ($result->num_rows > 0) {
        $error = "email already exists";
    } else {
        if (strlen($_POST['password'] < 8)) {
            $error = "password must have 8 or more characters";
        } else {
            $stmt = $conn->prepare("INSERT INTO `users`(`username`, `email`, `password`, `device`) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $password, $device);
            if ($stmt->execute()){
                session_regenerate_id();
                $_SESSION['email'] = $email;
                $_SESSION['user_id'] = $row['id'];
                header("Location: home.php");
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Sign Up</title>
</head>
<body>
    <div class="w-screen h-screen flex justify-center items-center">
        <div class="w-96 rounded-lg shadow-lg p-5 bg-gray-900 text-white">
        <h2 class="text-2xl font-bold pb-5">SignUp</h2>
        <form action="" method="POST">
            <div class="mb-4">
            <label for="name" class="block mb-2 text-sm font-medium">Your name</label>
            <input
                type="text"
                id="name"
                name="username"
                class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full py-2.5 px-4"
                placeholder="Your Name"
                required
                value=""
            >
            </div>
            <div class="mb-4">
            <label for="email" class="block mb-2 text-sm font-medium">Your email</label>
            <input
                type="email"
                id="email"
                name="email"
                class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full py-2.5 px-4"
                placeholder="Email"
                required
                value=""
            >
            </div>
            <div class="mb-4">
            <label for="device" class="block mb-2 text-sm font-medium">Your Device Id</label>
            <input
                type="text"
                id="device"
                name="device"
                class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full py-2.5 px-4"
                placeholder="Device Id"
                required
                value=""
            >
            </div>
            <div class="mb-4">
            <label for="password" class="block mb-2 text-sm font-medium">Your password</label>
            <input
                type="password"
                id="password"
                name="password"
                class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full py-2.5 px-4"
                placeholder="Password"
                required
                value=""
            >
            </div>
            <div>
            <p class="text-red-500 pb-5"></p>
            </div>
            <div class="flex items-center justify-between mb-4">
            <button
                type="submit"
                class="text-white bg-purple-600 hover:bg-purple-700 focus:ring-2 focus:ring-blue-300 font-medium rounded-lg text-sm py-2.5 px-5 w-full sm:w-auto"
            >
                Register
            </button>
            <div class="flex items-center text-sm">
                <p>Already have an account?</p>
                <a href="login.php" class="cursor-pointer ml-1 font-bold text-sm text-cyan-500 hover:text-cyan-800 ">Sign in</a>
            </div>
            </div>
            <p class="text-red-600"><?php
                echo $error;
                ?></p>
        </form>
        </div>
    </div>
</body>
</html>