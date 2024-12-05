<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
    header("Location: index.php"); // Redirect to login if not logged in
    exit();
}

include 'connect.php'; // Include your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['find_application'])) {
    $userId = $_POST['user_id']; // Get user ID from form
    $subsidyCategory = $_POST['subsidy_category']; // Get subsidy category from form
    $appId = $_POST['app_id']; // Get application ID from form

    try {
        // Call the stored procedure
        $query = "{CALL CheckApplicationExistence(?, ?, ?)}";
        $params = [$appId, $userId, $subsidyCategory];

        $stmt = sqlsrv_query($conn, $query, $params);

        if ($stmt === false) {
            throw new Exception("Database query failed: " . print_r(sqlsrv_errors(), true));
        }

        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

        if ($row['ApplicationExists'] == 1) {
            // Application exists, store data in the session
            $_SESSION['subsidy_category'] = $subsidyCategory;
            $_SESSION['app_id'] = $appId;
            $_SESSION['user_id'] = $userId;

            // Redirect to the next page
            header("Location: submitOrder.php");
            exit();
        } else {
            // Application does not exist, show an error message
            $error = "The specified application does not exist. Please check the details and try again.";
        }

        sqlsrv_free_stmt($stmt);
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dealer Dashboard</title>
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

       

        @keyframes fadeInSlideDown {
            0% {
                opacity: 0;
                transform: translateY(-50px); /* Start slightly above */
            }
            100% {
                opacity: 1;
                transform: translateY(0); /* End at normal position */
            }
        }

        /* Main card container with frosted glass effect */
        .form-container {
            margin-top: 400px;
            text-align: center;
            width: 400px;
            padding: 30px;
            background: rgba(0, 0, 0, 0.5); /* Transparent dark background */
            backdrop-filter: blur(8px); /* Frosted glass effect */
            border-radius: 20px;
            box-shadow: 0px 10px 15px rgba(0, 255, 153, 0.5); /* Neon glow effect */
            color: #ffffff; /* White text */
            transition: all 0.3s ease-in-out;
            margin-bottom: 20px;
        }

        .form-container:hover {
            transform: scale(1.05); /* Slight zoom effect */
            box-shadow: 0px 15px 25px rgba(0, 255, 153, 0.7); /* Stronger glow on hover */
        }

        /* Input fields with neon glow on focus */
        input[type="text"], input[type="number"], input[type="date"] {
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

        input[type="text"]:hover, input[type="number"]:hover, input[type="date"]:hover {
            border-color: #00FF99;
            box-shadow: 0 0 5px rgba(0, 255, 153, 0.5);
        }

        input[type="text"]:focus, input[type="number"]:focus, input[type="date"]:focus {
            border-color: #00FF99;
            box-shadow: 0 0 8px rgba(0, 255, 153, 0.7);
            outline: none;
        }

        /* Buttons with neon glow effects */
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


    <div class="form-container">
        <h2>Enter Application Details</h2>

        <!-- Display error message if exists -->
        <?php if (isset($error)) echo "<div class='error-message'>$error</div>"; ?>

        <!-- Application Form -->
        <form method="POST" action="dealer.php">
            <input type="text" name="subsidy_category" placeholder="Subsidy Category" required>
            <input type="text" name="app_id" placeholder="Application ID" required>
            <input type="text" name="user_id" placeholder="User ID" required>
            <button type="submit" name="find_application" class="btn-submit">Find Application</button>
        </form>
    </div>

</body>
</html>
