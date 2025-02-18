<?php
// Database configuration
$servername = "localhost:3307";
$username = "root";
$password = "MySQLpass004";
$dbname = "LibertyClinic";

$db_conn_err = "";
$db_conn_suc = "";


try {
    // Create a new PDO instance
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Optionally, set character set to utf8 (if needed)
    $conn->exec("SET NAMES utf8");

    $db_conn_suc = " <p class=\"alert alert-success\" style='background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px; padding: 10px; margin: 20px 0; font-family: Arial, sans-serif;'>
        <i class=\"fa fa-check-circle\" aria-hidden=\"true\"></i>
        <strong>Success:</strong> Database connection established successfully.
        <i style=\"float: right;\ cursor: pointer; font-size: 20px; margin-right: 10px; \" title=\"Close \" onclick=\"this.parentElement.style.display='none';\" class=\"fa fa-times-circle\" aria-hidden=\"true\"></i>
    </p>";

    require_once 'session.php';

} catch(PDOException $e) {
    
    // If connection fails, display error message
    $db_conn_err = "<p class=\"alert alert-danger\" style='background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px; padding: 10px; margin: 20px 0; font-family: Arial, sans-serif;'>
        <i class=\"fa fa-exclamation-circle\" aria-hidden=\"true\"></i>
        <strong>WARNING:</strong> Connection to database failed. Please contact your administrator!
        <a onclick=\"location.reload();\" style=\"float: right; cursor: pointer; font-size: 17px; margin-right: 10px; \" class=\"fa fa-refresh \" aria-hidden=\"true\"> Retry Connection</a>
    </p>";

    $user = "N/A";
    $userType = "error";

    $conn = null;
}
?>

