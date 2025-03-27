<?php
include 'config.php';
session_start();
$user_id = isset ($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (!$user_id) {
    header('location:login.php');
    exit;
}

if (isset ($_GET['logout'])) {
    unset($_SESSION['user_id']);
    session_destroy();
    header('location:login.php');
    exit;
}

if (isset ($_POST['confirm_delete'])) {
    // Delete the user account
    $delete_query = mysqli_query($conn, "DELETE FROM user_form WHERE id = '$user_id'");
    if ($delete_query) {
        // Account deleted successfully
        unset($_SESSION['user_id']);
        session_destroy();
        header('location:login.php');
        exit;
    } else {
        // Error deleting account
        $message = "Error deleting account: " . mysqli_error($conn);
    }
}


if (isset ($_POST['delete_selected'])) {
    if (isset ($_POST['user_ids']) && is_array($_POST['user_ids'])) {
        foreach ($_POST['user_ids'] as $user_id_to_delete) {
            // Delete the selected user accounts
            $delete_query = mysqli_query($conn, "DELETE FROM user_form WHERE id = '$user_id_to_delete'");
        }
        // Redirect to prevent resubmission
        header('Location: profile.php');
        exit;
    }
}

function displayUsers($conn, $fetch)
{
    $sql = "SELECT * FROM user_form";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<h3> Table of users </h3> ";
        echo "<form method='post'>";
        echo "<div class='table-responsive'>";
        echo "<table class='table table-bordered'>";

        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Type of User</th></tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["id"] . "</td>";
            echo "<td>" . $row["name"] . "</td>";
            echo "<td>" . $row["email"] . "</td>";

            if ($row['user_type'] == 'admin' || $row['user_type'] == 'owner') {
                if ($fetch['user_type'] == 'admin') {
                    echo "<td> RESTRICTED </td>";
                } else {
                    echo "<td>" . $row["user_type"] . "</td>";
                    echo "<td><input type='checkbox' name='user_ids[]' value='" . $row["id"] . "'></td>";

                }
            } else {
                echo "<td>" . $row["user_type"] . "</td>";
                echo "<td><input type='checkbox' name='user_ids[]' value='" . $row["id"] . "'></td>";
            }
            echo "</tr>";
        }

        echo "</table>";
        echo "</div>";
        echo "<input type='submit' name='delete_selected' value='Delete Selected Users' class='btn btn-danger' onclick=\"return confirm('are you sure you want to delete the selected users?')\"  >";

        if ($fetch['user_type'] == 'owner') {
            echo "<a href='add_admin.php' class='btn btn-danger'>Add Admin User</a>";
        }

        echo "</form>";
    } else {
        echo "0 results";
    }
}

function displayproduct($conn, $fetch)
{
    // Fetch shopping cart items from the database
    $user_id = $fetch['id'];
    $query = "SELECT * FROM shopping_cart WHERE user_id = '$user_id'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $_SESSION["shopping_cart"] = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    // Your display product function content here
    if (isset ($_POST["add"])) {
        if (isset ($_SESSION["shopping_cart"])) {
            $item_array_id = array_column($_SESSION["shopping_cart"], "product_id");
            if (!in_array($_GET["id"], $item_array_id)) {
                $count = count($_SESSION["shopping_cart"]);
                $item_array = array(
                    'product_id' => $_GET["id"],
                    'product_name' => $_POST["hidden_name"],
                    'product_price' => $_POST["hidden_price"],
                    'product_quantity' => $_POST["quantity"],
                );
                $_SESSION["shopping_cart"][$count] = $item_array;
                // Save the shopping cart items to the database
                saveShoppingCart($conn, $user_id, $_SESSION["shopping_cart"]);
                echo '<script>window.location="profile.php"</script>';
            } else {
                echo '<script>alert("Product is already in  the cart")</script>';
                echo '<script>window.location="profile.php"</script>';
            }
        } else {
            $item_array = array(
                'product_id' => $_GET["id"],
                'product_name' => $_POST["hidden_name"],
                'product_price' => $_POST["hidden_price"],
                'product_quantity' => $_POST["quantity"],
            );
            $_SESSION["shopping_cart"][0] = $item_array;
            // Save the shopping cart items to the database
            saveShoppingCart($conn, $user_id, $_SESSION["shopping_cart"]);
        }
    }

    if (isset ($_GET["action"])) {
        if ($_GET["action"] == "delete") {
            foreach ($_SESSION["shopping_cart"] as $keys => $value) {
                if ($value["product_id"] == $_GET["id"]) {
                    unset($_SESSION["shopping_cart"][$keys]);
                    // Update the shopping cart in the database after removing an item
                    saveShoppingCart($conn, $user_id, $_SESSION["shopping_cart"]);
                    echo '<script>alert("Product has been removed")</script>';
                    echo '<script>window.location="profile.php"</script>';
                }
            }
        }
    }
}

// Function to save shopping cart items to the database
function saveShoppingCart($conn, $user_id, $shopping_cart)
{
    // Delete existing entries for the user
    mysqli_query($conn, "DELETE FROM shopping_cart WHERE user_id = '$user_id'");

    // Insert new entries
    foreach ($shopping_cart as $item) {
        $product_id = $item['product_id'];
        $product_name = $item['product_name'];
        $product_price = $item['product_price'];
        $product_quantity = $item['product_quantity'];
        mysqli_query($conn, "INSERT INTO shopping_cart (user_id, product_id, product_name, product_price, product_quantity) VALUES ('$user_id', '$product_id', '$product_name', '$product_price', '$product_quantity')");
    }
}

function adminProducts($conn, $fetch)
{
    $sql = "SELECT * FROM product";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<h3> Table of products </h3> ";
        echo "<form method='post'>";
        echo "<div class='table-responsive'>";
        echo "<table class='table table-bordered'>";

        echo "<tr><th>ID</th><th>Description</th><th>Price</th><th>Image</th><th>Options</th></tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["id"] . "</td>";
            echo "<td>" . $row["description"] . "</td>";
            echo "<td>" . $row["price"] . "</td>";
            echo "<td><img src='products_img/" . $row["image"] . "' width='100px' height='100px'></td>";
            echo "<td><a href='edit_product.php?id=" . $row["id"] . "' class='btn btn-primary'>Edit</a><form method='post'><input type='hidden' name='delete_id' value='" . $row["id"] . "'><input type='submit' name='delete' value='Delete' class='btn btn-danger' onclick=\"return confirm('Are you sure you want to delete this product?')\"></form></td>";
            echo "</tr>";
        }

        echo "</table>";
        echo "</div>";
        echo "<a href='add_product.php' class='btn btn-danger'>Add Product</a>";
        echo "</form>";
    } else {
        echo "0 results";
    }
}

if (isset($_POST['delete'])) {
    if (isset($_POST['delete_id'])) {
        $id = $_POST['delete_id'];
        
        $sql = "DELETE FROM product WHERE id = $id";
        if ($conn->query($sql) === TRUE) {
            echo "Product deleted successfully";
        } else {
            echo "Error deleting product: " . $conn->error;
        }
    } else {
        echo "Product ID not provided for deletion.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"
        integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI"
        crossorigin="anonymous"></script>

    <style>
        .product {
            border: 1px solid #eaeaec;
            margin: 2px 2px 8px 2px;
            padding: 10px;
            text-align: center;
            background-color: #efefef;
        }

        table,
        th,
        tr {
            text-align: center;
        }

        .title2 {
            text-align: center;
            color: #66afe9;
            background-color: #efefef;
            padding: 2%;
        }

        h2 {
            text-align: center;
            color: #66afe9;
            background-color: #efefef;
            padding: 2%;
        }

        table th {
            background-color: #efefef;
        }
    </style>
    <style>
        /* Floating Chatbot Button */
        #chatbot-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            cursor: pointer;
            font-size: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            transition: 0.3s;
        }

        #chatbot-toggle:hover {
            background: #0056b3;
        }

        /* Chatbot Container */
        #chat-container {
            position: fixed;
            bottom: 80px;
            right: 20px;
            width: 300px;
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            display: none;
            flex-direction: column;
            z-index: 1000;
        }

        /* Chat Box */
        #chat-box {
            height: 250px;
            overflow-y: auto;
            background: #f9f9f9;
            padding: 10px;
            border-radius: 8px;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* User Message */
        .user-message {
            background: #007bff;
            color: white;
            padding: 8px;
            border-radius: 10px;
            margin: 5px 0;
            text-align: right;
            max-width: 80%;
            align-self: flex-end;
        }

        /* Bot Message */
        .bot-message {
            background: #ddd;
            color: black;
            padding: 8px;
            border-radius: 10px;
            margin: 5px 0;
            text-align: left;
            max-width: 80%;
            align-self: flex-start;
        }

        /* Chat Input */
        .input-container {
            display: flex;
            margin-top: 10px;
        }

        #user-input {
            flex: 1;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
            font-size: 14px;
        }

        button.send-btn {
            padding: 8px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-left: 5px;
        }

        button.send-btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

    <div class="container" style="column-gap: 50px">

<div class="profile">
    <?php
    $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE id = '$user_id'") or die ('query failed');
    if (mysqli_num_rows($select) > 0) {
        $fetch = mysqli_fetch_assoc($select);
    }
    if ($fetch['image'] == '') {
        echo '<img src="images/default-avatar.png">';
    } else {
        echo '<img src="uploaded_img/' . $fetch['image'] . '">';
    }
    ?>
    <h3>
        <?php echo $fetch['name']; ?>
    </h3>
    <a href="update_profile.php" class="btn btn-primary">Update Profile</a>

    <?php

    if (($fetch['user_type'] == 'user') || ($fetch['user_type'] == 'admin')) {
        echo '<form method="post" action="">';
        echo '<input type="submit" name="confirm_delete" value="Delete Your Account" class="btn btn-danger" onclick="return confirm(\'Are you sure you want to delete your account?\')">';
        echo '</form>';
    }

    ?>

    <a href="profile.php?logout=<?php echo $user_id; ?>" class="btn btn-danger">Logout</a>

    <p>New <a href="login.php">Login</a> or <a href="register.php">Register</a></p>
</div>


<div>


    <?php

    if (($fetch['user_type'] == 'owner') || ($fetch['user_type'] == 'admin')) {
        displayUsers($conn, $fetch);
        
        echo "<br><br>";
        adminProducts($conn, $fetch);
    };

    if ($fetch['user_type'] == 'user') {

        displayproduct($conn, $fetch);
        ?>

        <div>
            <!-- Your shopping cart content here -->
            <h2>Shopping Cart</h2>
            <?php
            $query = "select * from product order by id asc";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_array($result)) {
                    ?>
                    <div class="col-md-3" style="float: left;">
                        <form method="post" action="profile.php?action=add&id=<?php echo $row["id"]; ?>">
                            <div class="product">
                                <img src="products_img/<?php echo $row["image"]; ?>" width="190px" height="200px"
                                    class="img-responsive">
                                <h5 class="text-info">
                                    <?php echo $row["description"]; ?>
                                </h5>
                                <h5 class="text-danger">£
                                    <?php echo $row["price"]; ?>
                                </h5>
                                <input type="text" name="quantity" class="form-control" value="1">
                                <input type="hidden" name="hidden_name" value="<?php echo $row["description"]; ?>">
                                <input type="hidden" name="hidden_price" value="<?php echo $row["price"]; ?>">
                                <input type="submit" name="add" style="margin-top: 5px;" class="btn btn-success"
                                    value="Add to cart">
                            </div>
                        </form>
                    </div>
                    <?php
                }
            }
            ?>

            <div style="clear: both"></div>
            <h3 class="title2">Shopping Cart Details</h3>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Product Description</th>
                        <th width="10%">Quantity</th>
                        <th width="13%">Price Details</th>
                        <th width="10%">Total Price</th>
                        <th width="17%">Remove Item</th>
                    </tr>
                    <?php
                    if (!empty ($_SESSION["shopping_cart"])) {
                        $total = 0;
                        foreach ($_SESSION["shopping_cart"] as $key => $value) {
                            ?>
                            <tr>
                                <td>
                                    <?php echo $value["product_name"]; ?>
                                </td>
                                <td>
                                    <?php echo $value["product_quantity"]; ?>
                                </td>
                                <td>£
                                    <?php echo $value["product_price"]; ?>
                                </td>
                                <td>£
                                    <?php echo number_format($value["product_quantity"] * $value["product_price"], 2); ?>
                                </td>
                                <td><a href="profile.php?action=delete&id=<?php echo $value["product_id"]; ?>"><span
                                            class="text-danger">Remove Item</span></a></td>
                            </tr>
                            <?php
                            $total = $total + ($value["product_quantity"] * $value["product_price"]);
                        }
                        ?>
                        <tr>
                            <td colspan="3" align="right">Total</td>
                            <td align="right">£
                                <?php echo number_format($total, 2); ?>
                            </td>
                            <td></td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>

        </div>
    </div>

    <?php
    }
    ?>
</div>
</div>













    <!-- Floating Chatbot Button -->
    <button id="chatbot-toggle">💬</button>

    <!-- Chatbot UI -->
    <div id="chat-container">
        <h3>AI Assistant</h3>
        <div id="chat-box"></div>
        <div class="input-container">
            <input type="text" id="user-input" placeholder="Type a message...">
            <button class="send-btn" onclick="sendMessage()">Send</button>
        </div>
    </div>

    <script>
        // Toggle chatbot visibility
        document.getElementById('chatbot-toggle').addEventListener('click', function() {
            const chatContainer = document.getElementById('chat-container');
            chatContainer.style.display = (chatContainer.style.display === 'none' || chatContainer.style.display === '') ? 'flex' : 'none';
        });

        function sendMessage() {
            const userInput = document.getElementById('user-input').value.trim();
            if (userInput === "") return;

            const chatBox = document.getElementById('chat-box');

            // Append user message
            const userMessage = document.createElement('div');
            userMessage.className = 'user-message';
            userMessage.textContent = userInput;
            chatBox.appendChild(userMessage);

            fetch("chatbot.php", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: userInput })
            })
            .then(response => response.json())
            .then(data => {
                const botMessage = document.createElement('div');
                botMessage.className = 'bot-message';
                botMessage.textContent = data.error ? `Bot: ${data.error}` : `Bot: ${data.response}`;
                chatBox.appendChild(botMessage);
                document.getElementById('user-input').value = '';
                chatBox.scrollTop = chatBox.scrollHeight;
            })
            .catch(error => {
                const errorMessage = document.createElement('div');
                errorMessage.className = 'bot-message';
                errorMessage.textContent = 'Bot: Failed to fetch response.';
                chatBox.appendChild(errorMessage);
            });
        }
    </script>

</body>
</html>
