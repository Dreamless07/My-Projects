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

// Handle adding new customer
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_customer'])) {
    $customer_name = $_POST['customer_name'];
    $gender = $_POST['gender'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];

    $insert_sql = "INSERT INTO customer (customer_name, gender, phone_number, address, pharmacist_id) VALUES ('$customer_name', '$gender', '$phone_number', '$address', '$pharmacist_id')";
    if ($conn->query($insert_sql) === TRUE) {
        // Redirect to refresh the page
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } else {
        echo "Error: " . $insert_sql . "<br>" . $conn->error;
    }
}

// Retrieve customer list
$sql_customers = "SELECT * FROM customer WHERE pharmacist_id='$pharmacist_id'";
$result_customers = $conn->query($sql_customers);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Customers - <?php echo $firstname; ?></title>
    <link rel="stylesheet" href="customer1.css">
</head>
<body>
    <div class="sidebar">
        <h2>Welcome, <?php echo $firstname; ?></h2>
        <div id="actions_btn1" class="action_btn">
            <form action="dashboard.php" method="post">
                <button type="submit" name="submit1">My dashboard</button><br>
            </form>
        </div>

        <!-- Other sidebar navigation links -->

        <div id="actions_btn1" class="action_btn">
            <form action="logout.php" method="post">
                <button type="submit" name="submit1">Logout</button><br>
            </form>
        </div>
    </div>

    <div id="manage_customers" class="manage_customers_container">
        <h3>Manage Customers</h3>
        
        <!-- Form to add new customer -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="customer_name">Customer Name:</label><br>
                <input type="text" id="customer_name" name="customer_name" required><br><br>
            </div>

            <div class="form-group">
                <label>Gender:</label><br>
                <input type="radio" id="male" name="gender" value="Male" required>
                <label for="male">Male</label>
                <input type="radio" id="female" name="gender" value="Female" required>
                <label for="female">Female</label><br><br>
            </div>

            <div class="form-group">
                <label for="phone_number">Phone Number:</label><br>
                <input type="tel" id="phone_number" name="phone_number" required><br><br>
            </div>

            <div class="form-group">
                <label for="address">Address:</label><br>
                <textarea id="address" name="address" required></textarea><br><br>
            </div>

            <button type="submit" name="add_customer">Add Customer</button>
        </form>

        <!-- Display Customer List Table Here -->
        <h4>Customer List</h4>
        <table>
            <thead>
                <tr>
                    <th>Customer Id</th>
                    <th>Customer Name</th>
                    <th>Gender</th>
                    <th>Phone Number</th>
                    <th>Address</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_customers->num_rows > 0) {
                    while ($row_customer = $result_customers->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row_customer['customer_id'] . "</td>";
                        echo "<td>" . $row_customer['customer_name'] . "</td>";
                        echo "<td>" . $row_customer['gender'] . "</td>";
                        echo "<td>" . $row_customer['phone_number'] . "</td>";
                        echo "<td>" . $row_customer['address'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No customers found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
