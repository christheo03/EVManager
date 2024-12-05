<?php


// Manually specify database credentials
$sqlDBname = "vmarou01"; // Replace with your database name
$sqlUser = "vmarou01";       // Replace with your username
$sqlPass = "jzmjbYEg";       // Replace with your password

// Validate inputs
if (empty($sqlDBname)) {
    die("Database name is empty!<br/>");
}
if (empty($sqlUser)) {
    die("Username is empty!<br/>");
}
if (empty($sqlPass)) {
    die("Password is empty!<br/>");
}

// Set session variables for database connection
$_SESSION["serverName"] = "mssql.cs.ucy.ac.cy"; // Database server
$_SESSION["connectionOptions"] = array(
    "Database" => $sqlDBname,
    "Uid" => $sqlUser,
    "PWD" => $sqlPass
);

// Test the connection
$serverName = $_SESSION["serverName"];
$connectionOptions = $_SESSION["connectionOptions"];

$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn === false) {
    die(print_r(sqlsrv_errors(), true)); // Display connection error details
}



// Close the connection when done

?>