<?php
session_start();
if (!isset($_SESSION['userId'])) {
    header("Location: index.php");
    exit();
}
require_once 'db_config.php';


$error_icon = '<i class="fas fa-exclamation-circle" aria-hidden="true"></i>';
$success_icon = '<i class="fas fa-check-circle" aria-hidden="true"></i>';

// Initialize variables
$row = array();
$patId = isset($_POST['patId']) ? htmlspecialchars($_POST['patId']) : '';
$patName = isset($_POST['patName']) ? htmlspecialchars($_POST['patName']) : '';

if($conn != null) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
            if (empty($patId) && empty($patName)) {
                $error_message = $error_icon . " Please fill in at least one field";
            } elseif (!empty($patId) && !empty($patName)) {
                // Both ID and name provided, check if they match in the database
                $sql = "SELECT patientId, name, age, gender, phoneNumber, email, address 
                        FROM patientsRecords 
                        WHERE patientId = :patId AND name = :patName";

                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':patId', $patId);
                $stmt->bindParam(':patName', $patName);
                $stmt->execute();

                $row = $stmt->fetch();

                if (!$row) {
                    $error_message = $error_icon . " Patient not found <b>[ID: LHWC/PAT/" . $patId ."]</b> does not match <b>[".    $patName . "]</b>";
                }
            } else {
                // Only one of ID or name provided, proceed with regular search
                $sql = "SELECT patientId, name, age, gender, phoneNumber, email, address 
                        FROM patientsRecords 
                        WHERE patientId = :patId OR name = :patName";

                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':patId', $patId);
                $stmt->bindParam(':patName', $patName);
                $stmt->execute();

                $row = $stmt->fetch();

                if (!$row && !empty($patId)) {
                    $error_message = $error_icon . " Patient <b>[LHWC/PAT/" . $patId . "]</b> does not exist!";
                } elseif (!$row && !empty($patName)){
                    $error_message = $error_icon . " Patient <b>" . $patName . "</b> does not exist!";
                } elseif($row && !empty($patId)) {
                    $success_message = $success_icon . " Patient <b>[ID: LHWC/PAT/" . $patId .  "]</b> found";

                    $patId = "";
                } else {
                    $success_message = $success_icon . " Patient <b>" . $patName . "</b> found";

                    $patName = "";
                }
            }
        } catch(PDOException $e) {
            // Display error message if database connection fails
            $error_message = $error_icon . "Database is not connected"; 
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LHWC - Search Patient</title>
    <link rel="stylesheet" href="main.css">
    <link rel="icon" href="http://localhost/Hospital%20Management/icon.jpg" type="image/jpeg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        
        #patientDetails{
            margin-bottom: 15px;
            color: #3a506b;
            font-weight: 600;
            font-size: 18px;
            margin-top: 15px;
            margin-left: 15px;
            margin-right: 15px;
            border: 1px solid #adb5bd;
            padding: 10px;
            border-radius: 5px;
            background-color: #e9e9e9;
            box-shadow: 0px 0px 5px 0px #adb5bd;
            text-align: left;
            font-family: Arial, sans-serif;

        }
        #patientDetails p{
            margin-top: 0;
            padding: 0;
            margin-bottom: 5px;
            font-weight: 500;
            font-size: 15px;
        }
        #patientDetails h2{
            margin-top: 0;
            padding: 10px;
            margin-bottom: 15px;
            text-transform: uppercase;
            text-decoration: underline;
        }
        #patientDetails p:hover{
            filter: brightness(210%);
            cursor: default;
        }
        .container{
            padding-top: 20px;
            padding-bottom: 5%;
        }
        .fa-user{
            margin-right: 5px;
        }
        input[type="submit"]{
            margin-top: 10px;
        }
        input[type="reset"]{
            margin-top: 10px;
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
<?php 


 require 'nav.php'; ?>

<div class='title-head'><p>Search Patient</p> <p class="username">User: <?php echo $user; ?></p></div>
<div class="container">
    <form class="search-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <section class='info'>
            <p class='form-title'><i class='fas fa-search' aria-hidden='true'></i> Search Patient</p>
            <hr>

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

            <p style="margin-bottom: 5px; font-size: 13px"><i class="fa fa-info-circle" aria-hidden="true"></i> You can search by Patient Id, or Patient Name, or both</p>

            <label for="patId">Patient ID:<input type="text" id="patId" name="patId" autocomplete="new-password" placeholder="Enter Patient ID" value="<?php echo htmlspecialchars($_GET['patId'] ?? ''); ?>"></label>

            <label for="patName">Patient Name:<input type="text" id="patName" name="patName" autocomplete="new-password" placeholder="Enter Patient Name" value="<?php echo $patName; ?>"></label>
        </section>
        <input type="submit" value="Search">
        <input type="reset" value="Reset">
    </form>

    <?php 
        if (!empty($row)) {
            echo "<div id='patientDetails'>";
            echo "<h2><i class='fas fa-user' aria-hidden='true'></i>General Details</h2>";
            echo "<p><b>Patient ID:</b> LHWC/PAT/" . $row['patientId'] . "</p>";
            echo "<p><b>Name:</b> " . $row['name'] . "</p>";
            echo "<p><b>Age:</b> " . $row['age'] . "</p>";
            echo "<p><b>Gender:</b> " . $row['gender'] . "</p>";
            echo "<p><b>Phone Number:</b> " . $row['phoneNumber'] . "</p>";
            echo "<p><b>Email:</b> " . $row['email'] . "</p>";
            echo "<p><b>Address:</b> " . $row['address'] . "</p>";
            echo "</div>"; 
        }
    ?>
</div>
<script>
    const closeBtn = document.getElementById('closeBtn');
    const patientTable = document.getElementById('patientTable');
    const success = document.getElementById('success');

    closeBtn.addEventListener('click', () => {
        patientTable.style.display = 'none';
        closeBtn.style.display = 'none';
        success.style.display = 'none';
    })
</script>
</body>
</html>

<!--
    // Table styles
        <style>
            table{
            width: 97%;
            border-collapse: collapse;
            margin: 10px auto;
            font-size: 15px;
            }
            th{
                border-top: 3px solid #adb5bd;
                border-bottom: 3px solid #adb5bd;
                border-right: 1px solid #adb5bd;
                border-left: 1px solid #adb5bd;
                padding: 10px 5px 10px 8px;
                text-align: left;
                color: #3a506b;
            }
            td{
                border: 1px solid #adb5bd;
                padding: 10px 5px 10px 8px;
                color: #3a506b;
                font-size: 14px;
                font-weight: 500;
            }
            tr:nth-child(even){
                background-color: #e9e9e9;
            }
            tr:hover{
                background-color: #f5f5f5;
            }
        </style>


    // Display patient details in a table
        echo "<table id='patientTable'><i id='closeBtn' style=\" cursor: pointer; font-size: 20px; margin-left: 30px; margin-top: 30px; \" title=\"Close Table \"  class=\"fa fa-times-circle\" aria-hidden=\"true\"></i>";
        echo "<thead>";
        echo "<tr>
            <th style='width: 25px;'></th>
            <th style='width: 70px;'>Patient ID</th>
            <th style='width: 150px;'>Patient Name</th>
            <th style='width: 80px;'>Age</th>
            <th style='width: 80px;'>Gender</th>
            <th style='width: 150px;'>Phone Number</th>
            <th style='width: 200px;'>Email</th>
            <th style='width: 200px;'>Address</th>
        </tr>";
        echo "</thead>";
        echo "<tbody>";
        echo "<tr>";
        echo "<td><input type='checkbox' name='selected_users[]' value='" . $row['patientId'] . "'></td>";
        echo "<td>LHWC/PAT/" . $row['patientId'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['age'] . "</td>";
        echo "<td>" . $row['gender'] . "</td>";
        echo "<td>" . $row['phoneNumber'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['address'] . "</td>";
        echo "</tr>";
        echo "</tbody>";
        echo "</table>";
-->

