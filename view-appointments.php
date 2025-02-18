<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LHWC - View Appointments</title>
    <link rel="stylesheet" href="main.css">
    <link rel="icon" href="http://localhost/Hospital%20Management/icon.jpg" type="image/jpeg">
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

        tr:nth-child(even){
            background-color: #e9e9e9;
        }
        tr:hover{
            background-color: lightgreen;
        }
        .page-link{
            border: 1px solid #adb5bd;
            padding: 5px 10px;
            color: #3a506b;
            text-decoration: none;
            cursor: pointer;
            margin: 0 3px;
            font-size: 14px;
            font-weight: 500;
            border-radius: 3px;
        }
        .page-link:hover{
            background-color: #adb5bd;
        }
        
        .alert-link a:hover{
            color: darkred;
        }
        #rows{
            width: 100px;
            display: inline;
        }
        #departmentFilter, #statusFilter{
            width: 170px;
        }
        #rows, #departmentFilter, #statusFilter{
            border: 1px solid #adb5bd;
            padding: 5px 10px;
            display: inline;
            font-size: 15px;
            font-weight: 500;
            border-radius: 3px;
            color: #3a506b;
        }

        label{
            font-weight: 500;
            color: #3a506b;
            margin-left: 15px;
        }
        #rowCount{
            font-size: 15px;
            font-weight: 600;
            margin-left: 15px;
            color: #36454f;
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
            table{
                font-size: 12px;
                margin: 0 5px;
            }
            .page-link{
                width: 85%;
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

    try {
        // Create a new PDO instance
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

        // Set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Optionally, set character set to utf8 (if needed)
        $conn->exec("SET NAMES utf8");

        // Number of rows to display per page
        $rowsPerPage = isset($_GET['rows']) ? (int)$_GET['rows'] : 999999;

        // Current page number
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        // Additional filters
        $departmentFilter = isset($_GET['department']) ? $_GET['department'] : '';
        $statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

        // Count total number of rows
        $stmt_count = $conn->prepare("SELECT COUNT(*) FROM appointmentsview");
        $stmt_count->execute();
        $totalRows = $stmt_count->fetchColumn();

        // Calculate total number of pages
        $totalPages = ceil($totalRows / $rowsPerPage);

        // Calculate offset
        $offset = ($currentPage - 1) * $rowsPerPage;

        // Prepare and execute the SQL query with pagination and additional filters
        $query = "SELECT * FROM appointmentsview WHERE 1=1";
        if (!empty($departmentFilter)) {
            $query .= " AND deptName = :departmentFilter";
        }
        if (!empty($statusFilter)) {
            $query .= " AND status = :statusFilter";
        }
        $query .= " ORDER BY checkInId LIMIT :offset, :rowsPerPage";

        $stmt = $conn->prepare($query);
        if (!empty($departmentFilter)) {
            $stmt->bindParam(':departmentFilter', $departmentFilter, PDO::PARAM_STR);
        }
        if (!empty($statusFilter)) {
            $stmt->bindParam(':statusFilter', $statusFilter, PDO::PARAM_STR);
        }
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':rowsPerPage', $rowsPerPage, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch all rows as associative array
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Check if there are any rows fetched
        if (count($rows) > 0) {
            // If rows are fetched, display the table
            echo "<div class='title-head'><p>Appointments</p> <p class=\"username\">User: " . $user . "</p></div>";
            echo "<div class='container'>";
            echo "<section class='info'>";
            echo "<p class='form-title'><i class='fa fa-users' aria-hidden='true'></i> Appointments List</p>";
            echo "<hr>";
            echo "<p class='db-suc'>$db_conn_suc</p>";
            echo "<p class='alert-link' style='margin-bottom: 10px; margin-left: 10px; color: #3a506b; font-weight: 700'><i class=\"fa fa-info-circle\" aria-hidden=\"true\"></i> Go to <a href='search-appointment.php'>search appointment</a> to search an appointment.</p>";
           

            // Select dropdown for rows per page
            echo "<label for='rows'>Rows Per Page:</label> ";
            echo "<select id='rows' onchange='changeRowsPerPage()'>";
            echo "<option value='999999' " . ($rowsPerPage == 999999 ? "selected" : "") . ">All</option>";
            echo "<option value='10' " . ($rowsPerPage == 10 ? "selected" : "") . ">10</option>";
            echo "<option value='50' " . ($rowsPerPage == 50 ? "selected" : "") . ">50</option>";
            echo "<option value='100' " . ($rowsPerPage == 100 ? "selected" : "") . ">100</option>";
            echo "<option value='500' " . ($rowsPerPage == 1000 ? "selected" : "") . ">500</option>";
            echo "<option value='1000' " . ($rowsPerPage == 1000 ? "selected" : "") . ">1000</option>";
            echo "</select>";

            // Select dropdown for filtering by department
            echo "<label for='departmentFilter'>Filter by Department:</label> ";
            echo "<select id='departmentFilter' name='departmentFilter'>";
            echo "<option value=''>All Departments</option>";
            echo "<option value='Radiology'>1. Radiology</option>";
            echo "<option value='Cardiology'>2. Cardiology</option>";
            echo "<option value='Dentistry'>3. Dentistry</option>";
            echo "<option value='Oncology'>4. Oncology</option>";
            echo "<option value='Immunology'>5. Immunology</option>";
            echo "<option value='Gastroenterology'>6. Gastroenterology</option>";
            echo "<option value='Hematology'>7. Hematology</option>";
            echo "<option value='Nephrology'>8. Nephrology</option>";
            echo "<option value='Urology'>9. Urology</option>";
            echo "<option value='Orthopedics'>10. Orthopedics</option>";
            echo "<option value='Gynaecology'>11. Gynaecology</option>";
            echo "<option value='ENT'>12. ENT</option>";
            echo "<option value='Dermatology'>13. Dermatology</option>";
            echo "<option value='Optics'>14. Optics</option>";
            echo "<option value='Neurology'>15. Neurology</option>";
            echo "<option value='Pedeatrics'>16. Pedeatrics</option>";
            echo "<option value='Surgery'>17. Surgery</option>";
            echo "<option value='Physiatrics'>18. Physiactrics</option>";
            echo "</select>";

            // Status filter dropdown
            echo "<label for='statusFilter'>Filter by Status:</label> ";
            echo "<select id='statusFilter' name='statusFilter'>";
            echo "<option value=''>All</option>";
            echo "<option value='waiting'>Waiting</option>";
            echo "<option value='in progress'>In Progress</option>";
            echo "</select>";

            // Paragraph to display number of patients
            echo "<p id='rowCount'></p>";

            // Display the table
            echo "<table id='appointmentsTable'>";
            echo "<thead>";
            echo "<tr>
                    <th style='width: 25px;'></th>
                    <th style='width: 70px;'>Appointment ID</th>
                    <th style='width: 70px;'>Patient ID</th>
                    <th style='width: 150px;'>Patient Name</th>
                    <th style='width: 80px;'>Department Name</th>
                    <th style='width: 200px;'>Check in Time</th>
                    <th style='width: 100px;'>Status</th>
                    <th style='width: 100px;'>Action</th>
                </tr>";
            echo "</thead>";
            
            echo "<tbody>";

            foreach ($rows as $row) {
                echo "<tr class='dept-" . strtolower($row['deptName']) . " status-" . strtolower($row['status']) . "'>";
                echo "<td> </td>";
                echo "<td>" . $row['checkInID'] . "</td>";
                echo "<td>" . $row['patientId'] . "</td>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td>" . $row['deptName'] . "</td>";
                echo "<td>" . $row['checkInTime'] . "</td>";
                echo "<td>"; // Start a new column for the status
                // Display the status
                if ($row['status'] == 'waiting') {
                    echo "<span style='background-color: #cccccc; padding: 4px;'>" . $row['status'] . "</span>";
                } elseif ($row['status'] == 'completed') {
                    echo "<span style='background-color: #7fff7f ; padding: 4px;'>" . $row['status'] . "</span>";
                } elseif ($row['status'] == 'cancelled') {
                    echo "<span style='background-color: #ff9999; padding: 4px;'>" . $row['status'] . "</span>";
                } elseif ($row['status'] == 'in progress') {
                    echo "<span style='background-color:  #ffb366; padding: 4px;'>" . $row['status'] . "</span>";
                } else {
                    echo $row['status']; // If neither active nor inactive, display the status as is
                } echo "</td>"; // End the column

                echo "<td>";
                if (isset($row['deptId'])) {
                    echo "<a href='update-appointment.php?id=" . $row['checkInID'] . "&dept=" . $row['deptId'] . "'>Edit</a>";
                } else {
                    echo "<a class='edit-link' href='update-appointment.php?id=" . $row['checkInID'] . "'>Edit</a>";
                }
                echo "</td>";


                  
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";

            // Display pagination links
            echo "<div class='pagination'>";
            if ($currentPage > 1) {
                echo "<a class='page-link' href='view-appointments.php?page=" . ($currentPage - 1) . "&rows=$rowsPerPage'>Previous</a>";
            }

            for ($i = 1; $i <= $totalPages; $i++) {
                echo "<a class='page-link' href='view-appointments.php?page=$i&rows=$rowsPerPage'>$i</a>";
            }

            if ($currentPage < $totalPages) {
                echo "<a class='page-link' href='view-appointments.php?page=" . ($currentPage + 1) . "&rows=$rowsPerPage'>Next</a>";
            }
            echo "</div>";
            echo "</section>";
            echo "</div>";
        } else {
            // If no rows are fetched, display an appropriate message
            echo "<div class='title-head'><p>Appointments</p> <p class=\"username\">User: ". $user . "</p></div>";
            echo "<div class='container'>";
            echo "<section class='info'>";
            echo "<p class='form-title'><i class='fa fa-users' aria-hidden='true'></i> Appointments List</p>";
            echo "<hr>";
            echo "<p>No records found.</p>";
            echo "</section>";
            echo "</div>";
        }
    } catch(PDOException $e) {
        // Display only the database connection error message
        echo "<div class='title-head'><p>Appointments</p> <p class=\"username\">User: ". $user . "</p></div>";
        echo "<div class='container'>";
        echo "<section class='info'>";
        echo "<p class='form-title'><i class='fa fa-users' aria-hidden='true'></i> Appointments List</p>";
        echo "<hr>";
        echo "<p class='db-err'>$db_conn_err</p>";
        echo "</section>";
        echo "</div>";
        // If database connection fails, use the error message defined in db_config.php
        $db_conn_err = "<p style='background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px; padding: 10px; margin: 20px 0; font-family: Arial, sans-serif;'>
            <i class=\"fa fa-exclamation-circle\" aria-hidden=\"true\"></i>
            <strong>WARNING:</strong> Database connection failed. Please contact your administrator!
        </p>";
    }
?>

<script>
    // Function to handle changing department filter
    document.getElementById("departmentFilter").addEventListener("change", applyFilters);
    document.getElementById("statusFilter").addEventListener("change", applyFilters);

    function applyFilters() {
        var selectedDept = document.getElementById("departmentFilter").value;
        var selectedStatus = document.getElementById("statusFilter").value;

        var rows = document.querySelectorAll("#appointmentsTable tbody tr");

        rows.forEach(function(row) {
            var deptName = row.cells[4].textContent;
            var status = row.cells[6].textContent;

            if ((selectedDept === "" || deptName === selectedDept) &&
                (selectedStatus === "" || status === selectedStatus)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });

        // Update the row count display after applying filters
        updateRowCount();
    }

    // Function to update the row count display
    function updateRowCount() {
    var visibleRows = document.querySelectorAll("#appointmentsTable tbody tr:not([style*='display: none'])");
    var totalRows = visibleRows.length;

    document.getElementById('rowCount').innerText = 'Displaying ' + totalRows + ' appointments';
}

    // Function to handle changing rows per page
    function changeRowsPerPage() {
        var selectedRows = document.getElementById("rows").value;
        window.location.href = "view-appointments.php?rows=" + selectedRows;
    }

    // Call updateRowCount function when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        applyFilters(); // Apply filters if any on page load
    });

    function redirectToUpdate(appointmentId) {
        window.location.href = 'update-appointment.php?id=' + appointmentId;
    }

</script>
</body>
</html>
