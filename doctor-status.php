<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LHCW - Doctor Status</title>
    <link rel="stylesheet" href="main.css">
    <link rel="icon" href="http://localhost/Hospital%20Management/icon.jpg" type="image/jpeg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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

    require 'nav.php'; ?>

    <?php
    // Initialize variables
    

    // Initialize success and error icons
    $success_icon = "<i class=\"fa fa-check-square-o\" aria-hidden=\"true\"></i>";
    $error_icon = "<i class=\"fa fa-exclamation-circle\" aria-hidden=\"true\"></i>";

    $docId = $status = $error_message = $success_message = '';
    // Connect to database
    require_once 'db_config.php';
    

    if($conn != null) {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {


            // Sanitize and validate form data
            $docId = htmlspecialchars($_POST['docId']);
            $status = htmlspecialchars($_POST['status']);


            // Validate form data
            if (empty($docId) || empty($status)) {
                $error_message =  $error_icon . " All fields are required. <i style=\"float: right;\ cursor: pointer; font-size:    20px; margin-right: 10px; \" title=\"Close \" onclick=\"this.parentElement.style.display='none';\" class=\"fa  fa-times-circle\" aria-hidden=\"true\"></i>";
            } else {
                try{
                    // Check value of existing doctor status
                    $stmt_check_status = $conn->prepare("SELECT status FROM doctorstatus WHERE doctorId = :docId");
                    $stmt_check_status->bindParam(':docId', $docId);
                    $stmt_check_status->execute();
                    $existing_status = $stmt_check_status->fetchColumn();


                    if($existing_status == 'inactive' && $status == 'inactive'){
                        $error_message = $error_icon . " Doctor with <b>[ID: LHCW/DOC/" . $docId . "]</b> is already inactive.";
                    } else if($existing_status == 'active' && $status =='active'){
                        $error_message = $error_icon . " Doctor with <b>[ID: LHCW/DOC/" . $docId . "]</b> is already active.";
                    } else if ($existing_status = 'active' && $status == 'inactive' || $existing_status = 'inactive' && $status == 'active'){
                        // Check if doctor exists
                        $stmt_check_doctor = $conn->prepare("SELECT doctorId FROM doctorsRecords WHERE doctorId =   :docId");
                        $stmt_check_doctor->bindParam(':docId', $docId);
                        $stmt_check_doctor->execute();
                        $doctor = $stmt_check_doctor->fetchColumn();

                        // Select doctor name
                        $stmt_get_name = $conn->prepare("SELECT name FROM doctorsRecords WHERE doctorId = :docId");
                        $stmt_get_name->bindParam(':docId', $docId);
                        $stmt_get_name->execute();
                        $docName = $stmt_get_name->fetchColumn();

                        // Display error if doctor is not in doctors records
                        if(empty($doctor)){
                            $error_message = $error_icon . " Doctor with <b>[ID: LHCW/DOC/" . $docId . "]</b> does not  exist.";
                        } else {
                            // Check if doctor status already exists
                            $stmt_check_status = $conn->prepare("SELECT * FROM doctorstatus WHERE doctorId = :docId");
                            $stmt_check_status->bindParam(':docId', $docId);
                            $stmt_check_status->execute();
                            $existing_status = $stmt_check_status->fetch();


                            // Update doctor status
                            $stmt_update_status = $conn->prepare("UPDATE doctorstatus SET status = :status WHERE    doctorId = :docId");
                            $stmt_update_status->bindParam(':docId', $docId);
                            $stmt_update_status->bindParam(':status', $status);
                            $stmt_update_status->execute();
                            $success_message = $success_icon . " Doctor <b>[ID: LHCW/DOC/" . $docId . ": " . $docName .  "]</b> status updated successfully to <b>[" . $status . "]</b>.";

                            // Reset form
                            $docId = $status = '';
                        }
                    }

                } catch(PDOException $e){
                    $error_message = $e->getMessage();

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

    <div class="title-head"><p>Change Doctor Status</p> <p class="username">User: <?php echo $user ;?></p></div>

    <form class="add-form container" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <section class="personal-info">
            <p class="form-title"><i class="fa fa-user-md" aria-hidden="true"></i> Change Doctor Status</p>
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
                <p class="message success" style="margin-top: -10px;"><?php echo $success_message . "<i style=\"float: right;\ cursor: pointer; font-size: 20px; margin-right: 10px; \" title=\"Close \"   onclick=\"this.parentElement.style.display='none';\" class=\"fa fa-times-circle\" aria-hidden=\"true\"></i>"; ?></p>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                <p class="message error" style="margin-top: -10px;"><?php echo $error_message; ?></p>
            <?php endif; ?>


            <label>Doctor Id *<input type="number" id="id" name="docId" min="1" placeholder="Doctor Id" value="<?php echo $docId; ?>"> </label>
            <br>

            <label>Status *</label>
            <div class="status-options">
                <label style="background-color: #ff9999; padding: 5px; margin-right: 10px; display: none"><input type="radio" name="status" value="" checked <?php echo ($status == '' ) ? 'checked' : ''; ?>></label>
                <label style="background-color: #99ff99; padding: 5px; margin-right: 10px;"><input type="radio" name="status" value="active" <?php echo ($status == 'active' ) ? 'checked' : ''; ?>> Active</label>
                <label style="background-color: #ff9999; padding: 5px; margin-right: 10px"><input type="radio" name="status" value="inactive" <?php echo ( $status == 'inactive') ? 'checked' : ''; ?>>Inactive</label>
            </div>

            <input type="submit" value="Update">
        </section>
    </form>
</body>
</html>



