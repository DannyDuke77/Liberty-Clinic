<!DOCTYPE <html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LHWC - Check-in & Status Update</title>
    <link rel="stylesheet" href="main.css">
    <link rel="icon" href="http://localhost/Hospital%20Management/icon.jpg" type="image/jpeg">
</head>
<style>
    .input:hover{
        cursor: not-allowed;
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
<body>
<?php session_start();
if (!isset($_SESSION['userId'])) {
    header("Location: index.php");
    exit();
}

 require 'nav.php'; ?>

<?php
    // Initialize variables
    $patId = $patName = $status = $error_message = $success_message = '';
    // Initialize success and error icons
    $success_icon = "<i class=\"fa fa-check-square-o\" aria-hidden=\"true\"></i>";
    $error_icon = "<i class=\"fa fa-exclamation-circle\" aria-hidden=\"true\"></i>";

    // Connect to database
    require_once 'db_config.php';

    if($conn != null) {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize and validate form data
            $patId = htmlspecialchars($_POST['patId']);
            $status = htmlspecialchars($_POST['status']);
    
            // Validate form data
            if (empty($patId) || empty($status)) {
                $error_message = $error_icon . " Please fill in all the required fields";
            } else{
                try{

                    // Select name of patient
                    $stmt_check_patient = $conn->prepare("SELECT Name FROM patientsRecords WHERE patientId = :patId");
                    $stmt_check_patient->bindParam(':patId', $patId);
                    $stmt_check_patient->execute();
                    $patName = $stmt_check_patient->fetchColumn();

                        // Update payment status
                        $stmt_update_payment = $conn->prepare("UPDATE PendingPayments SET paymentStatus = :status WHERE patientId = :patId");
                        $stmt_update_payment->bindParam(':status', $status);
                        $stmt_update_payment->bindParam(':patId', $patId);
                        $stmt_update_payment->execute();
                        $success_message = $success_icon . " Payment status updated successfully for <b>[ID : LHWC/PAT/" . $patId . " : " . $patName . "]</b>";

                        // Reset form
                        $patId  = $status = '';

                } catch(PDOException $e) {
                    //$error_message = $error_icon . " " . $e->getMessage();
                    $error_message = $error_icon . " Error! Contact your administrator.";
                }
            }


        }
    }
?>

<div class="title-head"><p>Update Payment Status</p> <p class="username">User: <?php echo $user ;?></p></div>

<form class="add-form container" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <section class="personal-info">
        <p class="form-title"><i class="fa fa-dollar" aria-hidden="true"></i> Update Payment Status</p>
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
        <label>Patient Id *<input class="input" type="number" id="patId" name="patId" placeholder="Patient Id" autocomplete="new-password" readonly value="<?php echo isset($_GET['patId']) ? htmlspecialchars($_GET['patId']) : ''; ?>"></label>
 
         
        <br>
        <label>Status *</label>
        <div class="status-options">
            <label style="background-color: #99ff99; padding: 5px; margin-right: 10px; display: none"><input type="radio" name="status" value="" checked <?php echo ($status == '' ) ? 'checked' : ''; ?>></label>
            <label style="background-color: #7fff7f; padding: 5px; margin-right: 10px;"><input type="radio" name="status" checked value="completed" <?php echo ($status == 'completed' ) ? : ''; ?>> Completed</label>
        </div>

        <input type="reset" value="Reset">
        <input type="submit" value="Update">
    </section>
</form>
</body>
</html>