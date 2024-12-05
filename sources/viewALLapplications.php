<?php
session_start();
include 'connect.php'; // This will include your database connection

// Check if the session has the required variables
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
    die("Session variables are not set. Please log in again.");
}

// Fetch applications by calling the stored procedure
$applications = [];
try {
    $stmt = sqlsrv_query($conn, "EXEC vmarou01.GetActiveOrSentApplications");

    if ($stmt === false) {
        throw new Exception("SQL query failed: " . print_r(sqlsrv_errors(), true));
    }

    // Fetch all the rows into the $applications array
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $applications[] = $row;
    }

    // Free the statement
    sqlsrv_free_stmt($stmt);
} catch (Exception $e) {
    die("Error fetching applications: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View All Applications</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Add your custom styles here */
    </style>
</head>
<body>

    <!-- Navbar (same as before) -->
    <div class="navbar">
        <a href="mainOfficer.php"><i class="fas fa-home"></i> Home</a>
        <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
        <a href="index.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Main content -->
    <div class="content">
        <h1>Active or Sent Applications</h1>

        <!-- Check if there are any applications -->
        <?php if (count($applications) > 0): ?>
            <table border="1" cellpadding="10">
                <thead>
                    <tr>
                        <th>Application ID</th>
                        <th>Date of Application</th>
                        <th>User ID</th>
                        <th>Subsidy Name</th>
                        <th>Car ID</th>
                        <th>Status</th>
                        <th>Stage</th>
                        <th>Reason for Modify</th>
                        <th>Document ID</th>
                        <th>Document Type</th>
                        <th>Document Size</th>
                        <th>Document Upload Date</th>
                        <th>Document Path</th>
                        <th>Document Title</th>
                        <th>Document Category</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $application): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($application['app_id']); ?></td>
                            <td><?php echo htmlspecialchars($application['date_of_app']); ?></td>
                            <td><?php echo htmlspecialchars($application['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($application['subsidy_name']); ?></td>
                            <td><?php echo htmlspecialchars($application['carID']); ?></td>
                            <td><?php echo htmlspecialchars($application['status_id']); ?></td>
                            <td><?php echo htmlspecialchars($application['stage']); ?></td>
                            <td><?php echo htmlspecialchars($application['reason_of_modify']); ?></td>
                            <td><?php echo htmlspecialchars($application['doc_id']); ?></td>
                            <td><?php echo htmlspecialchars($application['type']); ?></td>
                            <td><?php echo htmlspecialchars($application['size_of_doc']); ?></td>
                            <td><?php echo htmlspecialchars($application['up_date']); ?></td>
                            <td><?php echo htmlspecialchars($application['path']); ?></td>
                            <td><?php echo htmlspecialchars($application['title']); ?></td>
                            <td><?php echo htmlspecialchars($application['category']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No active or sent applications found.</p>
        <?php endif; ?>
    </div>

</body>
</html>
