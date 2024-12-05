<?php
session_start();
include 'connect.php'; // Include your database connection script
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Get the user's A.A. ID from the session
$aa_id = $_SESSION['user_id']; // Assuming the user ID matches the A.A. ID

// Fetch orders and documents for the user
$orders = [];
try {
    // Prepare and execute the stored procedure
    $query = "{CALL GetOrdersAndDocumentsByAAID(?)}";
    $stmt = sqlsrv_query($conn, $query, [$aa_id]);

    if ($stmt === false) {
        throw new Exception("Failed to execute the stored procedure: " . print_r(sqlsrv_errors(), true));
    }

    // Fetch the results into an array
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $orders[] = $row;
    }

    sqlsrv_free_stmt($stmt);
} catch (Exception $e) {
    die("Error fetching orders: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #2C2C2C;
            color: #ffffff;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #00FF99;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #333333;
            color: #00FF99;
        }

        table tr:nth-child(even) {
            background-color: #444444;
        }

        .btn-back {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #00FF99;
            color: #1F1F1F;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }

        .btn-back:hover {
            background-color: #1F1F1F;
            color: #00FF99;
        }

        .btn-action {
            display: inline-block;
            padding: 8px 12px;
            margin: 5px 0;
            color: #ffffff;
            background-color: #00FF99;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }

        .btn-action:hover {
            background-color: #1F1F1F;
            color: #00FF99;
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
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <a href="mainDealer.php"><i class="fas fa-home"></i> Home</a>
        <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
        <a href="index.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="container">
        <h1>Your Orders</h1>

        <?php if (empty($orders)): ?>
            <p>No orders found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Order Date</th>
                        <th>Vehicle Details</th>
                        <th>CO2 Emissions</th>
                        <th>Price (€)</th>
                        <th>Registration Month</th>
                        <th>Registration Year</th>
                        <th>Document ID</th>
                        <th>Document Title</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                            <td><?php echo htmlspecialchars($order['order_date']->format('Y-m-d')); ?></td>
                            <td><?php echo htmlspecialchars($order['vehicle_details']); ?></td>
                            <td><?php echo htmlspecialchars($order['CO2'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($order['price']); ?></td>
                            <td><?php echo htmlspecialchars($order['month_reg']); ?></td>
                            <td><?php echo htmlspecialchars($order['year_reg']); ?></td>
                            <td><?php echo htmlspecialchars($order['doc_id'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($order['title'] ?? 'N/A'); ?></td>
                            <td>
                                <?php if (!empty($order['path'])): ?>
                                    <!-- View Document -->
                                    <a href="<?php echo htmlspecialchars($order['path']); ?>" target="_blank" class="btn-action">View</a>
                                    <!-- Download Document -->
                                    <a href="<?php echo htmlspecialchars($order['path']); ?>" download class="btn-action">Download</a>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a href="mainDealer.php" class="btn-back">Back to Dashboard</a>
    </div>
</body>
</html>

