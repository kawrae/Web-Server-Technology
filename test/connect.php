<?php
$conn = new mysqli('localhost', 'root', '', 'test');

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

// Get user input safely
$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$gender = $_POST['gender'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$number = $_POST['number'] ?? 0;

// Check if email already exists
$emailCheckQuery = "SELECT * FROM registration WHERE email = ?";
$emailStmt = $conn->prepare($emailCheckQuery);
$emailStmt->bind_param("s", $email);
$emailStmt->execute();
$emailStmt->store_result();

if ($emailStmt->num_rows > 0) {
    echo "Error: duplicate email, please enter a different email";
} else {
    // Secure SQL query using prepared statements
    $stmt = $conn->prepare("INSERT INTO registration (firstName, lastName, gender, email, password, number) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $firstName, $lastName, $gender, $email, $password, $number);

    if ($stmt->execute()) {
        echo "Registration successful.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// $stmt = $conn->prepare("INSERT INTO registration (firstName, lastName, gender, email, password, number) VALUES (?, ?, ?, ?, ?, ?)");
// $stmt->bind_param("sssssi", $firstName, $lastName, $gender, $email, $password, $number);

// if ($stmt->execute()) {
//     echo "Registration successful.";
// } else {
//     echo "Error: " . $stmt->error;
// }

// $stmt->close();

$emailStmt->close();
$conn->close();
?>