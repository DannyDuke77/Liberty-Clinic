<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LHWC - Check-in & Status Update</title>
    <link rel="stylesheet" href="main.css">
    <link rel="icon" href="http://localhost/Hospital%20Management/icon.jpg" type="image/jpeg">
    <style>
        input[type=number], select{
            width: 340px !important;          
        }

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
<?php session_start();

if (!isset($_SESSION['userId'])) {
    header("Location: index.php");
    exit();
}

require 'nav.php'; 



// Initialize variables
$patId = $dept = $status = $payment_method = $card_number = $error_message = $success_message = '';
// Initialize success and error icons
$success_icon = "<i class=\"fa fa-check-square-o\" aria-hidden=\"true\"></i>";
$error_icon = "<i class=\"fa fa-exclamation-circle\" aria-hidden=\"true\"></i>";

// Connect to database
require_once 'db_config.php';


// Check if connection was successful
if($conn !== null) {
    
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Sanitize and validate form data
        $patId = htmlspecialchars($_POST['patId']);
        $dept = htmlspecialchars($_POST['dept']);
        $payment_method = htmlspecialchars($_POST['payment-method']);
        $card_number = htmlspecialchars($_POST['card-number']);
        $status = "waiting";
        $payment_status = "pending";

        // Validate form data
        if (empty($patId) || empty($dept) || empty($payment_method)) {
            $error_message = $error_icon . " Please fill in all the required fields";
        } else {
            try{
                // Check if patient exists
                $stmt_check_patient = $conn->prepare("SELECT Name FROM patientsRecords WHERE patientId = :patId");
                $stmt_check_patient->bindParam(':patId', $patId);
                $stmt_check_patient->execute();
                $patName = $stmt_check_patient->fetchColumn();


                // Display error if patient is not in patient records
                if(empty($patName)){
                    $error_message = $error_icon . " Patient with <b>ID : LHWC/PAT/" . $patId . "</b> not found in records. Please  <a href='add-patient.php'> add patient</a> first or <a href='search-patient.php'> search patient</a>.";
                } else {
                    // Check if there's an existing appointment for the patient
                    $stmt_check_appointment = $conn->prepare("SELECT * FROM appointments WHERE patientId = :patId");
                    $stmt_check_appointment->bindParam(':patId', $patId);
                    $stmt_check_appointment->execute();
                    $existing_appointment_status = $stmt_check_appointment->fetch();

                    // Check if there's a pending payment for the patient
                    $stmt_check_payment = $conn->prepare("SELECT * FROM pendingPayments WHERE patientId = :patId AND paymentStatus = 'pending'");
                    $stmt_check_payment->bindParam(':patId', $patId);
                    $stmt_check_payment->execute();
                    $pending_payment = $stmt_check_payment->fetch();

                    // Display error if patient already has an appointment
                    if(!empty($existing_appointment_status)){
                        $error_message = $error_icon . " Patient with <b>ID : LHWC/PAT/" . $patId . "</b> already has <b>" . $existing_appointment_status['status'] . "</b> appointment. Please <a href='add-appointment.php'> add appointment</a> first";
                    } elseif($payment_method == 2 && empty($card_number) || $payment_method == 3 && empty($card_number)) {
                        $error_message = $error_icon . " Please enter card number";
                    } elseif($payment_method == 1 && !empty($card_number) || $payment_method == 4 && !empty($card_number)){
                        $error_message = $error_icon . " Payment method <b>" . $payment_method . "</b> does not require card number";
                    } elseif(!empty($pending_payment)){
                        $error_message = $error_icon . " Patient with <b>ID : LHWC/PAT/" . $patId . "</b> has a pending payment.";
                    }elseif(!empty($card_number) && !preg_match('/^[\d-]{4}-[\d-]{4}-[\d-]{4}-[\d-]{4}$/', $card_number)) {
                        $error_message = $error_icon . " Please enter a valid card number";
                    } else {
                        // Insert new appointment

                        $sql = "INSERT INTO appointments (patientId, deptId, status, paymentMethod) VALUES (:patId, :dept, :status, :payment_method)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':patId', $patId);
                        $stmt->bindParam(':dept', $dept);
                        $stmt->bindParam(':status', $status);
                        $stmt->bindParam(':payment_method', $payment_method);
                        $stmt->execute();

                        // Add new payment
                        $sql = "INSERT INTO pendingPayments (patientId, deptId, paymentMethod, cardNumber, paymentStatus) VALUES (:patId, :dept, :payment_method, :card_number, :payment_status)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':patId', $patId);
                        $stmt->bindParam(':dept', $dept);
                        $stmt->bindParam(':payment_method', $payment_method);
                        $stmt->bindParam(':card_number', $card_number);
                        $stmt->bindParam(':payment_status', $payment_status);
                        $stmt->execute();

                        // Display success message
                        $success_message = $success_icon . " New appointment added successfully for <b>[ID: LHWC/PAT/" . $patId .   " : " . $patName . "]</b>. <a href='add-payment.php'> Proceed to Payment</a>";

                        // Reset form data
                        $patId = '';
                        $dept = '';
                        $payment_method = '';
                        $card_number = '';
                    }

                    
                }
            }
             catch(PDOException $e){
                $error_message = $error_icon . " Error: Cannot add appointment. Contact system administrator.";
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


<div class="title-head"><p>Check-In</p> <p class="username">User: <?php echo $user ;?></p></div>

<form class="add-form container" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

    <section class="personal-info">
            <p class="form-title"><i class="fa fa-plus-circle" aria-hidden="true"></i> Check-In</p>
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
                <p class="message success"><?php echo $success_message . "<i style=\"float: right;\ cursor: pointer; font-size: 20px; margin-right: 10px; \" title=\"Close \" onclick=\"this.parentElement.style.display='none';\" class=\"fa fa-times-circle\" aria-hidden=\"true\"></i>"; ?></p>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                <p class="message error"><?php echo $error_message . "<i style=\"float: right;\ cursor: pointer; font-size: 20px; margin-right: 10px; \" title=\"Close \" onclick=\"this.parentElement.style.display='none';\" class=\"fa fa-times-circle\" aria-hidden=\"true\"></i>";?></p>
            <?php endif; ?>

        <!-- Add Appointment Form -->
        <label>Patient Id *<input type="number" id="patId" name="patId" autocomplete="new-password" placeholder="Patient Id" value="<?php echo $patId; ?>"></label>

        <label for="dept">Department *
            <select id="dept" name="dept">
                <option value="">--- Select Department ---</option>
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

         <label for="method-of-payment">Method of Payment *
            <select id="method-of-payment" name="payment-method">
                <option value="">--- Select Method of Payment ---</option>
                <option value="1" <?php echo ($payment_method == '1') ? 'selected' : ''; ?>>Cash</option>
                <option value="2" <?php echo ($payment_method == '2') ? 'selected' : ''; ?>>NHIF</option>
                <option value="3" <?php echo ($payment_method == '3') ? 'selected' : ''; ?>>Insurance</option>
                <option value="4" <?php echo ($payment_method == '4') ? 'selected' : ''; ?>>MPESA</option>
            </select>
         </label>

        
            <label for="card-number">Card Number
                <input type="text" id="card-number" name="card-number" autocomplete="new-password" placeholder="Card Number" value="<?php echo $card_number; ?>">
            </label>
       
         


<br>
        <input type="reset" value="Reset">
        <input type="submit" value="Add">

    </section>

</form>
</body>
</html>