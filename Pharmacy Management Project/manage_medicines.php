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


// Get pharmacist ID
$sql_pharmacist_id = "SELECT pharmacist_id FROM pharmacist WHERE email='$email'";
$result_pharmacist_id = $conn->query($sql_pharmacist_id);
$row_pharmacist_id = $result_pharmacist_id->fetch_assoc();
$pharmacist_id = $row_pharmacist_id['pharmacist_id'];

// Handle adding new medicine
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_medicine'])) {
    $medicine_name = $_POST['medicine_name'];
    $expiry_date = $_POST['expiry_date'];
    $price_per_unit = $_POST['price_per_unit'];

    

    $insert_sql = "INSERT INTO medicine (pharmacist_id, medicine_name, expiry_date, price_per_unit) VALUES ('$pharmacist_id', '$medicine_name', '$expiry_date', '$price_per_unit')";
    if ($conn->query($insert_sql) === TRUE) {
        // Redirect to refresh the page
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } else {
        echo "Error: " . $insert_sql . "<br>" . $conn->error;
    }
}

// Retrieve medicine list
$sql_medicines = "SELECT * FROM medicine WHERE pharmacist_id ='$pharmacist_id'";
$result_medicines = $conn->query($sql_medicines);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Medicines - <?php echo $firstname; ?></title>
    <link rel="stylesheet" href="medicine1.css">
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

        <div id="actions_btn5" class="action_btn">
            <form action="logout.php" method="post">
                <button type="submit" name="logout">Logout</button><br>
            </form>
        </div>
    </div>

    <div id="manage_medicines" class="manage_medicines_container">
        <h3>Manage Medicines</h3>
        
        <!-- Form to add new medicine -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

            <label for="medicine_name">Medicine Name:</label><br>
            <input type="text" id="medicine_name" name="medicine_name" required><br><br>

            <label for="expiry_date">Expiry Date:</label><br>
            <input type="date" id="expiry_date" name="expiry_date" required><br><br>

            <label for="price_per_unit">Price Per Unit:</label><br>
            <input type="text" id="price_per_unit" name="price_per_unit" required><br><br>

            <button type="submit" name="add_medicine">Add Medicine</button>
        </form>

        <!-- Display Medicine List Table Here -->
        <h4>Medicine List</h4>
        <table>
            <thead>
                <tr>
                    <th>Medicine Id</th>
                    <th>Medicine Name</th>
                    <th>Expiry Date</th>
                    <th>Price Per Unit</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_medicines->num_rows > 0) {
                    while ($row_medicine = $result_medicines->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row_medicine['medicine_id'] . "</td>";
                        echo "<td>" . $row_medicine['medicine_name'] . "</td>";
                        echo "<td>" . $row_medicine['expiry_date'] . "</td>";
                        echo "<td>" . $row_medicine['price_per_unit'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No medicines found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
