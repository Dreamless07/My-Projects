<?php
$firstname = '';
session_start();

// Check if user is not logged in, redirect to login page
if (!isset($_SESSION['email'])) {
    header("location: login.php");
    exit;
}

include 'db.php';

$email = $_SESSION['email'];

// Retrieve pharmacist details
$sql_pharmacist = "SELECT * FROM pharmacist WHERE email='$email'";
$result_pharmacist = $conn->query($sql_pharmacist);
$pharmacist_details = $result_pharmacist->fetch_assoc();

$pharmacist_id = $pharmacist_details['pharmacist_id'];

function generatePDF($transaction_data, $pharmacist_details, $customer_details, $medicine_details, $conn) 
    {
    try {
        // Include TCPDF library
        require_once('TCPDF-main/TCPDF-main/tcpdf.php');

        // Create new PDF document
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Transaction Details');
        $pdf->SetSubject('Transaction Details');

        // Add a page
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('helvetica', 'B', 12);

        // Title
        $pdf->Cell(0, 10, 'Transaction Details', 0, 1, 'C');

        // Pharmacy Purchase Details
        $pdf->Cell(0, 10, 'Pharmacy Purchase Details:', 0, 1);
        $pdf->Cell(0, 10, '--------------------------', 0, 1);

        // Pharmacist Details
        $pdf->Cell(0, 10, 'Pharmacist Details:', 0, 1);
        $pdf->Cell(0, 10, 'Name: ' . $pharmacist_details['firstname'] . ' ' . $pharmacist_details['lastname'], 0, 1);
        $pdf->Cell(0, 10, 'Email: ' . $pharmacist_details['email'], 0, 1);
        $pdf->Cell(0, 10, 'Phone Number: ' . $pharmacist_details['phone_number'], 0, 1);
        $pdf->Cell(0, 10, 'Address: ' . $pharmacist_details['address'], 0, 1);

        // Customer Details
        $pdf->Cell(0, 10, 'Customer Details:', 0, 1);
        $pdf->Cell(0, 10, 'ID: ' . $customer_details['customer_id'], 0, 1);
        $pdf->Cell(0, 10, 'Name: ' . $customer_details['customer_name'], 0, 1);
        $pdf->Cell(0, 10, 'Phone Number: ' . $customer_details['phone_number'], 0, 1);
        $pdf->Cell(0, 10, 'Address: ' . $customer_details['address'], 0, 1);

        // Transaction Details
        $pdf->Cell(0, 10, 'Transaction Details:', 0, 1);
        $pdf->Cell(0, 10, '--------------------------', 0, 1);
        foreach ($transaction_data as $transaction) {
            $pdf->Cell(0, 10, 'Transaction ID: ' . $transaction['transaction_id'], 0, 1);
            $pdf->Cell(0, 10, 'Medicine ID: ' . $transaction['medicine_id'], 0, 1);

            // Fetch medicine details from medicine table using medicine_id
            $medicine_id = $transaction['medicine_id'];
            $sql_medicine = "SELECT * FROM medicine WHERE medicine_id='$medicine_id'";
            $result_medicine = $conn->query($sql_medicine);
            $medicine_details = $result_medicine->fetch_assoc();
            
            $pdf->Cell(0, 10, 'Medicine Name: ' . $medicine_details['medicine_name'], 0, 1);
            $pdf->Cell(0, 10, 'Expiry Date: ' . $medicine_details['expiry_date'], 0, 1);

            // Assuming you have customer details passed from the function parameters
            $pdf->Cell(0, 10, 'Customer ID: ' . $transaction['customer_id'], 0, 1);
            $pdf->Cell(0, 10, 'Quantity: ' . $transaction['quantity'], 0, 1);
            $pdf->Cell(0, 10, 'Cost: ' . $transaction['cost'], 0, 1);
            $pdf->Cell(0, 10, 'Tax: ' . $transaction['tax'], 0, 1);
            $pdf->Cell(0, 10, '--------------------------', 0, 1);
        }

        $filename = 'C:/xampp/htdocs/peek/' . $customer_details['customer_name'] . '_transactiondetails.pdf';
        
        // Close and output PDF with dynamically set filename
        $pdf->Output($filename, 'F');
        return $filename; // Return filename if PDF is generated successfully
    } catch (Exception $e) {
        // Log any exceptions
        error_log('PDF generation error: ' . $e->getMessage());
        return false; // Return false if there's an error
    }
}

// Handle adding new transaction
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_transaction'])) {
    $medicine_id = $_POST['medicine_id'];
    $customer_id = $_POST['customer_id'];
    $quantity = $_POST['quantity'];
    $cost = $_POST['cost'];
    $tax = $_POST['tax'];

    $insert_sql = "INSERT INTO transaction (medicine_id, customer_id, quantity, cost, tax, pharmacist_id) VALUES ('$medicine_id', '$customer_id', '$quantity', '$cost', '$tax', '$pharmacist_id')";
    if ($conn->query($insert_sql) === TRUE) {
        // Generate PDF
        $transaction_data = array(); // Initialize an empty array to store transaction data
        $sql_transactions = "SELECT * FROM transaction WHERE pharmacist_id='$pharmacist_id'";
        $result_transactions = $conn->query($sql_transactions);

        // Check if there are any rows returned
        if ($result_transactions->num_rows > 0) {
            // Loop through each row of the result set
            while ($row_transaction = $result_transactions->fetch_assoc()) {
                // Add each row to the transaction data array
                $transaction_data[] = $row_transaction;
            }
        }

        // Fetch customer details
        $customer_id = $_POST['customer_id'];
        $sql_customer = "SELECT * FROM customer WHERE customer_id='$customer_id'";
        $result_customer = $conn->query($sql_customer);
        $customer_details = $result_customer->fetch_assoc();

        $pdf_filename = generatePDF($transaction_data, $pharmacist_details, $customer_details, $medicine_details, $conn);

        if ($pdf_filename) {
            // Redirect to refresh the page if PDF is generated successfully
            header("Location: ".$_SERVER['PHP_SELF']);
            exit;
        } else {
            echo "Error occurred while generating PDF.";
        }
    } else {
        echo "Error: " . $insert_sql . "<br>" . $conn->error;
    }
}

// Retrieve transaction list
$sql_transactions = "SELECT * FROM transaction WHERE pharmacist_id='$pharmacist_id'";
$result_transactions = $conn->query($sql_transactions);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Transactions - <?php echo $pharmacist_details['firstname']; ?></title>
    <link rel="stylesheet" href="transaction1.css">
</head>
<body>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Transactions - <?php echo $firstname; ?></title>
    <link rel="stylesheet" href="transaction1.css">
</head>
<body>
    <div class="sidebar">
        <h2>Welcome, <?php  echo $firstname; ?></h2>
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

    <div id="manage_transactions" class="manage_transactions_container">
        <h3>Manage Transactions</h3>
        
        <!-- Form to add new transaction -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="medicine_id">Medicine ID:</label><br>
            <input type="text" id="medicine_id" name="medicine_id" required><br><br>

            <label for="customer_id">Customer ID:</label><br>
            <input type="text" id="customer_id" name="customer_id" required><br><br>

            <label for="quantity">Quantity:</label><br>
            <input type="text" id="quantity" name="quantity" required><br><br>

            <label for="cost">Cost:</label><br>
            <input type="text" id="cost" name="cost" required><br><br>

            <label for="tax">Tax:</label><br>
            <input type="text" id="tax" name="tax" required><br><br>

            <button type="submit" name="add_transaction">Add Transaction</button>
        </form>

        <!-- Display Transaction List Table Here -->
        <h4>Transaction List</h4>
        <table>
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Medicine ID</th>
                    <th>Customer ID</th>
                    <th>Quantity</th>
                    <th>Cost</th>
                    <th>Tax</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_transactions->num_rows > 0) {
                    while ($row_transaction = $result_transactions->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row_transaction['transaction_id'] . "</td>";
                        echo "<td>" . $row_transaction['medicine_id'] . "</td>";
                        echo "<td>" . $row_transaction['customer_id'] . "</td>";
                        echo "<td>" . $row_transaction['quantity'] . "</td>";
                        echo "<td>" . $row_transaction['cost'] . "</td>";
                        echo "<td>" . $row_transaction['tax'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No transactions found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

</body>
</html>
