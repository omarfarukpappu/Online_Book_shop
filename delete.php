 <?php
include 'config.php';

if (isset($_GET['id'])) {
    $user_id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    if ($user_id !== false && $user_id > 0) {
      
        $delete_query = "DELETE FROM `users` WHERE id = $user_id";


        if (mysqli_query($conn, $delete_query)) {
            $message = "User data deleted successfully";
        } else {
            $error_message = "Error deleting user data: " . mysqli_error($conn);
        }
    } else {
        $error_message = "Invalid user ID";
    }
} else {
    $error_message = "User ID not provided";
}


header("Location: display_users.php");
exit();
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete User Data</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        if (isset($message)) {
            echo "<p class='success'>$message</p>";
        } elseif (isset($error_message)) {
            echo "<p class='error'>$error_message</p>";
        }
        ?>
    </div>
</body>
</html>
