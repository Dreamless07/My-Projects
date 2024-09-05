<?php
session_start();

// Check if user is not logged in, redirect to login page
if (!isset($_SESSION['email'])) {
    header("location: login.php");
    exit;
}

include 'db.php';

$email = $_SESSION['email'];

// Retrieve pharmacist details
$sql = "SELECT firstname FROM pharmacist WHERE email='$email'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$firstname = $row['firstname'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo $firstname; ?></title>
    <link rel="stylesheet" href="dashboard1.css">
</head>
<body>
    <div class="sidebar">
        <h2>Welcome, <?php echo $firstname; ?></h2>
        <div id="actions_btn1" class="action_btn">
            <form action="manage_medicines.php" method="post">
                <button type="submit" name="submit1">Manage Medicines</button><br>
            </form>
        </div>

        <div id="actions_btn2" class="action_btn">
            <form action="manage_customer.php" method="post">
                <button type="submit" name="submit2">Manage Customers</button><br>
            </form>
        </div>

        <div id="actions_btn3" class="action_btn">
            <form action="manage_transaction.php" method="post">
                <button type="submit" name="submit3">Manage Transactions</button><br>
            </form>
        </div>

        <div id="actions_btn4" class="action_btn">
            <form action="manage_sales.php" method="post">
                <button type="submit" name="submit4">Manage Sales</button><br>
            </form>
        </div>

        <div id="actions_btn5" class="action_btn">
            <form action="logout.php" method="post">
                <button type="submit" name="logout">Logout</button><br>
            </form>
        </div>
    </div>

    <div id="dash_welcome" class="Dashboard_welcome">
        <h3>Welcome to the Dashboard</h3>
        <p>Here you can manage medicines, customers, transactions, and sales.</p>
    </div>
</body>
</html>
