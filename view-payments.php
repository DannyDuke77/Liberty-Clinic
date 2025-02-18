<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <link rel="icon" href="http://localhost/Hospital%20Management/icon.jpg" type="image/jpeg">
    <title>LHCW - View Payments</title>
    <style>
        table{
            width: 97%;
            border-collapse: collapse;
            margin: 15px auto;
            font-size: 15px;
            margin-bottom: 20px !important;
        }
        .container{
            padding-top: 20px;
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
        tr{
            text-align: center;
        }
        tr:nth-child(even){
            background-color: #e9e9e9;
        }
        tr:hover{
            background-color: lightgreen;
        }
        .edit-link{
            text-decoration: none;
            color: #3a506b;
            font-weight: 500;
            border: 1px solid #adb5bd;
            padding: 5px 10px;
            border-radius: 5px;
            background-color: #f5f5f5;
            margin-left: 10px;
        }
        .edit-link:hover{
            background-color: #adb5bd;
            color: white;
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
        @media screen and (max-width: 600px){
            body{
                width: 795px !important;
            }
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

    // Include navigation
    require 'nav.php';

    // Include database configuration
    require_once 'db_config.php';

    
    try{
        // Create connection
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // Set the PDO error mode to exception

        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Optionally, set character set to utf8 (if needed)
        $conn->exec("SET NAMES utf8");


        $sql = "SELECT * FROM paymentsview";

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        // Fetch all rows as associative array
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($rows) > 0) {
             // If rows are fetched, display the table
             echo "<div class='title-head'><p>Payments</p> <p class=\"username\">User: " . $user . "</p></div>";
             echo "<div class='container'>";
             echo "<section class='info'>";
             echo "<p class='form-title'><i class='fa fa-users' aria-hidden='true'></i> Payments List</p>";
             echo "<hr>";
             echo "<p class='db-suc'>$db_conn_suc</p>";

             echo "<table id='PaymentsTable'>";
             echo "<thead>";
             echo "<tr>";
             echo "<th>Payment ID</th>";
             echo "<th>Patient ID</th>";
             echo "<th>Patient Name</th>";
             echo "<th>Department</th>";
             echo "<th>Method of Payment</th>";
             echo "<th>Payment Status</th>";
             echo "<th>Action</th>";
             echo "</tr>";
             echo "</thead>";

             echo "<tbody>";

             foreach ($rows as $row) {
                 echo "<tr>";
                 echo "<td>" . $row['paymentId'] . "</td>";
                 echo "<td>" . $row['patientId'] . "</td>";
                 echo "<td>" . $row['name'] . "</td>";
                 echo "<td>" . $row['deptName'] . "</td>";
                 echo "<td>" . $row['methodName'] . "</td>";
                 echo "<td>";
                 // Display the status
                 if ($row['paymentStatus'] == 'pending') {
                     echo "<span style='background-color: #cccccc; padding: 4px;'>" . $row['paymentStatus'] . "</span>";
                 } elseif ($row['paymentStatus'] == 'completed') {
                     echo "<span style='background-color: #7fff7f ; padding: 4px;'>" . $row['paymentStatus'] . "</span>";
                 }
                 echo "</td>";

                 echo "<td>";
                 echo "<a class='edit-link' href='update-payment.php?patId=" . $row['patientId'] . "'>Update</a>";
                 echo "</td>";

                echo "</tr>";
             }

             echo "</tbody>";
             echo "</table>";
             
        } else {
            // If no rows are fetched, display an appropriate message
            echo "<div class='title-head'><p>Payments</p> <p class=\"username\">User: " . $user . "</p></div>";
            echo "<div class='container'>";
            echo "<section class='info'>";
            echo "<p class='form-title'><i class='fa fa-users' aria-hidden='true'></i> Payments List</p>";
            echo "<hr>";
            echo "<p>No records found.</p>";
            echo "</section>";
            echo "</div>";
        }
    } catch(PDOException $e) {
        echo $e->getMessage();
    }


?>
</body>
</html>