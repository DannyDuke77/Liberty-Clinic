<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LHWC - Check-in & Status Update</title>
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
<?php session_start();
if (!isset($_SESSION['userId'])) {
    header("Location: index.php");
    exit();
}

 require 'nav.php'; ?>

<?php
    // Initialize variables
    $patId = $dept = $status = $error_message = $success_message = '';
    // Initialize success and error icons
    $success_icon = "<i class=\"fa fa-check-square-o\" aria-hidden=\"true\"></i>";
    $error_icon = "<i class=\"fa fa-exclamation-circle\" aria-hidden=\"true\"></i>";

    // Connect to database
    require_once 'db_config.php';

    if($conn != null) {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize and validate form data
            $patId = htmlspecialchars($_POST['patId']);
            $dept = htmlspecialchars($_POST['dept']);
            $status = htmlspecialchars($_POST['status']);

            // Validate form data
            if (empty($patId) || empty($dept) || empty($status)) {
                $error_message = $error_icon . " Please fill in all the required fields";
            } else {
                try{
                    // Check if there's an existing appointment for the patient
                    $stmt_check_appointment = $conn->prepare("SELECT * FROM appointments WHERE patientId = :patId");
                    $stmt_check_appointment->bindParam(':patId', $patId);
                    $stmt_check_appointment->execute();
                    $existing_appointment = $stmt_check_appointment->fetch();

                    // Select patient name
                    $stmt_check_patient = $conn->prepare("SELECT Name FROM patientsRecords WHERE patientId = :patId");
                    $stmt_check_patient->bindParam(':patId', $patId);
                    $stmt_check_patient->execute();
                    $patName = $stmt_check_patient->fetchColumn();

                    // Display error if patient is not in patient records
                    if(empty($existing_appointment)){
                        $error_message = $error_icon . " Patient with <b>ID : LHWC/PAT/" . $patId . "</b> not found in appointments. Please <a href='add-appointment.php'> add appointment</a> first";
                    } else {
                        // If there's an existing appointment
                        if($existing_appointment) {
                            // Check if the existing appointment is completed or cancelled
                            if($existing_appointment['status'] == 'completed' || $existing_appointment['status'] == 'cancelled') {
                                // If the existing appointment is completed or cancelled, create a new appointment
                                $error_message = $error_icon . " Cannot update a <b>" . $existing_appointment['status'] . "</b> appointment for <b>[ID : LHWC/PAT/" . $patId . " : " . $patName . "]</b> Please <a href='add-appointment.php'> add new appointment</a>";
                            } else {
                                // Check if the status of the existing appointment matches the status of the new appointment
                                if ($existing_appointment['status'] == $status) {
                                    // If the status of the existing appointment matches the status of the new appointment
                                    $error_message = $error_icon . " Already <b>" . $existing_appointment['status'] . "</b> appointment for <b>[ID : LHWC/PAT/" . $patId . " : " . $patName . "]</b>.";
                                } else {
                                    // Update the status of the existing appointment
                                    $sql = "UPDATE appointments SET deptId = :dept, status = :status WHERE patientId = :patId";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bindParam(':dept', $dept);
                                    $stmt->bindParam(':status', $status);
                                    $stmt->bindParam(':patId', $patId);
                                    $stmt->execute();

                                    $success_message = $success_icon . " Appointment status updated successfully for <b>[ID: LHWC/PAT/" . $patId . " : " . $patName . "]</b> from " . "<b>" . $existing_appointment['status'] . "</b> to <b>" . $status . "</b>.
                                    <i style=\"float: right;\ cursor: pointer; font-size: 20px; margin-right: 10px; \" title=\"Close \" onclick=\"this.parentElement.style.display='none';\" class=\"fa fa-times-circle\" aria-hidden=\"true\"></i>";
                                }
                            }
                        }
                        // Reset form data
                        $patId = '';
                        $dept = '';
                        $status = '';
                    }
                } catch(PDOException $e){
                    $error_message = $error_icon . " " . $e->getMessage();
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

<?php
// Retrieve the appointment ID from the URL parameter
$appointmentId = isset($_GET['id']) ? $_GET['id'] : '';

// Initialize variables to store appointment details
$patId = $dept = $status = '';

// Check if the appointment ID is not empty
if (!empty($appointmentId)) {
    // Include database configuration
    require_once 'db_config.php';

    // Prepare and execute the query to fetch appointment details
    $stmt = $conn->prepare("SELECT * FROM appointmentsview WHERE checkInID = :appointmentId");
    $stmt->bindParam(':appointmentId', $appointmentId, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch appointment details
    $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

    // If appointment details are found, populate the input fields
    if ($appointment) {
        $patId = $appointment['patientId'];
        $dept = $appointment['deptName'];
        $status = $appointment['status'];
    } else {
        // Handle case when appointment details are not found
        echo "<p>Error: Appointment details not found.</p>";
    }
}
?>


<div class="title-head"><p>Update Appointment Status</p> <p class="username">User: <?php echo $user ;?></p></div>

<form class="add-form container" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

    <section class="personal-info">
            <p class="form-title"><i class="fa fa-plus-circle" aria-hidden="true"></i> Update Appointment Status</p>
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
                <p class="message error"><?php echo $error_message;?></p>
            <?php endif; ?>

        <!-- Add Appointment Form -->
        <label>Patient Id *<input type="number" id="patId" name="patId" placeholder="Patient Id" readonly autocomplete="new-password" value="<?php echo $patId; ?>"></label>

        <label>Department *<input type="text" id="dept" name="dept" placeholder="Department" readonly autocomplete="new-password" value="<?php echo $dept; ?>"></label>
        

        <!--<label for="dept">Department *
            <select id="dept" name="dept">
                <option value="">--- Select Department ---</option>
                <option value="1" <?php echo ($dept == 'Radiology') ? 'selected' : ''; ?>>Radiology</option>
                <option value="2" <?php echo ($dept == 'Cardiology') ? 'selected' : ''; ?>>Cardiology</option>
                <option value="3" <?php echo ($dept == 'Dentistry') ? 'selected' : ''; ?>>Dentistry</option>
                <option value="4" <?php echo ($dept == 'Oncology') ? 'selected' : ''; ?>>Oncology</option>
                <option value="5" <?php echo ($dept == 'Immunology') ? 'selected' : ''; ?>>Immunology</option>
                <option value="6" <?php echo ($dept == 'Gastroenterology') ? 'selected' : ''; ?>>Gastroenterology</option>
                <option value="7" <?php echo ($dept == 'Hematology') ? 'selected' : ''; ?>>Hematology</option>
                <option value="8" <?php echo ($dept == 'Nephrology') ? 'selected' : ''; ?>>Nephrology</option>
                <option value="9" <?php echo ($dept == 'Urology') ? 'selected' : ''; ?>>Urology</option>
                <option value="10" <?php echo ($dept == 'Orthopedics') ? 'selected' : ''; ?>>Orthopedics</option>
                <option value="11" <?php echo ($dept == 'Gynaecology') ? 'selected' : ''; ?>>Gynaecology</option>
                <option value="12" <?php echo ($dept == 'ENT') ? 'selected' : ''; ?>>ENT</option>
                <option value="13" <?php echo ($dept == 'Dermatology') ? 'selected' : ''; ?>>Dermatology</option>
                <option value="14" <?php echo ($dept == 'Optics') ? 'selected' : ''; ?>>Optics</option>
                <option value="15" <?php echo ($dept == 'Neurology') ? 'selected' : ''; ?>>Neurology</option>
                <option value="16" <?php echo ($dept == 'Pedeatrics') ? 'selected' : ''; ?>>Pedeatrics</option>
                <option value="17" <?php echo ($dept == 'Surgery') ? 'selected' : ''; ?>>Surgery</option>
                <option value="18" <?php echo ($dept == 'Physiatrics') ? 'selected' : ''; ?>>Physiatrics</option>
            </select>
         </label> -->
        
         
<br>
         <label>Status *</label>
            <div class="status-options">
                <label style="background-color: #99ff99; padding: 5px; margin-right: 10px; display: none"><input type="radio" name="status" value="" checked <?php echo ($status == '' ) ? 'checked' : ''; ?>></label>
                <label style="background-color: #cccccc; padding: 5px; margin-right: 10px;"><input type="radio" name="status" value="waiting" <?php echo ($status == 'waiting' ) ? 'checked' : ''; ?>> Waiting</label>
                <label style="background-color: #ffb366; padding: 5px"><input type="radio" name="status" value="in progress" <?php echo ( $status == 'in progress') ? 'checked' : ''; ?>>In Progress</label>
                <label style="background-color: #7fff7f; padding: 5px; margin-right: 10px"><input type="radio" name="status" value="completed" <?php echo ( $status == 'completed') ? 'checked' : ''; ?>>Completed</label>
                <label style="background-color: #ff9999; padding: 5px; margin-right: 10px"><input type="radio" name="status" value="cancelled" <?php echo ( $status == 'cancelled') ? 'checked' : ''; ?>>Cancelled</label>
                
            </div>

        <input type="reset" value="Reset">
        <input type="submit" value="Update">

    </section>

</form>
</body>
</html>