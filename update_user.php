<?php
session_start();

// Check if the user is logged in; if not, redirect to login page
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'lab_5b');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user data for the selected matric
if (isset($_GET['matric'])) {
    $matric = $_GET['matric'];
    $sql = "SELECT matric, name, role FROM users WHERE matric = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $matric);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}

// Handle Update Operation
if (isset($_POST['update'])) {
    $old_matric = $_POST['old_matric']; // Original matric value
    $new_matric = $_POST['matric'];
    $name = htmlspecialchars(trim($_POST['name']));
    $role = htmlspecialchars(trim($_POST['role']));

    $update_sql = "UPDATE users SET matric = ?, name = ?, role = ? WHERE matric = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssss", $new_matric, $name, $role, $old_matric);

    if ($stmt->execute()) {
        echo "<p style='color:green; text-align:center;'>Record updated successfully!</p>";
        header("refresh:2; url=display_users.php");
        exit();
    } else {
        echo "<p style='color:red; text-align:center;'>Error updating record!</p>";
    }
    $stmt->close();
}

// Handle Cancel button
if (isset($_POST['cancel'])) {
    header("Location: display_users.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
        }
        form {
            border: 1px solid #ccc;
            padding: 20px;
            width: 350px;
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
        button.cancel-btn {
            background-color: #f44336;
        }
        button:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Update User</h2>
    <form method="POST" action="update_user.php">
        <!-- Hidden field to track the original matric -->
        <input type="hidden" name="old_matric" value="<?php echo $user['matric']; ?>">

        <label for="matric">Matric:</label>
        <input type="text" name="matric" id="matric" value="<?php echo $user['matric']; ?>" required>

        <label for="name">Name:</label>
        <input type="text" name="name" id="name" value="<?php echo $user['name']; ?>" required>

        <label for="role">Role:</label>
        <select name="role" id="role" required>
            <option value="Lecturer" <?php echo ($user['role'] == 'Lecturer') ? 'selected' : ''; ?>>Lecturer</option>
            <option value="Student" <?php echo ($user['role'] == 'Student') ? 'selected' : ''; ?>>Student</option>
        </select>

        <button type="submit" name="update">Update</button>
        <button type="submit" name="cancel" class="cancel-btn">Cancel</button>
    </form>
</body>
</html>

<?php
$conn->close();
?>