<?php

require_once 'db_config.php';

// Initialize variables to prevent undefined errors
$user = "";
$userType = "";


if (isset($_SESSION['userId'])) {
    // Fetch the username
    $sql1 = "SELECT userName FROM usersPasswords WHERE userId = :userId";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bindParam(':userId', $_SESSION['userId']);
    $stmt1->execute();
    $result1 = $stmt1->fetch();

    if ($result1) {
        $user = $result1['userName'];
    } else {
        // Handle the case where no user is found
        echo "User not found.";
    }

    // Fetch the user type
    $sql2 = "SELECT userType FROM usersPasswords WHERE userId = :userId";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bindParam(':userId', $_SESSION['userId']);
    $stmt2->execute();
    $result2 = $stmt2->fetch();

    if ($result2) {
        $userType = $result2['userType'];
    } else {
        // Handle the case where no user type is found
        echo "User type not found.";
    }
}
?>
