<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lab_5b";

// Redirect to display_users.php if already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: display_users.php");
    exit();
}

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if (isset($_POST['login'])) {
    $matric = $_POST['matric'];
    $password = $_POST['password'];

    // Fetch user details from the database
    $sql = "SELECT * FROM users WHERE matric = '$matric'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $row['password'])) {
            // Set session variables
            $_SESSION['logged_in'] = true;
            $_SESSION['name'] = $row['name'];
            $_SESSION['role'] = $row['role'];

            // Redirect to display_users.php
            header("Location: display_users.php");
            exit();
        } else {
            $error_message = "Invalid password. Please try again.";
        }
    } else {
        $error_message = "No user found with the provided Matric.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
        }
        form {
            border: 1px solid #ccc;
            padding: 20px;
            width: 300px;
            margin: auto;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input, button {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
        .register-link {
            text-align: center;
        }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Login</h2>
    <?php if (isset($error_message)): ?>
        <p class="error"><?php echo $error_message; ?></p>
    <?php endif; ?>
    <form method="POST" action="login.php">
        <label for="matric">Matric:</label>
        <input type="text" name="matric" id="matric" required>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <button type="submit" name="login">Login</button>
    </form>
    <div class="register-link">
        <p>Not registered yet? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>