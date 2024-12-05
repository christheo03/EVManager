<?php
session_start();
include 'connect.php'; // Ensure this file sets up a connection to your database correctly

// Check if the session has the required variables
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
    die("Session variables are not set. Please log in again.");
}

// Fetch data from the database using the updated stored procedure
try {
    $sql = "{CALL [vmarou01].[GetApplicationDetails]}"; // Updated procedure name
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) {
        throw new Exception("Database query failed: " . print_r(sqlsrv_errors(), true));
    }

    
    echo "Number of rows fetched: $rowCount";
    // Prepare the data to group documents by application
    $applications = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $app_id = $row['app_id'];
        if (!isset($applications[$app_id])) {
            $applications[$app_id] = [
                'app_id' => $row['app_id'],
                'date_of_app' => $row['date_of_app'],
                'user_id' => $row['user_id'],
                'subsidy_name' => $row['subsidy_name'],
                'carID' => $row['carID'],
                'order_id' => $row['order_id'],
                'order_date' => $row['order_date'],
                'vehicle_details' => $row['vehicle_details'],
                'CO2' => $row['CO2'],
                'price' => $row['price'],
                'month_reg' => $row['month_reg'],
                'year_reg' => $row['year_reg'],
                'status_id' => $row['status_id'],
                'stage' => $row['stage'],
                'status_date_of_modify' => $row['status_date_of_modify'],
                'reason_of_modify' => $row['reason_of_modify'],
                'documents' => []
            ];
        }
    
        if ($row['doc_id']) {
            $applications[$app_id]['documents'][] = [
                'doc_id' => $row['doc_id'],
                'type' => $row['type'],
                'size_of_doc' => $row['size_of_doc'],
                'up_date' => $row['up_date'],
                'path' => $row['path'],
                'title' => $row['title'],
                'category' => $row['category']
            ];
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Applications</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background: #2C2C2C;
            color: #ffffff;
            height: 100%;
        }
   /* Enhanced Navbar style */
   .navbar {
            width: 100%;
            background: rgba(0, 0, 0, 0.6); /* Semi-transparent black for a glass effect */
            padding: 15px 30px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            backdrop-filter: blur(15px); /* Strong blur for the frosted glass effect */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.8); /* Dark shadow for depth */
            display: flex;
            align-items: center;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        }

        .navbar a {
            color: #ffffff; /* White color for text */
            text-decoration: none;
            padding: 10px 15px;
            font-size: 18px;
            transition: color 0.3s ease, transform 0.3s ease;
            border-radius: 8px;
            display: flex;
            align-items: center;
            text-shadow: 0 0 8px rgba(255, 255, 255, 0.6); /* Neon glow effect */
        }

        .navbar a:hover {
            color: #00FF99; /* Neon green hover color */
            transform: scale(1.1); /* Slight scaling effect */
        }

        .navbar a i {
            margin-right: 8px;
            font-size: 1.3em;
        }
        table {
            width: 92%;
            margin-left: 100px;
            margin-top: 100px;
            border-collapse: collapse;
            box-shadow: 0 0 20px rgba(0, 255, 153, 0.6); /* Neon green glow */
        }

        th, td {
            border: 1px solid #444; /* Darker border for contrast */
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #00FF99; /* Neon green */
            color: #000; /* Black text for contrast */
            text-shadow: 0 0 8px rgba(0, 255, 153, 0.6); /* Glow effect */
        }

        button {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            background-color: #00FF99; /* Neon green */
            color: #000; /* Black text for contrast */
            cursor: pointer;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        button:hover {
            background-color: #00e68a; /* Slightly darker neon green */
            transform: scale(1.05); /* Slight zoom on hover */
        }

        button.reject {
            background-color: #ff4444; /* Bright red for reject */
            color: white; /* White text for contrast */
        }

        button.reject:hover {
            background-color: #cc0000; /* Darker red on hover */
        }

    </style>
</head>
<body>
    <div class="navbar">
        <a href="mainAdmin.php"><i class="fas fa-home"></i> Home</a>
        <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
        <a href="index.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
    <h1 style="text-align: center; margin-top: 150px; color: #00e68a;">Application List</h1>
    <table>
        <thead>
            <tr>
                <th>App ID</th>
                <th>Date of Application</th>
                <th>User ID</th>
                <th>Subsidy Name</th>
                <th>Car ID</th>
                <th>Order ID</th>
                <th>Order Date</th>
                <th>Vehicle Details</th>
                <th>CO2 Emissions</th>
                <th>Price</th>
                <th>Month Registered</th>
                <th>Year Registered</th>
                <th>Documents</th>
                <th>Status ID</th>
                <th>Stage</th>
                <th>Status Date Modified</th>
                <th>Reason for Modification</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
    <?php foreach ($applications as $app) : ?>
        <tr>
            <td><?php echo htmlspecialchars($app['app_id']); ?></td>
            <td><?php echo !empty($app['date_of_app']) ? htmlspecialchars($app['date_of_app']->format('Y-m-d')) : 'N/A'; ?></td>
            <td><?php echo htmlspecialchars($app['user_id']); ?></td>
            <td><?php echo htmlspecialchars($app['subsidy_name']); ?></td>
            <td><?php echo !empty($app['carID']) ? htmlspecialchars($app['carID']) : 'N/A'; ?></td>
            <td><?php echo !empty($app['order_id']) ? htmlspecialchars($app['order_id']) : 'N/A'; ?></td>
            <td><?php echo !empty($app['order_date']) ? htmlspecialchars($app['order_date']->format('Y-m-d')) : 'N/A'; ?></td>
            <td><?php echo !empty($app['vehicle_details']) ? htmlspecialchars($app['vehicle_details']) : 'N/A'; ?></td>
            <td><?php echo !empty($app['CO2']) ? htmlspecialchars($app['CO2']) : 'N/A'; ?></td>
            <td><?php echo !empty($app['price']) ? htmlspecialchars($app['price']) : 'N/A'; ?></td>
            <td><?php echo !empty($app['month_reg']) ? htmlspecialchars($app['month_reg']) : 'N/A'; ?></td>
            <td><?php echo !empty($app['year_reg']) ? htmlspecialchars($app['year_reg']) : 'N/A'; ?></td>
            <td>
                <?php if (!empty($app['documents'])): ?>
                    <ul>
                        <?php foreach ($app['documents'] as $doc): ?>
                            <li     <strong> <?php echo htmlspecialchars($doc['title']); ?></strong> 
                                (<?php echo htmlspecialchars($doc['type']); ?>, <?php echo htmlspecialchars($doc['size_of_doc']); ?> KB)
                                - Uploaded on <?php echo htmlspecialchars($doc['up_date']->format('Y-m-d')); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <span>No Documents</span>
                <?php endif; ?>
            </td>
            <td><?php echo htmlspecialchars($app['status_id']); ?></td>
            <td><?php echo htmlspecialchars($app['stage']); ?></td>
            <td><?php echo !empty($app['status_date_of_modify']) ? htmlspecialchars($app['status_date_of_modify']->format('Y-m-d')) : 'N/A'; ?></td>
            <td><?php echo !empty($app['reason_of_modify']) ? htmlspecialchars($app['reason_of_modify']) : 'N/A'; ?></td>
            <td class="action-buttons">
                <?php if (strtolower($app['stage']) !== 'approved' && strtolower($app['stage']) !== 'reject') : ?>
                    <button class="reject" onclick="rejectApplication(<?php echo $app['app_id']; ?>)">Reject</button>
                    <button class="approve" onclick="approveApplication(<?php echo $app['app_id']; ?>)">Approve</button>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>


    </table>

    <script>
        function rejectApplication(appId) {
            const reason = prompt("Please enter the reason for rejection:");
            if (reason !== null && reason.trim() !== "") {
                const formData = new FormData();
                formData.append('app_id', appId);
                formData.append('reason', reason);

                fetch('reject.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    alert(data); // Display the server's response
                    location.reload(); // Reload the page to refresh the data
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            } else {
                alert("Rejection reason is required!");
            }
        }

        function approveApplication(appId) {
            if (confirm("Are you sure you want to approve this application?")) {
                const formData = new FormData();
                formData.append('app_id', appId);

                fetch('approve.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    alert(data); // Display the server's response
                    location.reload(); // Reload the page to refresh the data
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        }
    </script>
</body>
</html>
