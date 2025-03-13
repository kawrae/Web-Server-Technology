<!DOCTYPE html>
<html>
<head>
    <title>User Management System</title>
</head>
<body>

<?php
// MySQL connection
include 'config.php';

// Function to display users
function displayUsers($conn) {
    $sql = "SELECT * FROM user_form";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr><td>".$row["id"]."</td><td>".$row["name"]."</td><td>".$row["email"]."</td></tr>";
        }
        echo "</table>";
    } else {
        echo "0 results";
    }
}

// Display users
displayUsers($conn);

// Add User Form
echo "<h3>Add User</h3>";
echo "<form action='add_user.php' method='post'>";
echo "Name: <input type='text' name='name'><br>";
echo "Email: <input type='text' name='email'><br>";
echo "<input type='submit' value='Add User'>";
echo "</form>";

// Remove User Form
echo "<h3>Remove User</h3>";
echo "<form action='remove_user.php' method='post'>";
echo "User ID: <input type='text' name='user_id'><br>";
echo "<input type='submit' value='Remove User' onclick='return confirm(\"Are you sure you want to delete this record?\")'>";
echo "</form>";

// Update User Form
echo "<h3>Update User</h3>";
echo "<form action='update_user.php' method='post'>";
echo "User ID: <input type='text' name='user_id'><br>";
echo "New Name: <input type='text' name='new_name'><br>";
echo "New Email: <input type='text' name='new_email'><br>";
echo "<input type='submit' value='Update User'>";
echo "</form>";

$conn->close();
?>
</body>
</html>
