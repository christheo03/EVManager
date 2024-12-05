<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include 'connect.php';  // Ensure this file connects to your SQL Server database

// Ensure the session is valid and contains necessary variables
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
    die("Session variables are not set. Please log in again.");
}

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $subsidy = isset($_POST['subsidy']) ? $_POST['subsidy'] : '';
    $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
    $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;
    $entity_type = isset($_POST['entity_type']) ? $_POST['entity_type'] : '';
    $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : '';
    $option_select = isset($_POST['option_select']) ? $_POST['option_select'] : '';

    // Validate required fields
    if (empty($subsidy) || empty($option_select)) {
        echo "Please select the subsidy and option.";
        exit;
    }

    // Ensure the dates are either properly formatted or set to NULL
    $start_date = !empty($start_date) ? date('Y-m-d', strtotime($start_date)) : null;
    $end_date = !empty($end_date) ? date('Y-m-d', strtotime($end_date)) : null;

    // Determine which stored procedure to call based on the selected option
    switch ($option_select) {
        case '1':
            $query = "{CALL vmarou01.CalculateRemainingSubsidyByNames(?, ?, ?, ?)}";
            $params = [
                [$subsidy, SQLSRV_PARAM_IN],       // Subsidy names
                [$start_date, SQLSRV_PARAM_IN],   // Start date (optional)
                [$end_date, SQLSRV_PARAM_IN],     // End date (optional)
                [$order_by, SQLSRV_PARAM_IN]      // Order direction
            ];
            break;

        case '2':
            $query = "{CALL vmarou01.CalculateTotalDisbursedSubsidyByNames(?, ?, ?, ?)}";
            $params = [
                [$subsidy, SQLSRV_PARAM_IN],       // Subsidy names
                [$start_date, SQLSRV_PARAM_IN],   // Start date (optional)
                [$end_date, SQLSRV_PARAM_IN],     // End date (optional)
                [$order_by, SQLSRV_PARAM_IN]      // Order direction
            ];
            break;

        case '3': // New case for GetApplicationCountBySubsidyFiltered
            $query = "{CALL dbo.GetApplicationCountBySubsidyFiltered(?, ?, ?)}";
            $params = [
                [$subsidy, SQLSRV_PARAM_IN],       // Subsidy names
                [$start_date, SQLSRV_PARAM_IN],   // Start date (optional)
                [$end_date, SQLSRV_PARAM_IN]      // End date (optional)
            ];
            break;

        case '4': // New case for GetApplicationPercentageByCategory
            $query = "{CALL vmarou01.GetApplicationPercentageByCategory()}";
            $params = [];  // No parameters required for this stored procedure
            break;

        case '5': // New case for GetApplicationPercentageByCategoryApproved
            $query = "{CALL vmarou01.GetApplicationPercentageByCategoryApproved(?, ?, ?)}";
            $params = [
                [$subsidy, SQLSRV_PARAM_IN],       // Subsidy names (optional)
                [$start_date, SQLSRV_PARAM_IN],   // Start date (optional)
                [$end_date, SQLSRV_PARAM_IN]      // End date (optional)
            ];
            break;

        case '6': // New case for GetAverageSubsidyByCategory
            $query = "{CALL dbo.GetAverageSubsidyByCategory()}";
            $params = [];  // No parameters required for this stored procedure
            break;

        case '7': // New case for IdentifySubsidyExtremes
            $query = "{CALL vmarou01.IdentifySubsidyExtremes()}";
            $params = [];  // No parameters required for this stored procedure
            break;

        case '8': // New case for SubsidyApplicationsEveryMonthLastFourMonths
            $query = "{CALL vmarou01.SubsidyApplicationsEveryMonthLastFourMonths()}";
            $params = [];  // No parameters required for this stored procedure
            break;

        default:
            echo "Invalid option selected.";
            exit;
    }

    // Execute the stored procedure using SQLSRV
    $stmt = sqlsrv_query($conn, $query, $params);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Fetch the results
    echo "<h3>Report Results:</h3>";

    if ($option_select == '3') {
        // For Case 3 (GetApplicationCountBySubsidyFiltered)
        echo "<table border='1'><tr><th>Subsidy Name</th><th>Number of Applications</th></tr>";
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            echo "<tr><td>" . htmlspecialchars($row['subsidy_name']) . "</td><td>" . htmlspecialchars($row['NumberOfApplications']) . "</td></tr>";
        }
        echo "</table>";
    } elseif ($option_select == '4') {
        // For Case 4 (GetApplicationPercentageByCategory)
        echo "<table border='1'><tr><th>Subsidy Name</th><th>Percentage of Total Applications</th></tr>";
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            echo "<tr><td>" . htmlspecialchars($row['subsidy_name']) . "</td><td>" . htmlspecialchars($row['PercentageOfTotal']) . "%</td></tr>";
        }
        echo "</table>";
    } elseif ($option_select == '5') {
        // For Case 5 (GetApplicationPercentageByCategoryApproved)
        echo "<table border='1'><tr><th>Subsidy Name</th><th>Percentage of Approved Applications</th></tr>";
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            echo "<tr><td>" . htmlspecialchars($row['subsidy_name']) . "</td><td>" . htmlspecialchars($row['PercentageOfTotal']) . "%</td></tr>";
        }
        echo "</table>";
    } elseif ($option_select == '6') {
        // For Case 6 (GetAverageSubsidyByCategory)
        echo "<table border='1'><tr><th>Category</th><th>Average Subsidy Amount</th></tr>";
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            echo "<tr><td>" . htmlspecialchars($row['Category']) . "</td><td>" . htmlspecialchars($row['AverageSubsidyAmount']) . "</td></tr>";
        }
        echo "</table>";
    } elseif ($option_select == '7') {
        // For Case 7 (IdentifySubsidyExtremes)
        echo "<table border='1'><tr><th>Type</th><th>Subsidy Name</th><th>Total Disbursed Amount</th></tr>";
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            echo "<tr><td>" . htmlspecialchars($row['Type']) . "</td><td>" . htmlspecialchars($row['subsidy_name']) . "</td><td>" . htmlspecialchars($row['TotalDisbursed']) . "</td></tr>";
        }
        echo "</table>";
        
    } elseif ($option_select == '8') {
        // For Case 8 (SubsidyApplicationsEveryMonthLastFourMonths)
        echo "<table border='1'><tr><th>Subsidy Name</th></tr>";
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            echo "<tr><td>" . htmlspecialchars($row['subsidy_name']) . "</td></tr>";
        }
        echo "</table>";
    } else {
        // For Case 1 and Case 2 (RemainingAmount, TotalDisbursedAmount)
        echo "<table border='1'><tr><th>Subsidy Name</th><th>Amount</th></tr>";
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            if (isset($row['RemainingAmount'])) {
                echo "<tr><td>" . htmlspecialchars($row['subsidy_name']) . "</td><td>" . htmlspecialchars($row['RemainingAmount']) . "</td></tr>";
            } elseif (isset($row['TotalDisbursedAmount'])) {
                echo "<tr><td>" . htmlspecialchars($row['subsidy_name']) . "</td><td>" . htmlspecialchars($row['TotalDisbursedAmount']) . "</td></tr>";
            }
        }
        echo "</table>";
    }

    // Free statement and close the connection
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
}
?>
