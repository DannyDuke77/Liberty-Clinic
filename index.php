<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="styles.css">
  <link rel="icon" href="http://localhost/Hospital%20Management/icon.jpg" type="image/jpeg">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <title>LHCW - Login</title>
  <style>
    .message{
      color: red;
    }
  </style>
</head>
<body>
<?php

require_once 'db_config.php';
require_once 'session.php';

$error_message = "";
$success_message = "";

// Define error and success icons
$error_icon = "<i class=\"fa fa-exclamation-circle\" aria-hidden=\"true\"></i>";
$success_icon = "<i class=\"fa fa-check-square-o\" aria-hidden=\"true\"></i>";

if($conn !== null){
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = htmlspecialchars($_POST["userId"]);
    $password = htmlspecialchars($_POST["password"]);

    // Select the user from the database
    $stmt = $conn->prepare("SELECT password FROM usersPasswords WHERE userId = :userId");
    $stmt->bindParam(':userId', $userId);
    $stmt->execute();
    
    // Check if the user exists
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch();
        
        // Verify the password
        $stored_hashed_password = $row['password'];

        if (password_verify($password, $stored_hashed_password)) {
          // Authentication successful
          session_start();
          $_SESSION['userId'] = $userId;
          // Redirect to the dashboard
            header("Location: dashboard.php");
            exit;
        } else {
            // Password is incorrect
            $error_message = $error_icon . " Invalid username or password";
        }
    } else {
        // User does not exist
        $error_message = $error_icon . " Invalid username or password";
    }
}}
?>

    <div class="login-page">
      <div class="form">
        <div class="login">
          <div class="login-header">
            <h1><i class="fa fa-user-circle-o" aria-hidden="true"></i></h1>
            <h3>LHCW LOGIN</h3>
            <p>Please enter your credentials to login.</p>
          </div>
        </div>
        <form class="login-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

          <?php if (!empty($db_conn_err)): ?>
            <p class="db-err"><?php echo $db_conn_err; ?></p>
          <?php endif; ?>
        
          <?php if (!empty($db_conn_suc)): ?>
            <p class="db-suc"><?php echo $db_conn_suc; ?></i></p>
          <?php endif; ?>

          <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
          <?php endif; ?>

          <?php if (!empty($success_message)): ?>
            <p class="success-message"><?php echo $success_message; ?></p>
          <?php endif; ?>

          <i class="fa fa-id-card" aria-hidden="true"></i><input type="text" name="userId" placeholder="ID Number" autocomplete="new-password">
          <i class="fa fa-lock" aria-hidden="true"></i><input type="password" name="password" placeholder="Password" autocomplete="new-password">

          <input type="submit" value="Login">
        </form>
        <p class="message">Not registered? Contact your administrator</p>
      </div>
    </div>

</body>

</html>