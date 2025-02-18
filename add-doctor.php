<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LHWC Add Doctor</title>
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

if($conn !== null) {
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
        $deptId = htmlspecialchars($_POST['dept']);


        // Validate form data
        if (empty($name) || empty($dob) || empty($age) || empty($gender) || empty($deptId)) {
            $error_message = $error_icon . " Please fill in all the required fields";
        } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = $error_icon . " Invalid email format";
        } elseif (!empty($phone) && !preg_match("/^\d{10}$/", $phone)) {
            $error_message = $error_icon . " Invalid phone number format";
        } else {
            try {
               // Prepare and execute SQL query for doctorsRecords table
                $sql = "INSERT INTO doctorsRecords (name, dateOfBirth, age, email, phoneNumber, gender, address, deptId)
                VALUES (:name, :dob, :age, :email, :phone, :gender, :address, :deptId)";

                //Bind parameters
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':dob', $dob);
                $stmt->bindParam(':age', $age);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':phone', $phone);
                $stmt->bindParam(':gender', $gender);
                $stmt->bindParam(':address', $address);
                $stmt->bindParam(':deptId', $deptId);

                // Execute the statement for doctorsRecords
                $stmt->execute();

                $doctorId = $conn->lastInsertId();
                $doctorName = $name;

                // Reset variables
                $name = '';
                $dob = '';
                $age = '';
                $email = '';
                $phone = '';
                $gender = '';
                $address = '';
                $dept = '';

                // Display success message

                $success_message = $success_icon . " New doctor <b>[ ID: LHWC/DOC/" . $doctorId . " : " . $doctorName . " ]</b> inserted successfully. Status set to <b>active</b>. <a href=\"doctor-status.php\">Change Status</a>
                <i style=\"float: right;\ cursor: pointer; font-size: 20px; margin-right: 10px; \" title=\"Close \" onclick=\"this.parentElement.style.display='none';\" class=\"fa fa-times-circle\" aria-hidden=\"true\"></i>";

                $db_conn_suc = "";

            } catch (PDOException $e) {
                // Display error message
                $error_message = $error_icon . " Error: " . $e->getMessage();
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
<div class="title-head"><p>Add Doctor</p> <p class="username">User: <?php echo $user ;?></p></div>

<form class="add-form container" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <section class="personal-info">
        <p class="form-title"><i class="fa fa-user-md" aria-hidden="true"></i> Add Doctor</p>
        <hr>
        <p class="key"><span> (*)</span> Required field</p>

        <!-- Display success and error messages -->
        
        <?php if (!empty($db_conn_err)): ?>
            <p class="db-err"><?php echo $db_conn_err; ?></p>
        <?php endif; ?>
   
        <?php if (!empty($db_conn_suc)): ?>
            <p class="db-suc"><?php echo $db_conn_suc; ?></p>
        <?php endif; ?>
        
        <?php if (!empty($success_message)): ?>
            <p class="message success"><?php echo $success_message; ?></p>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <p class="message error"><?php echo $error_message; ?></p>
        <?php endif; ?>


        <label>Doctor Name *<input type="text" id="name" name="name" placeholder="Doctor Name" autocomplete="new-password" value="<?php echo $name; ?>"></label>

        <label>Date Of Birth *<input type="date" id="dob" name="dob" value="<?php echo $dob;?>"></label>
        
        <label>Age *<input type="number" id="age" name="age" min="1" placeholder="Age" value="<?php echo $age; ?>"></label>

        <label>Email *<input type="text" id="email" name="email" placeholder="Email" autocomplete="new-password" value="<?php echo $email; ?>"></label> 
        
        <label>Phone *<input type="tel" id="phone" name="phone" placeholder="Phone Number" autocomplete="new-password" value="<?php echo $phone; ?>",></label>

        <label for="gender">Gender *
            <select id="gender" name="gender">
                <option value="">--- Select gender ---</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select>
        </label>
        <label>Address *<textarea id="address" name="address" placeholder="Address" autocomplete="new-password" value="<?php echo $address; ?>"></textarea></label>

        <label for="dept">Department to serve *
        <select id="dept" name="dept">
            <option value="">--- Select department ---</option>
            <option value="1" <?php echo ($dept == '1') ? 'selected' : ''; ?>>Radiology</option>
            <option value="2" <?php echo ($dept == '2') ? 'selected' : ''; ?>>Cardiology</option>
            <option value="3" <?php echo ($dept == '3') ? 'selected' : ''; ?>>Dentistry</option>
            <option value="4" <?php echo ($dept == '4') ? 'selected' : ''; ?>>Oncology</option>
            <option value="5" <?php echo ($dept == '5') ? 'selected' : ''; ?>>Immunology</option>
            <option value="6" <?php echo ($dept == '6') ? 'selected' : ''; ?>>Gastroenterology</option>
            <option value="7" <?php echo ($dept == '7') ? 'selected' : ''; ?>>Hematology</option>
            <option value="8" <?php echo ($dept == '8') ? 'selected' : ''; ?>>Nephrology</option>
            <option value="9" <?php echo ($dept == '9') ? 'selected' : ''; ?>>Urology</option>
            <option value="10" <?php echo ($dept == '10') ? 'selected' : ''; ?>>Orthopedics</option>
            <option value="11" <?php echo ($dept == '11') ? 'selected' : ''; ?>>Gynaecology</option>
            <option value="12" <?php echo ($dept == '12') ? 'selected' : ''; ?>>ENT</option>
            <option value="13" <?php echo ($dept == '13') ? 'selected' : ''; ?>>Dermatology</option>
            <option value="14" <?php echo ($dept == '14') ? 'selected' : ''; ?>>Optics</option>
            <option value="15" <?php echo ($dept == '15') ? 'selected' : ''; ?>>Neurology</option>
            <option value="16" <?php echo ($dept == '16') ? 'selected' : ''; ?>>Pedeatrics</option>
            <option value="17" <?php echo ($dept == '17') ? 'selected' : ''; ?>>Surgery</option>
            <option value="18" <?php echo ($dept == '18') ? 'selected' : ''; ?>>Physiatrics</option>
        </select>

        </label>
        
    </section>

    <input type="reset" value="Reset">
    <input type="submit" value="Submit">
</form>

</body>
</html>