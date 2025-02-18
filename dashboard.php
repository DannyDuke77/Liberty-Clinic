<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liberty Health and Wellness Clinic</title>
    <link rel="stylesheet" href="main.css">
    <link rel="icon" href="http://localhost/Hospital%20Management/icon.jpg" type="image/jpeg">
</head>
<style>
    body {
        overflow-y: scroll;
    }

    /* Styling the title head */
    .title-head {
        height: 60px;
        background-color: rgb(52, 52, 97);
        color: whitesmoke;
        font-size: 25px;
        border-radius: 0px 0px 3px 3px;
    }

    .title-head .title {
        float: left;
        margin-top: 10px;
        margin-left: 20px;
    }

    .stats-chart {
        margin-top: 20px;
        margin-left: 13px;
        font-size: 17px;
        display: inline-block;
        width: 85%;
        border-radius: 5px;
        border: 1px solid rgb(52, 52, 97);
        padding: 10px;
    }
    .stats-info{
        margin-top: 10px;
        margin-left: 20px;
        margin-bottom: 20px;
        display: inline-block;
        width: 94%;
        border-radius: 5px;
    }
    .stats{
        width: 180px;
        height: 180px;
        padding: 10px;
        margin-right: 1%;
        margin-left: 20px;
        display: inline-block;
        border-radius: 10px 60px;
        border: 1px solid rgb(52, 52, 97);
        background-color: #edf2f4;
    }
    .stats:hover{
        background-color: #f8f9fa;
        transition: 0.5s; 
        transform: scale(1.1);
        cursor: pointer;
        box-shadow: 0px 14px 16px 0px rgba(0, 0, 0, 0.3);
    }
    .stats-chart:hover{
        background-color: #f8f9fa;
        cursor: pointer;
        box-shadow: 0px 14px 16px 0px rgba(0, 0, 0, 0.2);
    }
    .stats h2{
        font-size: 18px;
        font-weight: 600;
        margin-top: 15px;
        text-align: center;
    }
    .stats p{
        margin-right: 10px;
        margin-top: 20px;
        text-align: center;
        font-size: 50px;
        font-weight: 500;
        color: rgb(52, 52, 97);
    }
    .fa-heart{
        animation: pulse 1.2s infinite;
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
    @keyframes pulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.2);
        }
        100% {
            transform: scale(1);
        }
    }
    @media screen and (max-width: 600px){
        .stats-chart{
            width: 89% !important;
            margin-left: 5px !important;
        }
        body{
            overflow-x: hidden;
        }

    }
</style>
<body>
<?php
session_start();
if (!isset($_SESSION['userId'])) {
    header("Location: index.php");
    exit();
}

require 'nav.php';
require_once 'db_config.php';


try {
    // Establish database connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get total number of patients
    $stmt = $conn->prepare("SELECT COUNT(*) FROM patientsRecords");
    $stmt->execute();
    $total_patients = $stmt->fetchColumn();

    // Get total number of doctors
    $stmt = $conn->prepare("SELECT COUNT(*) FROM doctorsRecords");
    $stmt->execute();
    $total_doctors = $stmt->fetchColumn();

    // Get number of patients by gender
    $stmt = $conn->prepare("SELECT COUNT(*) FROM patientsRecords WHERE gender = 'Male'");
    $stmt->execute();
    $male_patients = $stmt->fetchColumn();

    $stmt = $conn->prepare("SELECT COUNT(*) FROM patientsRecords WHERE gender = 'Female'");
    $stmt->execute();
    $female_patients = $stmt->fetchColumn();

    $stmt = $conn->prepare("SELECT COUNT(*) FROM patientsRecords WHERE gender = 'Other'");
    $stmt->execute();
    $other_patients = $stmt->fetchColumn();

    // Get number of patients by status
    $stmt = $conn->prepare("SELECT COUNT(*) FROM completedAndCancelledApps WHERE status = 'completed'");
    $stmt->execute();
    $completed_patients = $stmt->fetchColumn();

    $stmt = $conn->prepare("SELECT COUNT(*) FROM completedAndCancelledApps WHERE status = 'cancelled'");
    $stmt->execute();
    $cancelled_patients = $stmt->fetchColumn();

    $stmt = $conn->prepare("SELECT COUNT(*) FROM appointments WHERE status = 'in progress'");
    $stmt->execute();
    $in_progress_patients = $stmt->fetchColumn();

    $stmt = $conn->prepare("SELECT COUNT(*) FROM appointments WHERE status = 'waiting'");
    $stmt->execute();
    $waiting_patients = $stmt->fetchColumn();

    // Get number of doctors by status
    $stmt = $conn->prepare("SELECT COUNT(*) FROM doctorstatus WHERE status = 'active'");
    $stmt->execute();
    $active_doctors = $stmt->fetchColumn();

    $stmt = $conn->prepare("SELECT COUNT(*) FROM doctorstatus WHERE status = 'inactive'");
    $stmt->execute();
    $inactive_doctors = $stmt->fetchColumn();

    // Reset any previous database connection error message
    $db_conn_err = "";

    echo '<div class="title-head"><p class="title">Dashboard</p> <p class="username">User: ' . $user . '</p></div>';
    echo '<div class="container">';
    echo '    <section class="info">';
    echo '        <p class="form-title"><i class="fa fa-dashboard" aria-hidden="true"></i> Dashboard</p>';
    echo '        <hr>';
    echo '        <p class="db-suc">' . $db_conn_suc . '</p>';
    echo '    </section>';

    echo '  <div class="stats-info">';
    echo '       <div class="stats" onclick="location.href=\'view-patients.php\'">';
    echo '           <h2>Number of Registered Patients</h2>';
    echo '            <p><i class="fa fa-users" aria-hidden="true"></i> ' . $total_patients . '</p>';
    echo '       </div>';

    echo '       <div class="stats" onclick="location.href=\'view-doctors.php\'">';
    echo '           <h2>Number of Active Doctors</h2>';
    echo '           <p style="color: green;"><i class="fa fa-stethoscope" aria-hidden="true"></i> ' . $active_doctors . '</p>';
    echo '       </div>';

    echo '       <div class="stats" onclick="location.href=\'view-appointments.php\'">';
    echo '           <h2>Patients in Waiting Bay</h2>';
    echo '           <p style="color: grey;"><i class="fa fa-spinner fa-pulse" aria-hidden="true"></i> ' . $waiting_patients . '</p>';
    echo '       </div>';

    echo '       <div class="stats" onclick="location.href=\'view-appointments.php\'">';
    echo '           <h2>Number of In Progress Patients</h2>';
    echo '           <p style="color: orange;"><i class="fa fa-heart" aria-hidden="true"></i> ' . $in_progress_patients . '</p>';
    echo '       </div>';
    echo '  </div>';


    echo '<div class="stats-chart" style="width: 520px; height: 300px;">
            <div id="genderpiechart" style="width: 100%; height: 100%;"></div>
        </div>

        <div class="stats-chart" style="width: 520px; height: 300px;">
            <div id="patientstatuspiechart" style="width: 100%; height: 100%;"></div>
        </div>
        
        <div class="stats-chart" style="width: 520px; height: 300px;">
            <div id="doctorsstatuspiechart" style="width: 100%; height: 100%;"></div>
        </div>
        ';

} catch (PDOException $e) {
    // Capture and store the error message
    echo '<div class="title-head"><p>Dashboard</p> <p class="username">User: ' . $user . '</p></div>';
    echo '<div class="container">';
    echo '    <section class="info">';
    echo '        <p class="form-title"><i class="fa fa-dashboard" aria-hidden="true"></i> Dashboard</p>';
    echo '        <hr>';

    echo '        <p class="db-suc">' . $db_conn_err . '</p>';
    echo '    </section>';
}

?>



<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {'packages': ['corechart']});
    google.charts.setOnLoadCallback(drawGenderChart);
    google.charts.setOnLoadCallback(drawPatientStatusChart);
    google.charts.setOnLoadCallback(drawDoctorStatusChart);

    function drawGenderChart() {
        var data = google.visualization.arrayToDataTable([
            ['Label', 'Value'],
            ['Male: <?php echo $male_patients; ?>', <?php echo $male_patients; ?>],
            ['Female: <?php echo $female_patients; ?>', <?php echo $female_patients; ?>],
            ['Other: <?php echo $other_patients; ?>', <?php echo $other_patients; ?>],
        ]);

        var options = {
            title: 'Chart of Patients by Gender',
            is3D: true,
            colors: ['blue', 'purple', 'orange'],
            backgroundColor: '#edf2f4'
        };

        var chart = new google.visualization.PieChart(document.getElementById('genderpiechart'));
        chart.draw(data, options);
    }

    function drawPatientStatusChart() {
        var data = google.visualization.arrayToDataTable([
            ['Label', 'Value'],
            ['Waiting: <?php echo $waiting_patients; ?>', <?php echo $waiting_patients; ?>],
            ['Cancelled: <?php echo $cancelled_patients; ?>', <?php echo $cancelled_patients; ?>],
            ['In Progress: <?php echo $in_progress_patients; ?>', <?php echo $in_progress_patients; ?>],
            ['Completed: <?php echo $completed_patients; ?>', <?php echo $completed_patients; ?>],
        ]);

        var options = {
            title: 'Chart of Patients by Appointment Status',
            is3D: true,
            colors: ['grey', 'red', 'orange', 'green'],
            backgroundColor: '#edf2f4'
            
        };

        var chart = new google.visualization.PieChart(document.getElementById('patientstatuspiechart'));
        chart.draw(data, options);
    }

    function drawDoctorStatusChart() {
        var data = google.visualization.arrayToDataTable([
            ['Label', 'Value'],
            ['Active: <?php echo $active_doctors; ?>', <?php echo $active_doctors; ?>],
            ['Inactive: <?php echo $inactive_doctors; ?>', <?php echo $inactive_doctors; ?>],

        ]);

        var options = {
            title: 'Chart of Doctors by Status',
            is3D: true,
            colors: ['green', 'red'],
            backgroundColor: '#edf2f4'
            
        };

        var chart = new google.visualization.PieChart(document.getElementById('doctorsstatuspiechart'));
        chart.draw(data, options);
    }
</script>

</body>
</html>
