<?php
if (isset($_POST['submit'], $_POST['name'], $_POST['email'], $_POST['address'], $_POST['message'])) {
    include 'connection.php';
    // Collect and sanitize input data
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $address = htmlspecialchars(trim($_POST['address']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Make sure to properly format the SQL query
    $query = "INSERT INTO formdata (Name, Email, PhoneNumber, Address, Message) VALUES ('$name', '$email', '$phone', '$address', '$message')";
    
    // Assume $conn is your database connection
    $data = mysqli_query($conn, $query);
    
    if ($data) {
            // Here, you can process the data (e.g., save to database, send email, etc.)
            echo "<h2>Thank you, $name!</h2>";
            echo "<p>Your message has been received:</p>";
            echo "<p>Email: $email</p>";
            echo "<p>Phone: $phone</p>";
            echo "<p>Address: $address</p>";
            echo "<p>Message: $message</p>";
    } else {
        echo "Failed: " . mysqli_error($conn); // Provide more detail about the error
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-container">
        <h1>welcome</h1>
        <h1>Contact Us</h1>
        <form action="" method="post">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="phone">Phone Number:</label>
            <input type="tel" id="phone" name="phone" required>

            <label for="address">Address:</label>
            <input type="text" id="address" name="address" required>

            <label for="message">Message:</label>
            <textarea id="message" name="message" rows="4" required></textarea>

            <button name="submit" type="submit">Submit</button>
        </form>
    </div>
</body>
</html>