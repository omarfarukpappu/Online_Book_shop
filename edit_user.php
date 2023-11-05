<?php
include 'config.php';

// Check if the 'id' parameter is set in the URL
if (isset($_GET['id'])) {
    // Sanitize and validate the user ID
    $user_id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    if ($user_id !== false && $user_id > 0) {
        // Fetch user data based on ID
        $select_query = "SELECT * FROM `users` WHERE id = $user_id";
        $result = mysqli_query($conn, $select_query);

        if ($result) {
            $userData = mysqli_fetch_assoc($result);
        } else {
            echo "Error fetching data: " . mysqli_error($conn);
        }
    } else {
        echo "Invalid user ID";
    }
}

// Check if the form is submitted for updating user data
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
        header('location: display_users.php');
        
        // Fetch updated user data
        $result = mysqli_query($conn, "SELECT * FROM `users` WHERE id = $id");

        if ($result) {
            $userData = mysqli_fetch_assoc($result);
        } else {
            echo "Error fetching updated data: " . mysqli_error($conn);
        }
    } else {
        echo "Error updating data: " . mysqli_error($conn);
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
    <title>Edit User Data</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .table-container {
            width: 50%;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h3 {
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            margin-bottom: 10px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 10px;
            cursor: pointer;
        }
        nav li{
 list-style: none;
 display: inline;
 margin-right: 10px;


}
nav li:hover{
  background-color: azure;
  align-items: center;
}
nav a {
    text-decoration: none;
    color: black;
}
    </style>
</head>
<body>

   <div class="table-container">

      <h3>Edit User Data</h3>
      <form action="" method="post">
         <label for="id">User ID:</label>
         <input type="text" name="id" value="<?php echo $userData['id']; ?>" readonly>

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
   </div>

</body>
</html>
