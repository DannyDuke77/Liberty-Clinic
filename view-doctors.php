<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LHWC - View Doctors</title>
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
        #rows{
            width: 100px;
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

    // Number of rows to display per page
    $rowsPerPage = isset($_GET['rows']) ? (int)$_GET['rows'] : 10;

    // Current page number
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

    try {
        // Create a new PDO instance
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

        // Set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Optionally, set character set to utf8 (if needed)
        $conn->exec("SET NAMES utf8");

        // Count total number of rows
        $stmt_count = $conn->prepare("SELECT COUNT(*) FROM doctorInfo");
        $stmt_count->execute();
        $totalRows = $stmt_count->fetchColumn();

        // Calculate total number of pages
        $totalPages = ceil($totalRows / $rowsPerPage);

        // Calculate offset
        $offset = ($currentPage - 1) * $rowsPerPage;

        // Prepare and execute the SQL query with pagination
        $stmt = $conn->prepare("SELECT * FROM doctorInfo LIMIT :offset, :rowsPerPage");
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':rowsPerPage', $rowsPerPage, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch all rows as associative array
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


        // Check if there are any rows fetched
        if (count($rows) > 0) {
            // If rows are fetched, display the table
            echo "<div class='title-head'><p>Doctors</p> <p class=\"username\">User: " . $user . "</p></div>";
            echo "<div class='container'>";
            echo "<section class='info'>";
            echo "<p class='form-title'><i class='fa fa-users' aria-hidden='true'></i> Doctor List</p>";
            echo "<hr>";
            echo "<p class='db-suc'>$db_conn_suc</p>";


            // Select dropdown for rows per page
            echo "<label for='rows'>Rows Per Page:</label>";
            echo " <select id='rows' onchange='changeRowsPerPage()'>";
            echo "<option value='10' " . ($rowsPerPage == 10 ? "selected" : "") . ">10</option>";
            echo "<option value='50' " . ($rowsPerPage == 50 ? "selected" : "") . ">50</option>";
            echo "<option value='100' " . ($rowsPerPage == 100 ? "selected" : "") . ">100</option>";
            echo "<option value='500' " . ($rowsPerPage == 1000 ? "selected" : "") . ">500</option>";
            echo "<option value='1000' " . ($rowsPerPage == 1000 ? "selected" : "") . ">1000</option>";
            echo "</select>";
            

            // Dropdown for filtering by department
            echo "<label for='departmentFilter'>Filter by Department:</label> ";
            echo "<select id='departmentFilter' onchange='applyFilters()'> ";
            echo "<option value=''>All Departments</option>";
            echo "<option value='Cardiology'>Cardiology</option>";
            echo "<option value='Dermatology'>Dermatology</option>";
            echo "<option value='ENT'>ENT</option>";
            echo "<option value='Gastroenterology'>Gastroenterology</option>";
            echo "<option value='Gynaecology'>Gynaecology</option>";
            echo "<option value='Hematology'>Hematology</option>";
            echo "<option value='Immunology'>Immunology</option>";
            echo "<option value='Nephrology'>Nephrology</option>";
            echo "<option value='Orthopedics'>Orthopedics</option>";
            echo "<option value='Oncology'>Oncology</option>";
            echo "<option value='Radiology'>Radiology</option>";
            echo "<option value='Urology'>Urology</option>";
            echo "<option value='Surgery'>Surgery</option>";
            echo "<option value='Pedeatrics'>Pedeatrics</option>";
            echo "</select>";

            // Dropdown for filtering by status
            echo "<label for='statusFilter'>Filter by Status:</label> ";
            echo "<select id='statusFilter' onchange='applyFilters()'>";
            echo "<option value=''>All Statuses</option>";
            echo "<option value='active'>Active</option>";
            echo "<option value='inactive'>Inactive</option>";
            echo "</select>";

            // Paragraph to display number of patients
            echo "<p id='rowCount'></p>";

            echo "<table id='doctorTable'>";
            echo "<thead>
                    <th style='width: 25px;'>No.</th>
                    <th style='width: 70px;'>Doctor Id</th>
                    <th style='width: 150px;'>Name</th>
                    <th style='width: 80px;'>Phone</th>
                    <th style='width: 100px;'>Email</th>
                    <th style='width: 30px;'>Department</th>
                    <th style='width: 100px;'>Status</th>
                </thead>";


            // Iterate over each row and echo out the table rows
            foreach ($rows as $index => $row) {
                echo "<tr>";
                echo "<td>" . ($index + 1) . ".</td>";
                echo "<td>" . $row['doctorId'] . "</td>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td>" . $row['phoneNumber'] . "</td>";
                echo "<td>" . $row['email'] . "</td>";
                echo "<td>" . $row['deptName'] . "</td>";
                echo "<td>";
                // Wrap the status in a span with background color
                if ($row['status'] == 'active') {
                    echo "<span style='background-color: #99ff99; padding: 4px;'>" . $row['status'] . "</span>";
                } elseif ($row['status'] == 'inactive') {
                    echo "<span style='background-color: #ff9999; padding: 4px;'>" . $row['status'] . "</span>";
                } else {
                    echo $row['status']; // If neither active nor inactive, display the status as is
                }
                echo "</td>";
                echo "</tr>";
            }

            echo "</table>";

            // Display pagination links
            echo "<div class='pagination'>";
            if ($currentPage > 1) {
                echo "<a class='page-link' href='view-doctors.php?page=" . ($currentPage - 1) . "&rows=$rowsPerPage'>Previous</a>";
            }

            for ($i = 1; $i <= $totalPages; $i++) {
                echo "<a class='page-link' href='view-doctors.php?page=$i&rows=$rowsPerPage'>$i</a>";
            }

            if ($currentPage < $totalPages) {
                echo "<a class='page-link' href='view-doctors.php?page=" . ($currentPage + 1) . "&rows=$rowsPerPage'>Next</a>";
            }
            echo "</div>";

            echo "</section>";
            echo "</div>";
        } else {
            // If no rows are fetched, display an appropriate message
            $db_fetch_err = "No records found.";
        }
    } catch(PDOException $e) {
        // Display only the database connection error message
        echo "<div class='title-head'><p>Doctors</p> <p class=\"username\">User: " . $user . "</p></div>";
        echo "<div class='container'>";
        echo "<section class='info'>";
        echo "<p class='form-title'><i class='fa fa-users' aria-hidden='true'></i> Doctor List</p>";
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

        var rows = document.querySelectorAll("#doctorTable tr");

        rows.forEach(function(row, index) {
            // Skip table header
            if (index !== 0) {
                var deptName = row.cells[5].textContent;
                var status = row.cells[6].textContent;

                if ((selectedDept === "" || deptName === selectedDept) &&
                    (selectedStatus === "" || status === selectedStatus)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            }
        });

        // Update the row count display after applying filters
        updateRowCount();
    }

    // Function to update row count display
    function updateRowCount() {
        var visibleRows = document.querySelectorAll("#doctorTable tr:not([style='display: none;'])").length - 1; // Exclude table header
        var totalRows = <?php echo $totalRows; ?>;
        document.getElementById('rowCount').innerText = 'Displaying ' + visibleRows + ' out of ' + totalRows + ' doctors';
    }

    // Call the applyFilters function when the DOM content is fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        applyFilters();
    });

    // Function to handle changing rows per page
    function changeRowsPerPage() {
        var selectedRows = document.getElementById("rows").value;
        window.location.href = "view-doctors.php?rows=" + selectedRows;
    }
</script>



</body>
</html>
