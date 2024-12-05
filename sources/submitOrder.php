<?php
session_start();
include 'connect.php'; // Include your database connection script
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if application details exist in session
if (!isset($_SESSION['subsidy_category'], $_SESSION['app_id'], $_SESSION['user_id'])) {
    die("Session variables are not set. Redirecting to dealer.php...");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_vehicle_details'])) {
    // Fetch form data
    $vehicle_details = $_POST['vehicle_details'];
    $co2 = isset($_POST['co2']) && $_POST['co2'] !== "" ? (int) $_POST['co2'] : null; // Optional CO2 field
    $month_reg = $_POST['month_reg'];
    $year_reg = $_POST['year_reg'];
    $price = $_POST['price'];
    $aa_id = $_POST['aa_id'];
    $app_id = $_SESSION['app_id'];
    $new_orderID = 0; // Output parameter for the new order ID
    $doc_id = 0; // To capture document ID if uploaded
    $uploadDir = '/home/students/cs/2021/vmarou01/public_html/documents/'; // Path for the uploaded document

    // Validate required fields
    if (empty($aa_id) || empty($vehicle_details) || empty($month_reg) || empty($year_reg) || empty($price)) {
        echo "<script>alert('Please fill in all required fields.'); window.history.back();</script>";
        exit();
    }

    // Ensure database connection is established
    if (!isset($conn)) {
        die("Database connection not established.");
    }

    try {
        // Start a transaction
        sqlsrv_begin_transaction($conn);

        // Prepare parameters for the InsertOrderVehicle procedure
        $params = [
            [$vehicle_details, SQLSRV_PARAM_IN],
            [$co2, SQLSRV_PARAM_IN], // CO2 is optional
            [$month_reg, SQLSRV_PARAM_IN],
            [$year_reg, SQLSRV_PARAM_IN],
            [$price, SQLSRV_PARAM_IN],
            [$app_id, SQLSRV_PARAM_IN],
            [$aa_id, SQLSRV_PARAM_IN],
            [&$new_orderID, SQLSRV_PARAM_OUT], // Output parameter
        ];

        // Call the stored procedure
        $query = "{CALL InsertOrderVehicle(?, ?, ?, ?, ?, ?, ?, ?)}";
        $stmt = sqlsrv_query($conn, $query, $params);
        if ($stmt === false) {
            throw new Exception("InsertOrderVehicle failed: " . print_r(sqlsrv_errors(), true));
        }

        sqlsrv_free_stmt($stmt);

        if (!$new_orderID) {
            throw new Exception("Failed to retrieve the new Order ID.");
        }

        // Only handle document upload after the order is successfully inserted
        if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['document']['tmp_name'];
            $fileName = $_FILES['document']['name'];
            $fileSize = $_FILES['document']['size'];
            $fileType = $_FILES['document']['type'];
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

            // Ensure the upload directory exists
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Generate a unique file name
            $uniqueFileName = uniqid() . '.' . $fileExtension;
            $uploadPath = $uploadDir . $uniqueFileName;

            // Move the file to the upload directory
            if (!move_uploaded_file($fileTmpPath, $uploadPath)) {
                throw new Exception("Failed to upload the document.");
            }

            // Prepare parameters for the InsertDocument procedure
            $docParams = [
                [$fileType, SQLSRV_PARAM_IN],
                [$fileSize, SQLSRV_PARAM_IN],
                [$uploadPath, SQLSRV_PARAM_IN],
                [$app_id, SQLSRV_PARAM_IN], // Link to the new Order ID
                [$fileName, SQLSRV_PARAM_IN],
                ['order', SQLSRV_PARAM_IN], // Category
                [&$doc_id, SQLSRV_PARAM_OUT], // Output Document ID
            ];

            $docQuery = "{CALL InsertDocument(?, ?, ?, ?, ?, ?, ?)}";
            $docStmt = sqlsrv_query($conn, $docQuery, $docParams);

            if ($docStmt === false) {
                throw new Exception("InsertDocument failed: " . print_r(sqlsrv_errors(), true));
            }

            sqlsrv_free_stmt($docStmt);
        }

        // Commit the transaction
        sqlsrv_commit($conn);

        // Notify the user of successful submission
        echo "<script>alert('Order and document submitted successfully! Document ID: $doc_id'); window.location.href = 'mainDealer.php';</script>";
        exit();
    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        sqlsrv_rollback($conn);
        die("Error occurred: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Vehicle Order</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        /* General reset */
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Arial', sans-serif;
        }

        /* Full page container */
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            height: 100%;
            background: #2C2C2C; /* Dark background for a futuristic look */
        }

        /* Navbar style */
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

        /* Main content container */
        .form-container {
            text-align: center;
            width: 600px;
            padding: 30px;
            background: rgba(0, 0, 0, 0.5); /* Transparent dark background */
            backdrop-filter: blur(8px); /* Frosted glass effect */
            border-radius: 20px;
            box-shadow: 0px 10px 15px rgba(0, 255, 153, 0.5); /* Neon glow effect */
            color: #ffffff; /* White text */
            transition: all 0.3s ease-in-out;
            margin-top: 300px; /* Space below navbar */
        }

        .form-container:hover {
            transform: scale(1.05); /* Slight zoom effect */
            box-shadow: 0px 15px 25px rgba(0, 255, 153, 0.7); /* Stronger glow on hover */
        }

        /* Input fields */
        input[type="text"], input[type="number"], input[type="date"], input[type="file"] {
            width: 100%;
            padding: 12px;
            margin: 12px 0;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
            background: #1f1f1f; /* Dark background */
            color: #fff;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        input[type="text"]:hover, input[type="number"]:hover, input[type="date"]:hover, input[type="file"]:hover {
            border-color: #00FF99;
            box-shadow: 0 0 5px rgba(0, 255, 153, 0.5);
        }

        input[type="text"]:focus, input[type="number"]:focus, input[type="date"]:focus, input[type="file"]:focus {
            border-color: #00FF99;
            box-shadow: 0 0 8px rgba(0, 255, 153, 0.7);
            outline: none;
        }

        /* Button styling with neon glow */
        .btn-submit {
            padding: 15px;
            font-size: 16px;
            margin: 10px 0;
            width: 100%;
            color: #1F1F1F; /* Dark text */
            background-color: #00FF99; /* Neon green background */
            border: 2px solid #00FF99; /* Neon green border */
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            background-color: #1F1F1F; /* Dark background */
            color: #00FF99; /* Neon green text */
            transform: translateY(-3px); /* Slight raise effect */
            box-shadow: 0 6px 15px rgba(0, 255, 153, 0.6); /* Glowing effect on hover */
        }

        .btn-submit:focus {
            outline: none;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .form-container {
                width: 90%;
            }

            .btn-submit {
                width: 100%;
            }
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

    <!-- Vehicle Details Form -->
    <div class="form-container">
        <h2>Enter Vehicle Details</h2>

        <!-- Vehicle Details Form -->
        <form method="POST" action="" enctype="multipart/form-data">
    <input type="text" name="aa_id" placeholder="A.A. ID" required>
    <input type="text" name="vehicle_details" placeholder="Vehicle Details" required>
    <input type="number" name="co2" placeholder="CO2 Emission (g/km)" required min="0" step="any"> <!-- CO2 cannot be negative -->
    <input type="number" name="month_reg" placeholder="Month" min="1" step="1" max="12" required > 
    <input type="number" name="year_reg" placeholder="Year" min="2025" step="1" required >   
    <input type="number" name="price" placeholder="Price (€)" required min="0" step="1"> <!-- Price must be an integer -->
    <input type="file" name="document" required>
    <button type="submit" name="submit_vehicle_details" class="btn-submit">Order Vehicle</button>
</form>

    </div>

</body>
</html>