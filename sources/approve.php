<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['username'])) {
    echo "You are not logged in";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $app_id = $_POST['app_id'];

    try {
        $sql = "{CALL [vmarou01].[AddApplicationStatus](?, 'Approved', NULL)}";
        $params = array(
            array($app_id, SQLSRV_PARAM_IN)
        );

        $stmt = sqlsrv_query($conn, $sql, $params);
        if ($stmt === false) {
            throw new Exception("Database query failed: " . print_r(sqlsrv_errors(), true));
        } else {
            echo "Application Approved Successfully";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
