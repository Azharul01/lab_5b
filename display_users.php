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

// Handle DELETE operation
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM users WHERE matric = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("s", $delete_id);
    if ($stmt->execute()) {
        echo "<p style='color:green; text-align:center;'>Record deleted successfully!</p>";
    } else {
        echo "<p style='color:red; text-align:center;'>Error deleting record!</p>";
    }
    $stmt->close();
}

// Fetch data from the users table
$sql = "SELECT matric, name, role FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Users</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
        }
        table {
            width: 80%;
            margin: auto;
            border-collapse: collapse;
            text-align: center;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        a, button {
            text-decoration: none;
            color: white;
            background-color: #f44336;
            padding: 5px 10px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        a.update-btn {
            background-color: #4CAF50;
        }
        .logout {
            text-align: center;
            margin: 20px;
        }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Users List</h2>
    <table>
        <tr>
            <th>Matric</th>
            <th>Name</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['matric']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['role']}</td>
                        <td>
                            <a href='update_user.php?matric={$row['matric']}' class='update-btn'>Update</a>
                            <a href='display_users.php?delete_id={$row['matric']}' onclick=\"return confirm('Are you sure you want to delete this record?');\">Delete</a>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No records found</td></tr>";
        }
        ?>
    </table>

    <div class="logout">
        <form method="POST" action="logout.php">
            <button type="submit" name="logout">Logout</button>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>