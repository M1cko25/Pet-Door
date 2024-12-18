<?php
session_start();
require_once("database.php");
if (!isset($_SESSION['email'])){
    header("Location: index.php");
    exit();
}
$date = isset($_GET['date']) ? $_GET['date'] : date("Y-m-d", time());
$logs = [];
if (isset($_GET['date'])) {
    $date = $_GET['date'];
    $stmt = $conn->prepare("SELECT * FROM `logs` WHERE user_id = ? && date = ?");
    $stmt->bind_param("sss", $_SESSION['user_id'], $date );
    $stmt->execute();
    $result = $stmt->get_result();
    $logs = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Home</title>
</head>
<body>
    <header class="w-full h-16 bg-gray-900 text-white flex justify-between items-center p-5">
        <h1 class="text-3xl font-bold">Pet Door</h1>
        <nav>
            <form action="logout.php" method="post">
                <button class="bg-blue-500 rounded text-white px-3 py-1" type="submit">Logout</button>
            </form>
        </nav>
    </header>
    <div class="w-screen h-screen flex flex-col gap-5 md:text-lg text-xs">
        <h1 class="text-3xl font-bold p-10">Logs</h1>
        <div class="w-screen flex flex-col justify-start items-center ">
            <div class="w-1/2">
                <form id="group" action="" method="get" class="flex row gap-2 justify-start items-center">
                    <label for="date">Group By:</label>
                    <input class="border border-black rounded md:p-2 p-0" type="date" name="date" onchange="groupSubmit()" id="date" value="<?php echo $date;?>"/>
                </form>
            </div>
            <table class="md:w-3/4 w-full shadow-lg">
                <tr class="w-full flex justify-between items-center p-1">
                    <th>Description</th>
                    <th>Date</th>
                    <th>Time</th>
                </tr>
                <?php foreach ($logs as $index => $log) { 
                    echo "<tr class='flex justify-between items-center ". ($index % 2 === 0 ? " bg-gray-100" : " bg-white")  ." p-1'>";
                    echo "<td>".$log['description']."</td>";
                    echo "<td>".$log['date']."</td>";
                    echo "<td>".$log['time']."</td>";
                    echo "</tr>";
                }?>
            </table>
        </div>
        
    </div>
    <script>
        function groupSubmit() {
            let form = document.querySelector('#group');
            form.submit();
        }
    </script>
</body>
</html>