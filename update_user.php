<?php
include 'config.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $newName = mysqli_real_escape_string($conn, $_POST['new_name']);
    $newEmail = mysqli_real_escape_string($conn, $_POST['new_email']);
    $newUserType = $_POST['new_user_type'];

    // Update query
    $updateQuery = "UPDATE `users` SET name=?, email=?, user_type=? WHERE id=?";
    
    // Use prepared statement to prevent SQL injection
    $stmt = mysqli_prepare($conn, $updateQuery);
    
    // Bind parameters
    mysqli_stmt_bind_param($stmt, 'sssi', $newName, $newEmail, $newUserType, $id);

    // Execute the update query
    if (mysqli_stmt_execute($stmt)) {
        echo "Data updated successfully";
    } else {
        echo "Error updating data: " . mysqli_error($conn);
    }

    // Close the statement
    mysqli_stmt_close($stmt);
}

// Fetch user data based on ID
$userData = [];

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $selectQuery = "SELECT * FROM `users` WHERE id=?";
    
    // Use prepared statement to prevent SQL injection
    $stmt = mysqli_prepare($conn, $selectQuery);
    
    // Bind parameters
    mysqli_stmt_bind_param($stmt, 'i', $id);

    // Execute the select query
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $userData = mysqli_fetch_assoc($result);
    } else {
        echo "Error fetching data: " . mysqli_error($conn);
    }

    // Close the statement
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User Data</title>
</head>
<body>

    <h2>Update User Data</h2>

    <form action="" method="post">
        <input type="hidden" name="id" value="<?php echo $userData['id']; ?>">
        <label for="new_name">New Name:</label>
        <input type="text" name="new_name" value="<?php echo $userData['name']; ?>" required>
        
        <label for="new_email">New Email:</label>
        <input type="email" name="new_email" value="<?php echo $userData['email']; ?>" required>

        <label for="new_user_type">New User Type:</label>
        <select name="new_user_type">
            <option value="user" <?php if ($userData['user_type'] === 'user') echo 'selected'; ?>>User</option>
            <option value="admin" <?php if ($userData['user_type'] === 'admin') echo 'selected'; ?>>Admin</option>
        </select>

        <input type="submit" value="Update">
    </form>

</body>
</html>
