<?php
require_once 'db_config.php';

echo '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" href="http://localhost/Hospital%20Management/icon.jpg" type="image/jpeg">

    <style>
        *{
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            letter-spacing: 1px;
        }
    
        body{
            width: 90%;
            margin: 10px auto;
            background-color: #0022ff27;
        }
        nav{
            background: linear-gradient(90deg, rgba(9,9,121,1) 15%, #0077b6 100%);
            padding-top: 0.35cm;
            height: fit-content;
            border-radius: 1px 1px 0px 0px;
            padding-left: 1%;
        }
        .dropdown {
            position: relative;
            display: inline;
            width: 100%;
        }
        .dropbtn{
            height: 1cm;
            width: fit-content;
            margin-bottom: 10px; 
            margin-left: 7px;
            text-align: left;
            background-color: transparent;
            border: 1px solid rgb(99, 99, 207);
            border-radius: 5px;
            padding-left: 10px;
            padding-right: 10px;
            color: #e9e9e9;
            overflow: hidden;
            
            font-size: 12px;
        }
        header{
            color : #012a4a;
            padding-left: 35px;
            margin-left: -1%;
            height: 90px;
            padding-top: 10px;
            margin-bottom: 1%;
            margin-top: -13px;
            background-color: #edf2f4;
            border-radius: 1px 1px 0px 0px;
            cursor: default;
        }
        .logo{
            font-size: 50px;
            display: inline;
        }
        h1{
            display: inline;
            font-size: 30px;
            position: relative;
            top: -17px;
        }
        header p{
            display: block;
            font-size: 15px;
            margin-left: 60px;
            position: relative;
            top: -37px;
            color: rgb(0, 0, 0); 
            font-style: italic;   
            font-weight: 300;
            letter-spacing: 1px;
        }
        .dropdown-content{
            display: none;
            position: absolute;
            top: 170%;
            left: 8px;
            background-color: whitesmoke;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            width: 190px;
            font-size: 13px;
        }
        .fa-chevron-down{
            float: right;
            font-size: 8px;
            margin-top: 3px;
            margin-left: 10px;
        }
        .dropdown-content a{
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        .dropdown-content a:hover{
            background-color: #cfceec;
            color: rgb(0, 4, 255);
        }
        .dropdown:hover .dropbtn{
            background-color: rgba(0, 0, 0, 0.2);
            filter: brightness(170%);
            color: white;
        }
        @media screen and (max-width: 600px){
            header{
                padding-left: 5px;
            }
            logo{
                font-size: 30px;
            }
            h1{
                font-size: 20px;
                top: -20px;   
                letter-spacing: 0px;
            }
            header p{
                font-size: 10px;
                letter-spacing: 0px;
                font-weight: 400;
            }
            .dropdown-content{
                font-size: 12px;
                width: 150px;
                left: -5px;
                top: 190%;
                min-width: 100px;
                padding-left: 5px;
            }

        }
    </style>
</head>
<body>
    <nav>
        <header title="Liberty Health and Wellness Clinic">
            <div class="logo">
                <i class="fa fa-hospital-o" aria-hidden="true"></i> 
            </div>
            <h1>Liberty Health and Wellness Clinic</h1>
            <p>Empowering Health, Inspiring Hope: Your Wellness is Our Top Priority</p>
        </header>

        <!-- Dashboard Dropdown menu -->

        <div class="dropdown">
            <button class= "dropbtn"><i class="fa fa-home" aria-hidden="true"></i> Dashboard<span class="fa fa-chevron-down"></span></button>
            <div id="dashboard-dropdown" class="dropdown-content">
                <a href="dashboard.php">Dashboard</a>
            </div>
        </div>
            
        
        <!-- Patients Dropdown menu -->

        <div class="dropdown">
            <button class="dropbtn"><i class="fa fa-wheelchair" aria-hidden="true"></i> Patients<span class="fa fa-chevron-down"></span></button>
            <div id="patients-dropdown" class="dropdown-content">
                <a href="add-patient.php">Add Patient</a>
                <a href="view-patients.php">View All Patients</a>
                <a href="search-patient.php">Search Patient</a>
                <a href="#">Edit Patient</a>
            </div>
        </div>

        <!--Doctors Dropdown menu-->

        <div class="dropdown">
            <button class="dropbtn"><i class="fa fa-user-md" aria-hidden="true"></i> Doctors <span class="fa fa-chevron-down"></span></button>
            <div id="doctors-dropdown" class="dropdown-content">
                <a href="add-doctor.php">Add Doctor</a>
                <a href="view-doctors.php">View All Doctors</a>
                <a href="doctor-status.php">Update Doctor Status</a>
                <a href="#">Search Doctor</a>
                <a href="#">Edit Doctor</a>
            </div>
        </div>

        <!--Appointments Dropdown menu-->

        <div class="dropdown">
            <button class="dropbtn"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Appointments <span class="fa fa-chevron-down"></span></button>
            <div id="appointments-dropdown" class="dropdown-content">
                <a href="add-appointment.php">Check-In</a>
                <a href="update-appointment.php">Update Status</a>
                <a href="view-appointments.php">View All Appointments</a>
                <a href="#">Search Appointment</a>
                <a href="#">Edit Appointment</a>
            </div>
        </div>

        <!--Payments Dropdown menu-->

        <div class="dropdown">
            <button class="dropbtn"><i class="fa fa-usd" aria-hidden="true"></i> Payments <span class="fa fa-chevron-down"></span></button>
            <div id="payments-dropdown" class="dropdown-content">
                <a href="view-payments.php">View All Payments</a>
            </div>
        </div>        

        <!--Other Employees-->
        
        <div class="dropdown">
            <button class="dropbtn" title="Exclusive of all doctors!"><i class="fa fa-users" aria-hidden="true"></i> Manage<span class="fa fa-chevron-down"></span></button>
            <div id="doctors-dropdown" class="dropdown-content">
                <a href="#">Add Employee</a>
                <a href="#">View All Employees</a>
                <a href="#">Search Employee</a>
                <a href="#">Edit Employee</a>
            </div>
        </div>

        <!--Logout Dropdown menu-->

        <div class="dropdown">
            <button class="dropbtn"><i class="fa fa-cogs" aria-hidden="true"></i> More<span class="fa fa-chevron-down"></span></button>
            <div id="logout-dropdown" class="dropdown-content">
                    <a href="register.php">Add User (Admins only)</a>
                    <a href="logout.php">Logout <i class="fa fa-sign-out" aria-hidden="true"></i></a>
            </div>
        </div>
    </nav>
'?>
    <script>
        // Get all dropdown buttons
        var dropdowns = document.getElementsByClassName("dropbtn");
        var dropbtn = document.getElementsByClassName("dropbtn");
        var i;
      
        // Loop through the dropdown buttons and add a click event listener to each
        for (i = 0; i < dropdowns.length; i++) {
          dropdowns[i].addEventListener("click", function() {
            // Toggle the display of the dropdown content when the button is clicked
            var dropdownContent = this.nextElementSibling;
            if (dropdownContent.style.display === "block") {
              dropdownContent.style.display = "none";
            } else {
              dropdownContent.style.display = "block";
            }
          });
        }
      
        // Close the dropdown menu if the user clicks outside of it
        window.onclick = function(event) {
          if (!event.target.matches('.dropbtn')) {
            var dropdownContent = document.getElementsByClassName("dropdown-content");
            var j;
            for (j = 0; j < dropdownContent.length; j++) {
              var openDropdown = dropdownContent[j];
              if (openDropdown.style.display === "block") {
                openDropdown.style.display = "none";
              }
            }
          }
        }

        // Sorting dropdown menu in alphabetical order
        window.onload = function() {
            var select = document.getElementById('dept');
            var options = Array.from(select.options);
    
            options.sort(function(a, b) {
                return a.text.localeCompare(b.text);
            });
    
            select.innerHTML = '';
            options.forEach(function(option) {
                select.appendChild(option);
            });
        };

      </script>

</body>
</html>