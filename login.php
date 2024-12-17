<?php 
    session_start();
    require_once("database.php");
    if (isset($_SESSION['email'])){
        header("Location: home.php");
        exit();
    }
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    $wrongPassword = false;
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = hash("sha256", $_POST['password']);
        $stmt = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['password'] == $password) {
                session_regenerate_id();
                $_SESSION['email'] = $email;
                $_SESSION['user_id'] = $row['id'];
                header("Location: home.php");
            } else {
                $wrongPassword = true;
            }
        } else {
            $wrongPassword = true;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Login</title>
</head>
<body>
    <div class="w-screen h-screen flex justify-center items-center">
    <div class=" min-h-screen flex items-center justify-center">
    <div class="relative z-10 bg-gray-900 p-8 rounded-md shadow-lg text-white">
        <h1 class="text-xl font-bold mb-4">Login</h1>
        <form action="" method="POST">
            <div class="mb-4">
                <label class="block font-bold mb-2" for="email">Email</label>
                <input
                    class="appearance-none border rounded-md py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full"
                    id="email" type="email" name="email" placeholder="Email">
            </div>
            <div class="mb-4">
                <label class="block font-bold mb-2" for="password">Password</label>
                <input
                    class="appearance-none border rounded-md py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full"
                    id="password" type="password" name="password" placeholder="Password">
            </div>
            <div class="flex items-center justify-between gap-8">
                <button
                    class="bg-cyan-500 hover:bg-cyan-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    type="submit">
                    Sign In
                </button>
                <p>Doesn't have an account? <a class="inline-block align-baseline font-bold text-sm text-cyan-500 hover:text-cyan-800"
                    href="index.php">
                    Sign Up
                </a></p>
            </div>
            <?php if ($wrongPassword) { echo "<p class='text-red-500'>Wrong email or password</p>"; } ?>
        </form>
    </div>
</div>
    </div>
</body>
</html>