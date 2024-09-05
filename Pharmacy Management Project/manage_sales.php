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

$pharmacist_id_query = "SELECT pharmacist_id FROM pharmacist WHERE email='$email'";
$pharmacist_id_result = $conn->query($pharmacist_id_query);
$pharmacist_id_row = $pharmacist_id_result->fetch_assoc();
$pharmacist_id = $pharmacist_id_row['pharmacist_id'];

// Handle adding new sale
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_sale'])) {
    $medicine_id = $_POST['medicine_id'];
    $customer_id = $_POST['customer_id'];
    $transaction_id = $_POST['transaction_id'];
    $no_of_units_sold = $_POST['no_of_units_sold'];



    $insert_sql = "INSERT INTO sales (medicine_id, customer_id, transaction_id, no_of_units_sold, pharmacist_id) VALUES ('$medicine_id', '$customer_id', '$transaction_id', '$no_of_units_sold', '$pharmacist_id')";
    if ($conn->query($insert_sql) === TRUE) {
        // Redirect to refresh the page
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } else {
        echo "Error: " . $insert_sql . "<br>" . $conn->error;
    }
}

// Retrieve sales list
$sql_sales = "SELECT * FROM sales WHERE pharmacist_id='$pharmacist_id'";
$result_sales = $conn->query($sql_sales);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sales - <?php echo $firstname; ?></title>
    <link rel="stylesheet" href="sales1.css">
</head>
<body>
    <div class="sidebar">
        <h2>Welcome, <?php echo $firstname; ?></h2>
        <div id="actions_btn1" class="action_btn">
            <form action="dashboard.php" method="post">
                <button type="submit" name="submit1">My dashboard</button><br>
            </form>
        </div>

        <!-- Other sidebar avigation links -->

        <div id="actions_btn5" class="action_btn">
            <form action="logout.php" method="post">
                <button type="submit" name="logout">Logout</button><br>
            </form>
        </div>
    </div>

    <div id="manage_sales" class="manage_sales_container">
        <h3>Manage Sales</h3>
        
        <!-- Form to add new sale -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="transaction_id">Transaction ID:</label><br>
            <input type="text" id="transaction_id" name="transaction_id" required><br><br>
            <label for="medicine_id">Medicine ID:</label><br>
            <input type="text" id="medicine_id" name="medicine_id" required><br><br>

            <label for="customer_id">Customer ID:</label><br>
            <input type="text" id="customer_id" name="customer_id" required><br><br>

          

            <label for="no_of_units_sold">Number of Units Sold:</label><br>
            <input type="text" id="no_of_units_sold" name="no_of_units_sold" required><br><br>

            <button type="submit" name="add_sale">Add Sale</button>
        </form>

        <!-- Display Sales List Table Here -->
        <h4>Sales List</h4>
        <table>
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Medicine ID</th>
                    <th>Customer ID</th>
                    <th>Number of Units Sold</th>
                    <th>Sale Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_sales->num_rows > 0) {
                    while ($row_sale = $result_sales->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row_sale['transaction_id'] . "</td>";
                        echo "<td>" . $row_sale['medicine_id'] . "</td>";
                        echo "<td>" . $row_sale['customer_id'] . "</td>";
                        echo "<td>" . $row_sale['no_of_units_sold'] . "</td>";
                        echo "<td>" . $row_sale['sale_date'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No sales found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
