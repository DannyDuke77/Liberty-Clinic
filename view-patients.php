<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LHWC - View Patients</title>
    <link rel="stylesheet" href="main.css">
    <link rel="icon" href="http://localhost/Hospital%20Management/icon.jpg" type="image/jpeg">
    <style>
        table{
            width: 97%;
            border-collapse: collapse;
            border: 1px solid black;
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
            color: white;
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
            border: 1px solid #adb5bd;
            padding: 5px 10px;
            width: 100px;
            display: inline;
        }
        #rows{
            width: 120px;
        }
        #genderFilter{
            width: 170px;
        }
        #rows, #genderFilter{
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
        .update-btn{
            color: #3a506b;
            font-weight: 500;
            border: 1px solid #adb5bd;
            padding: 5px 10px;
            border-radius: 5px;
            background-color: #f5f5f5;
            margin-left: 5px;
            cursor: pointer;
        } 
        .update-btn:hover{
            background-color: lightblue;
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
    $rowsPerPage = isset($_GET['rows']) ? (int)$_GET['rows'] : 999999;

    // Current page number
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

    // Gender filter
    $genderFilter = isset($_GET['gender']) ? $_GET['gender'] : '';

    try {
        // Create a new PDO instance
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

        // Set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Count total number of rows
        $stmt_count = $conn->prepare("SELECT COUNT(*) FROM patientsRecords");
        $stmt_count->execute();
        $totalRows = $stmt_count->fetchColumn();

        // Calculate total number of pages
        $totalPages = ceil($totalRows / $rowsPerPage);

        // Calculate offset
        $offset = ($currentPage - 1) * $rowsPerPage;

        // Prepare SQL query with pagination and optional gender filter
        $sql = "SELECT * FROM patientsRecords";
        $params = array();

        if (!empty($genderFilter)) {
            $sql .= " WHERE gender = :gender";
            $params['gender'] = $genderFilter;  
        }

        $sql .= " LIMIT :offset, :rowsPerPage";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':rowsPerPage', $rowsPerPage, PDO::PARAM_INT);

        if (!empty($genderFilter)) {
            $stmt->bindParam(':gender', $genderFilter, PDO::PARAM_STR);
        }

        $stmt->execute();

        

        // Fetch all rows as associative array
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Display the table
        echo "<div class='title-head'><p>Patients</p> <p class=\"username\">User: " . $user . "</p></div>";
        echo "<div class='container'>";
        echo "<section class='info'>";
        echo "<p class='form-title'><i class='fa fa-users' aria-hidden='true'></i> Patient List</p>";
        echo "<hr>";
        echo "<p class='db-suc'>$db_conn_suc</p>";


        // Rows per page dropdown
        echo "<label for='rows'>Rows Per Page:</label>";
        echo " <select id='rows' onchange='changeRowsPerPage()'>";
        echo "<option value='999999' " . ($rowsPerPage == 999999 ? "selected" : "") . ">All Pages</option>";
        echo "<option value='10' " . ($rowsPerPage == 10 ? "selected" : "") . ">10</option>";
        echo "<option value='50' " . ($rowsPerPage == 50 ? "selected" : "") . ">50</option>";
        echo "<option value='100' " . ($rowsPerPage == 100 ? "selected" : "") . ">100</option>";
        echo "<option value='500' " . ($rowsPerPage == 1000 ? "selected" : "") . ">500</option>";
        echo "<option value='1000' " . ($rowsPerPage == 1000 ? "selected" : "") . ">1000</option>";
        echo "</select>";

        // Gender filter dropdown
        echo "<label for='genderFilter'>Filter by Gender:</label> ";
        echo "<select id='genderFilter' onchange='applyFilters()'>";
        echo "<option value=''>All Genders</option>";
        echo "<option value='male' " . ($genderFilter == 'male' ? "selected" : "") . ">Male</option>";
        echo "<option value='female' " . ($genderFilter == 'female' ? "selected" : "") . ">Female</option>";
        echo "<option value='other' " . ($genderFilter == 'other' ? "selected" : "") . ">Other</option>";
        echo "</select>";

        // Paragraph to display number of patients
        echo "<p id='rowCount'></p>";

        // Table header
        echo "<table>";
        echo "<tr>
                <th style='width: 25px;'></th>
                <th style='width: 70px;'>Patient Id</th>
                <th style='width: 170px;'>Name</th>
                <th style='width: 80px;'>Age</th>
                <th style='width: 80px;'>Gender</th>
                <th style='width: 130px;'>Phone</th>
                <th style='width: 200px';>Email</th>
                <th style='width: 200px;'>Address</th>
                <th style='width: 100px;'>Action</th>
            </tr>";

        // Iterate over each row and display table rows
        foreach ($rows as $index => $row) {
            echo "<tr>";
            echo "<td>" . ($index + 1) . ".</td>";
            echo "<td>LHWC/PAT/" . $row['patientId'] . "</td>";
            echo "<td>" . $row['name'] . "</td>";
            echo "<td>" . $row['age'] . "</td>";
            echo "<td>" . $row['gender'] . "</td>";
            echo "<td>" . $row['phoneNumber'] . "</td>";
            echo "<td>" . $row['email'] . "</td>";
            echo "<td>" . $row['address'] . "</td>";
            echo "<td><button title='View " . $row['name'] . "' class='update-btn' onclick='redirectToSearch(\"" . $row['patientId'] . "\")'>Details</button></td>";
            echo "</tr>";
        }
        

        echo "</table>";

        // Pagination links
        echo "<div class='pagination'>";
        if ($currentPage > 1) {
            echo "<a class='page-link' href='view-patients.php?page=" . ($currentPage - 1) . "&rows=$rowsPerPage&gender=$genderFilter'>Previous</a>";
        }

        for ($i = 1; $i <= $totalPages; $i++) {
            echo "<a class='page-link' href='view-patients.php?page=$i&rows=$rowsPerPage&gender=$genderFilter'>$i</a>";
        }

        if ($currentPage < $totalPages) {
            echo "<a class='page-link' href='view-patients.php?page=" . ($currentPage + 1) . "&rows=$rowsPerPage&gender=$genderFilter'>Next</a>";
        }
        echo "</div>";

        echo "</section>";
        echo "</div>";

    } catch(PDOException $e) {
        // Display error message if database connection fails
        echo "<div class='title-head'><p>Patients</p> <p class=\"username\">User: " . $user . "</p></div>";
        echo "<div class='container'>";
        echo "<section class='info'>";
        echo "<p class='form-title'><i class='fa fa-users' aria-hidden='true'></i> Patient List</p>";
        echo "<hr>";
        echo "<p class='db-err'>" . $db_conn_err . "</p>";
        echo "</section>";
        echo "</div>";
    }
?>

<script>
    // Function to handle changing gender filter
    document.getElementById("genderFilter").addEventListener("change", applyFilters);

    function applyFilters() {
        var selectedGender = document.getElementById("genderFilter").value;
        window.location.href = "view-patients.php?rows=<?php echo $rowsPerPage; ?>&gender=" + selectedGender;
    }

    // Function to handle changing rows per page
    function changeRowsPerPage() {
        var selectedRows = document.getElementById("rows").value;
        window.location.href = "view-patients.php?rows=" + selectedRows + "&gender=<?php echo $genderFilter; ?>";
    }

    // Display number of rows
    document.addEventListener('DOMContentLoaded', function() {
        updateRowCount(<?php echo $totalRows; ?>, <?php echo count($rows); ?>);
    });

    function updateRowCount(totalRows, displayedRows) {
        document.getElementById('rowCount').innerText = 'Displaying ' + displayedRows + ' out of ' + totalRows + ' patients';
    }

    function redirectToSearch(patientId) {
        window.location.href = 'search-patient.php?patId=' + patientId;
    }

</script>

</body>
</html>







        
