<?php
session_start();
if (isset($_SESSION['email'])) {
    header("location: index.php");
    exit;
}

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $gender = $_POST['gender'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];

    // Insert user data into the database
    $insert_sql = "INSERT INTO pharmacist (firstname, lastname, email, password, gender, phone_number, address) 
                    VALUES ('$firstname', '$lastname', '$email', '$password', '$gender', '$phone_number', '$address')";

    if ($conn->query($insert_sql) === TRUE) {
        header("location: login.php");
        exit;
    } else {
        echo "Error: " . $insert_sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration form</title>
    <link rel="stylesheet" href="register1.css">
</head>
<body>
    <div class="container">
        <h2>Pharmacy Inventory Management Registraion Form</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="firstname">First Name:</label><br>
            <input type="text" id="firstname" name="firstname" required><br><br>
            <label for="lastname">Last Name:</label><br>
            <input type="text" id="lastname" name="lastname" required><br><br>
            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" required><br><br>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br><br>
            <label for="gender">Gender:</label><br>
            <input type="radio" id="male" name="gender" value="Male" required>
            <label for="male">Male</label>
            <input type="radio" id="female" name="gender" value="Female" required>
            <label for="female">Female</label><br><br>
            <label for="phone_number">Phone Number:</label><br>
            <input type="tel" id="phone_number" name="phone_number" required><br><br>
            <label for="address">Address:</label><br>
            <textarea id="address" name="address" required></textarea><br><br>
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </div>
</body>
</html>
