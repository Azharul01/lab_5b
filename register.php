<?php
session_start();

// Redirect to display_users.php if already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: display_users.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Page</title>
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
        input, select, button {
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
        .login-link {
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Registration Form</h2>
    <form method="POST" action="register.php">
        <label for="matric">Matric:</label>
        <input type="text" name="matric" id="matric" required>

        <label for="name">Name:</label>
        <input type="text" name="name" id="name" required>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <label for="role">Role:</label>
        <select name="role" id="role" required>
            <option value="">Please select</option>
            <option value="Lecturer">Lecturer</option>
            <option value="Student">Student</option>
        </select>

        <button type="submit" name="submit">Register</button>
    </form>
    <div class="login-link">
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>

<?php
if (isset($_POST['submit'])) {
    // Sanitize user inputs
    $matric = htmlspecialchars(trim($_POST['matric']));
    $name = htmlspecialchars(trim($_POST['name']));
    $password = trim($_POST['password']);
    $role = htmlspecialchars(trim($_POST['role']));

    // Check if fields are empty
    if (empty($matric) || empty($name) || empty($password) || empty($role)) {
        echo "<p style='text-align:center; color:red;'>All fields are required!</p>";
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'lab_5b');

    // Check connection
    if ($conn->connect_error) {
        die("<p style='text-align:center; color:red;'>Connection failed: " . $conn->connect_error . "</p>");
    }

    // Prepare and bind SQL query to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO users (matric, name, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $matric, $name, $hashed_password, $role);

    // Execute the query
    if ($stmt->execute()) {
        echo "<p style='text-align:center; color:green;'>Registration successful! Redirecting to login page...</p>";
        header("refresh:3; url=login.php"); // Redirect to login.php after 3 seconds
        exit();
    } else {
        echo "<p style='text-align:center; color:red;'>Error: " . $stmt->error . "</p>";
    }

    // Close connections
    $stmt->close();
    $conn->close();
}
?>