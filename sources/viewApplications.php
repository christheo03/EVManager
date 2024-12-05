<?php
session_start();
include 'connect.php';

// Check if the session is set
if (!isset($_SESSION['user_id'])) {
    die("User is not logged in. Please log in again.");
}

// Get the user ID from session
$user_id = $_SESSION['user_id'];

// Call the stored procedure to get applications by user
try {
    // Prepare the stored procedure call
    $query = "{CALL GetApplicationsByUser(?)}";
    $params = array($user_id);

    // Execute the stored procedure
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt === false) {
        throw new Exception("SQL query failed: " . print_r(sqlsrv_errors(), true));
    }

    // Fetch the results into an array
    $applications = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $applications[] = $row;
    }

    // Free the statement
    sqlsrv_free_stmt($stmt);

} catch (Exception $e) {
    // Handle errors
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Applications</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body, html {
            background: #2C2C2C; /* Dark background for consistency with main page */
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            height: 100%;
        }

        /* Enhanced Navbar style */
        .navbar {
            width: 100%;
            background: rgba(0, 0, 0, 0.6); /* Semi-transparent background for glass effect */
            padding: 15px 30px; /* Padding for better spacing */
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            backdrop-filter: blur(15px); /* Blur the background for frosted glass effect */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.8); /* Dark shadow for depth */
            display: flex;
            align-items: center;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1); /* Soft border for elegance */
        }

        .navbar a {
            color: #ffffff; /* Bright white color for contrast */
            text-decoration: none;
            padding: 10px 15px;
            font-size: 18px;
            transition: color 0.3s ease, transform 0.3s ease;
            border-radius: 8px; /* Slight rounding for links */
            display: flex;
            align-items: center;
            text-shadow: 0px 0px 8px rgba(255, 255, 255, 0.6); /* Neon glow effect */
        }

        .navbar a:hover {
            color: #00FF99; /* Neon green hover color */
            transform: scale(1.1); /* Slight scaling effect */
        }

        .navbar a i {
            margin-right: 8px;
            font-size: 1.3em;
        }

        /* Main Content - Box Styling with Neon Glow */
        .content {
            margin-top: 130px; /* Avoid overlap with fixed navbar */
            width: 80%;
            margin-left: auto;
            margin-right: auto;
            padding: 30px;
            background: rgba(0, 0, 0, 0.7); /* Dark, transparent background */
            border-radius: 15px;
            border: 2px solid transparent;
            background-clip: padding-box;
            box-shadow: 0 0 20px rgba(0, 255, 153, 0.6); /* Neon green glow */
            color: white;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .content:hover {
            transform: scale(1.05); /* Slight zoom effect on hover */
            box-shadow: 0 0 40px rgba(0, 255, 153, 1); /* Stronger glow on hover */
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
            color: #fff;
        }

        th {
            background-color: #333;
            text-shadow: 0px 0px 4px rgba(255, 255, 255, 0.3); /* Soft text shadow */
        }

        td {
            background-color: #222;
        }

        tr:hover {
            background-color: #444; /* Hover effect on rows */
        }

        /* Button Styling */
        .btn-back {
            padding: 12px 20px;
            font-size: 18px;
            color: #00FF99; /* Neon green text */
            background-color: #333333; /* Dark button background */
            border: 2px solid #00FF99; /* Neon green border */
            border-radius: 8px;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.3s ease-in-out;
        }

        .btn-back:hover {
            background-color: #00FF99; /* Neon green on hover */
            color: #1F1F1F; /* Dark text when hovered */
            transform: translateY(-3px); /* Slight lift effect */
            box-shadow: 0 6px 15px rgba(0, 255, 153, 0.6); /* Glowing effect */
        }

        .btn-back:focus {
            outline: none;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .content {
                width: 90%;
                padding: 20px;
            }

            .btn-back {
                width: 100%;
                font-size: 16px;
            }

            .navbar {
                flex-direction: column;
                padding: 20px;
            }

            .navbar a {
                margin-bottom: 10px;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>

    <!-- Enhanced Navbar with Icons -->
    <div class="navbar">
        <a href="main.php"><i class="fas fa-home"></i> Home</a>
        <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
        <a href="index.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <h1>My Applications</h1>

        <!-- If there are no applications -->
        <?php if (empty($applications)): ?>
            <p>You have no applications yet.</p>
        <?php else: ?>
            <!-- Table to display applications -->
            <table>
                <thead>
                    <tr>
                        <th>Application ID</th>
                        <th>Date of Application</th>
                        <th>Subsidy Name</th>
                        <th>Car ID</th>
                        <th>Stage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $application): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($application['app_id']); ?></td>
                            <td><?php echo htmlspecialchars($application['date_of_app']->format('Y-m-d H:i:s')); ?></td>
                            <td><?php echo htmlspecialchars($application['subsidy_name']); ?></td>
                            <td><?php echo htmlspecialchars($application['carID']); ?></td>
                            <td><?php echo htmlspecialchars($application['stage']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- Back Button -->
        <a href="main.php"><button class="btn-back">Back to Main</button></a>
    </div>

</body>
</html>
