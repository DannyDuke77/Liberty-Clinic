<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LHWC - Add Patient</title>
    <link rel="stylesheet" href="main.css">
    <link rel="icon" href="http://localhost/Hospital%20Management/icon.jpg" type="image/jpeg">
    <style>
        .title-head .username{
            font-size: 17px;
            font-family:monospace;
            font-weight: 600;
            margin-top: 15px;
            margin-right: 20px;
            float: right;
            color: whitesmoke;
            background-color: #3a506b;
            padding: 5px 10px;
            border-radius: 5px;

        }
    </style>
</head>
<body>

<?php
session_start();
if (!isset($_SESSION['userId'])) {
    header("Location: index.php");
    exit();
}

 require 'nav.php';
// Include database configuration file
require_once 'db_config.php';

?>

<?php
// Initialize variables
$name = '';
$dob = '';
$age = '';
$email = '';
$phone = '';
$gender = '';
$address = '';
$dept = '';
$file = '';

// Initialize success and error messages
$success_message = "";
$error_message = "";
$success_icon = "<i class=\"fa fa-check-square-o\" aria-hidden=\"true\"></i>";
$error_icon = "<i class=\"fa fa-exclamation-circle\" aria-hidden=\"true\"></i>";

// Check if database connection was successful
if($conn != null) {
    // Check if the form has been submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Sanitize and validate form data
        $name = htmlspecialchars($_POST['name']);
        $dob = $_POST['dob']; // No need to sanitize date of birth
        $age = htmlspecialchars($_POST['age']);
        $email = htmlspecialchars($_POST['email']);
        $phone = htmlspecialchars($_POST['phone']);
        $gender = htmlspecialchars($_POST['gender']);
        $address = htmlspecialchars($_POST['address']);


        // Validate form data
        if (empty($name) || empty($dob) || empty($age) || empty($gender)) {
            $error_message = $error_icon . " Please fill in all the required fields";
        } elseif(!empty($name) && !preg_match("/^[a-zA-Z' ]*$/" , $name)){
            $error_message = $error_icon . "Only letters and whitespace allowed for name";
        }elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = $error_icon . " Invalid email format";
        } elseif (!empty($phone) && !preg_match("/^\d{10}$/", $phone)) {
            $error_message = $error_icon . " Invalid phone number format";
        } else {
            try {
                // Prepare and execute SQL query
                $sql = "INSERT INTO patientsRecords (Name, DateOfBirth, Age, Email, PhoneNumber, Gender, address)
                VALUES (:name, :dob, :age, :email, :phone, :gender, :address)";

                // Prepare statement
                $stmt = $conn->prepare($sql);

                // Bind parameters
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':dob', $dob);
                $stmt->bindParam(':age', $age);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':phone', $phone);
                $stmt->bindParam(':gender', $gender);
                $stmt->bindParam(':address', $address);

                //Execute into patientsRecords
                $stmt->execute();

                //Get last inserted ID and name
                $patientId = $conn->lastInsertId();
                $patientName = $name;

                // Reset form data
                $name = '';
                $dob = '';
                $age = '';
                $email = '';
                $phone = '';
                $gender = '';
                $address = '';

                // Display success message
                $success_message = $success_icon . " New patient <b>[ ID: LHWC/PAT/" . $patientId . " : " . $patientName . " ]</b> inserted successfully. <a href='add-appointment.php'>Add Appointment</a>.";

                $db_conn_suc = "";

            } catch (PDOException $e) {
                // Display error message
                $error_message = $error_icon . " Error:<a href='add-patient.php'> Try Again</a>";
            }
        }
    }

} else {
    $db_conn_err = "<p class=\"alert alert-danger\" style='background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px; padding: 10px; margin: 20px 0; font-family: Arial, sans-serif;'>
    <i class=\"fa fa-exclamation-circle\" aria-hidden=\"true\"></i>
    <strong>WARNING:</strong> Could not connect to the database. Please contact your administrator!
    <a onclick=\"location.reload();\" style=\"float: right; cursor: pointer; font-size: 17px; margin-right: 10px; \" class=\"fa fa-refresh\" aria-hidden=\"true\"> Retry Connection</a>
</p>";
}
?>

<div class="title-head"><p>Add Patient</p> <p class="username">User: <?php echo $user ;?></p></div>

<!-- Add Patient Form -->
<form class="add-form container" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <section class="personal-info">
        <p class="form-title"><i class="fa fa-wheelchair" aria-hidden="true"></i> Add Patient</p>
        <hr>
        <p class="key"><span> (*)</span> Required field</p>
    
        <!-- Display success and error messages -->
        <?php if (!empty($db_conn_err)): ?>
            <p class="db-err"><?php echo $db_conn_err; ?></p>
        <?php endif; ?>
        
        <?php if (!empty($db_conn_suc)): ?>
            <p class="db-suc"><?php echo $db_conn_suc; ?></i></p>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <p class="message success" style="margin-top: -10px;"><?php echo $success_message . "<i style=\"float: right;\ cursor: pointer; font-size: 20px; margin-right: 10px; \" title=\"Close \" onclick=\"this.parentElement.style.display='none';\" class=\"fa fa-times-circle\" aria-hidden=\"true\"></i>"; ?></p>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <p class="message error" style="margin-top: -10px;"><?php echo $error_message. "<i style=\"float: right;\ cursor: pointer; font-size: 20px; margin-right: 10px; \" title=\"Close \" onclick=\"this.parentElement.style.display='none';\" class=\"fa fa-times-circle\" aria-hidden=\"true\"></i>"?></p>
        <?php endif; ?>

        <p class="alert-add" style=" color: navy; font-weight: 400; font-size: 15px;  margin-bottom: 20px"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Fill in this form ONLY if patient is new. If patient already exists, please <a href="add-appointment.php">check in</a> or <a href="search-patient.php">search</a></p>

        <!-- Add Patient Form -->
        <label>Patient Name *<input type="text" id="name" name="name" placeholder="Patient Name" autocomplete="new-password" value="<?php echo $name; ?>"></label>

        <label>Date Of Birth *<input type="date" id="dob" name="dob" value="<?php echo $dob; ?>"></label>
        
        <label>Age *<input type="number" id="age" name="age" min="1" placeholder="Age" autocomplete="off" value="<?php echo $age; ?>" ></label>

        <label>Email<input type="text" id="email" name="email" placeholder="Email" autocomplete="new-password" value="<?php echo $email; ?>"></label> 
        
        <label>Phone<input type="tel" id="phone" name="phone" placeholder="Phone Number" autocomplete="new-password" value="<?php echo $phone; ?>"></label>

        <label for="gender">Gender *
            <select id="gender" name="gender">
                <option value="">--- Select gender ---</option>
                <option value="male" <?php echo ($gender == 'male') ? 'selected' : '';?> >Male</option>
                <option value="female" <?php echo ($gender == 'female') ? 'selected' : '';?> >Female</option>
                <option value="other" <?php echo ($gender == 'other') ? 'selected' : '';?> >Other</option>
            </select>
        </label>
        <label>Address<textarea id="address" name="address" placeholder="Address" autocomplete="new-password"><?php echo $address; ?></textarea></label>

        </label>
        
    </section>

    <input type="reset" value="Reset">
    <input type="submit" value="Submit">
    
</form>

</body>
</html>