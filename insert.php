<?php
session_start();

include 'config.php';

function validatePassword($password) {
    return (strlen($password) >= 6) && preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirmPassword = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    $user_type = $_POST['user_type'];

    // File upload handling
    $uploadDir = 'image_photo/'; // Directory where the files will be saved
    $uploadFile = $uploadDir . basename($_FILES['uploaded_img']['name']);
    $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));

    // Check if the file is an image
    $check = getimagesize($_FILES['uploaded_img']['tmp_name']);
    if ($check === false) {
        $_SESSION['message'][] = 'File is not an image.';
    } elseif (file_exists($uploadFile)) {
        $_SESSION['message'][] = 'File already exists.';
    } elseif ($_FILES['uploaded_img']['size'] > 50000000) {
        $_SESSION['message'][] = 'File is too large (greater than 5MB).';
    } elseif (!in_array($imageFileType, ['jpg', 'jpeg', 'png'])) {
        $_SESSION['message'][] = 'Only JPG, JPEG, and PNG files are allowed.';
    } else {
        move_uploaded_file($_FILES['uploaded_img']['tmp_name'], $uploadFile);

        if (!validatePassword($password)) {
            $_SESSION['message'][] = 'Password must be at least 6 characters long and contain at least one special character.';
        } else {
            $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email'") or die('query failed');

            if (mysqli_num_rows($select_users) > 0) {
                $_SESSION['message'][] = 'User already exists!';
            } else {
                if ($password != $confirmPassword) {
                    $_SESSION['message'][] = 'Confirm password not matched!';
                } else {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    $insertQuery = "INSERT INTO `users` (name, email, password, user_type, profile_pic) VALUES (?, ?, ?, ?, ?)";
                    $stmt = mysqli_prepare($conn, $insertQuery);



                    // Bind parameters including the profile picture path
                    mysqli_stmt_bind_param($stmt, 'sssss', $name, $email, $hashedPassword, $user_type, $uploadFile);

                    if (mysqli_stmt_execute($stmt)) {
                        $_SESSION['message'][] = 'Registered successfully!';
                        header('location: display_users.php');
                        exit();
                    } else {
                        $_SESSION['message'][] = 'Error inserting data: ' . mysqli_error($conn);
                    }

                    mysqli_stmt_close($stmt);
                }
            }
        }
    }

    $_SESSION['formData'] = [
        'name' => $name,
        'email' => $email,
        'user_type' => $user_type,
    ];

    header('location: ' . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert User Data</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .form-container {
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

        input[type="file"] {
            margin-bottom: 20px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 10px;
            cursor: pointer;
        }

        p.error-message {
            color: red;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h3>Insert User Data</h3>

    <?php
    if (!empty($_SESSION['message'])) {
        foreach ($_SESSION['message'] as $message) {
            echo '<p class="error-message">' . $message . '</p>';
        }

        unset($_SESSION['message']);
    }
    ?>

    <form action="" method="post" enctype="multipart/form-data">
        <label for="name">Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars(isset($_SESSION['formData']['name']) ? $_SESSION['formData']['name'] : ''); ?>" required>

        <label for="email">Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars(isset($_SESSION['formData']['email']) ? $_SESSION['formData']['email'] : ''); ?>" required>

        <label for="password">Password:</label>
        <input type="password" name="password" required>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" name="confirm_password" required>

        <label for="user_type">User Type:</label>
        <select name="user_type">
            <option value="user" <?php echo (isset($_SESSION['formData']['user_type']) && $_SESSION['formData']['user_type'] === 'user') ? 'selected' : ''; ?>>
                User
            </option>
            <option value="admin" <?php echo (isset($_SESSION['formData']['user_type']) && $_SESSION['formData']['user_type'] === 'admin') ? 'selected' : ''; ?>>
                Admin
            </option>
        </select>

        <label for="uploaded_img">Profile Picture:</label>
        <input type="file" name="uploaded_img" accept="image/jpg, image/jpeg, image/png">

        <input type="submit" value="Insert">
    </form>
</div>

</body>
</html>

<?php
unset($_SESSION['formData']);
?>
