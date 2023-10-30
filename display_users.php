<?php
include 'config.php';

// Retrieve user data from the database
$select_users = mysqli_query($conn, "SELECT * FROM `users`") or die('Query failed');

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Registered Users</title>

   <style>
      body {
         font-family: Arial, sans-serif;
         background-color: #f4f4f4;
         margin: 0;
         padding: 0;
      }

      .table-container {
         width: 80%;
         margin: 50px auto;
         background-color: #fff;
         padding: 20px;
         box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
         border-radius: 8px;
      }

      table {
         width: 100%;
         border-collapse: collapse;
         margin-top: 20px;
      }

      th, td {
         padding: 12px;
         text-align: left;
         border-bottom: 1px solid #ddd;
      }

      th {
         background-color: #f2f2f2;
      }

      h3 {
         text-align: center;
         color: #333;
      }
   </style>
</head>
<body>

   <div class="table-container">
      <h3>Registered Users</h3>
      <table>
         <thead>
            <tr>
               <th>ID</th>
               <th>Name</th>
               <th>Email</th>
               <th>User Type</th>
            </tr>
         </thead>
         <tbody>
            <?php
            // Display user data in the table
            while ($row = mysqli_fetch_assoc($select_users)) {
               echo "<tr>";
               echo "<td>{$row['id']}</td>";
               echo "<td>{$row['name']}</td>";
               echo "<td>{$row['email']}</td>";
               echo "<td>{$row['user_type']}</td>";
               echo "</tr>";
            }
            ?>
         </tbody>
      </table>
   </div>

</body>
</html>