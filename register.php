<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LHCW - Register</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" href="http://localhost/Hospital%20Management/icon.jpg" type="image/jpeg">
</head>
<body>
<?php
session_start();
if(!isset($_SESSION['userId'])){
        header("Location: index.php");
        exit();
}
// Connect to the database
require_once 'db_config.php';



?>

<div class="login-page">
      <div class="form">
        <div class="login">
          <div class="login-header">
            <h1><i class="fa fa-user-plus" aria-hidden="true"></i></h1>
            <h3>LHCW SIGN UP</h3>
            <p>Please enter your credentials to register.</p>
            <?php if (!empty($db_conn_err)): ?>
            <p class="db-err"><?php echo $db_conn_err; ?></p>
          <?php endif; ?>
        
          <?php if (!empty($db_conn_suc)): ?>
            <p class="db-suc"><?php echo $db_conn_suc; ?></i></p>
          <?php endif; ?>

            <!-- CHECK USER TYPE: ADMIN OR NORMAL-->
            <?php if($userType == "error"): ?>
              <br>
              <p class="note"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Database is down!</p>
            <?php endif; ?>
            <?php if($userType == "normal"): ?>
              <br>
              <p class="note"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> You are not an administrator!</p>
            <?php endif; ?>

            <?php if($userType == "administrator"): ?>
            <?php
              $error_message = "";
              $success_message = "";
              
              // Define error and success icons
              $error_icon = "<i class=\"fa fa-exclamation-circle\" aria-hidden=\"true\"></i>";
              $success_icon = "<i class=\"fa fa-check-square-o\" aria-hidden=\"true\"></i>";
              
              if($_SERVER["REQUEST_METHOD"] == "POST") {
                  $userId = htmlspecialchars($_POST["userId"]);
                  $userName = htmlspecialchars($_POST["userName"]);
                  $userPassword = htmlspecialchars($_POST["password"]);
                  $confirmPassword = htmlspecialchars($_POST["confirmPassword"]);
                  $userType = isset($_POST["userType"]) ? htmlspecialchars($_POST["userType"]) : "";
              
                  // Check if all fields are filled
                  if (empty($userId) || empty($userPassword) || empty($confirmPassword) || empty($userType)) {
                      $error_message = $error_icon . " Please fill in all fields";
                  } else if ($userPassword != $confirmPassword) {
                      $error_message = $error_icon . " Passwords do not match";
                  } else if (strlen($userPassword) < 8) {
                      $error_message = $error_icon . " Password must be at least 8 characters long";
                  } else if (!preg_match("#[0-9]+#", $userPassword)) {
                      $error_message = $error_icon . " Password must contain at least 1 number";
                  } else if (!preg_match('/^\w+$/', $username)) {
                      $error_message = $error_icon . " Username can only contain letters, numbers, and underscores. No whitespaces.";
                  } else {
                      // Check if the user already exists
                      $stmt = $conn->prepare("SELECT * FROM usersPasswords WHERE userId = :userId");
                      $stmt->bindParam(':userId', $userId);
                      $stmt->execute();
              
                      if ($stmt->rowCount() > 0) {
                          $error_message = $error_icon . " User already exists";
                      } else {
                          // Hash the password
                          $hashed_password = password_hash($userPassword, PASSWORD_DEFAULT);
              
                          // Insert the user into the database
                          $stmt = $conn->prepare("INSERT INTO usersPasswords (userId, userName, password, userType) VALUES (:userId, :userName, :password, :userType)");
                          $stmt->bindParam(':userId', $userId);
                          $stmt->bindParam(':userName', $userName);
                          $stmt->bindParam(':password', $hashed_password);
                          $stmt->bindParam(':userType', $userType);
                          $stmt->execute();
              
                          $success_message = $success_icon . " Registration successful - <a href=\"index.php\">Login</a>";
              
                          // Reset the form fields
                          $userId = "";
                          $userName = "";
                          $userPassword = "";
                          $confirmPassword = "";
                          $userType = "";
              
                      }
                  }
              }
              ?>
            
          </div>
        </div>
        <form class="login-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
          <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
          <?php endif; ?>

          <?php if (!empty($success_message)): ?>
            <p class="success-message"><?php echo $success_message; ?></p>
          <?php endif; ?>

          <!-- FORM INPUTS -->
          <i class="fa fa-id-card" aria-hidden="true">
          </i><input type="text" name="userId" placeholder="ID Number" autocomplete="new-password" value="<?php echo isset($_POST['userId']) ? $_POST['userId'] : ''; ?>">

          <i class="fa fa-user" aria-hidden="true">
          </i><input type="text" name="userName" placeholder="Username" autocomplete="new-password" value="<?php echo isset($_POST['userName']) ? $_POST['userName'] : ''; ?>">

          <i class="fa fa-users" aria-hidden="true"></i>
          <select name="userType">
              <option value="" disabled selected>Select User Type</option>
              <option value="administrator" <?php if (isset($_POST['userType']) && $_POST['userType'] == 'administrator') echo 'selected'; ?>>Administrator</option>
              <option value="normal" <?php if (isset($_POST['userType']) && $_POST['userType'] == 'normal') echo 'selected'; ?>>Normal</option>
          </select>


          <i class="fa fa-lock" aria-hidden="true"></i>
          <input type="password" name="password" placeholder="Password" autocomplete="new-password" value="<?php echo isset($_POST['password']) ? $_POST['password'] : ''; ?>">

          <i class="fa fa-lock" aria-hidden="true"></i>
          <input type="password" name="confirmPassword" placeholder="Confirm Password" autocomplete="new-password" value="<?php echo isset($_POST['confirmPassword']) ? $_POST['confirmPassword'] : ''; ?>">

          <input type="submit" value="Register">
        </form>
        <p class="message">Already registered? <a href="index.php">Login</a></p>
      </div>
    </div>
    <?php endif; ?>
</body>
</html>