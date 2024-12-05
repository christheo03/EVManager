<?php
session_start();
include 'connect.php';

// Check if the session has the required variables
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
    die("Session variables are not set. Please log in again.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dealer Main Page</title>
    <!-- Link to FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General reset */
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            height: 100%;
        }

        /* Full page container */
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            background: #2C2C2C; /* Dark background for a futuristic look */
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

        /* Main Content - futuristic neon box */
        .content {
            margin-top: 180px;
            width: 600px;
            text-align: center;
            padding: 30px;
            background: #1F1F1F; /* Dark background for the content box */
            border-radius: 15px;
            border: 2px solid transparent;
            background-clip: padding-box; /* To show the border glow */
            box-shadow: 0 0 20px rgba(0, 255, 153, 0.5); /* Neon glow effect */
            transition: all 0.3s ease-in-out;
            color: #ffffff; /* White text color */
        }

        .content:hover {
            transform: scale(1.05); /* Slight zoom effect */
            box-shadow: 0 0 40px rgba(0, 255, 153, 1); /* Stronger glow on hover */
        }

        /* Button styling with neon glow */
        .btn {
            padding: 15px;
            font-size: 18px;
            margin: 18px 0;
            width: 550px;
            color: #00FF99; /* Neon green text */
            background-color: #333333; /* Dark button background */
            border: 2px solid #00FF99; /* Neon green border */
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 255, 153, 0.4); /* Neon shadow */
            transition: all 0.3s ease-in-out;
        }

        .btn:hover {
            background-color: #00FF99; /* Neon background */
            color: #1F1F1F; /* Dark text when hovered */
            transform: translateY(-5px); /* Slight raise effect */
            box-shadow: 0 6px 15px rgba(0, 255, 153, 0.6); /* Glowing effect on hover */
        }

        .btn:focus {
            outline: none;
        }

        /* Center buttons */
        .button-container {
            display: flex;
            position: fixed;
            top: 17%;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 200px);
            width: 100%;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .content {
                width: 90%;
            }

            .btn {
                width: 100%;
            }

            .navbar {
                flex-direction: column;
                padding: 20px;
            }

            .navbar a {
                margin-bottom: 10px;
                font-size: 18px;
            }
        }

    </style>
</head>
<body>

    <!-- Enhanced Navbar with Icons -->
    <div class="navbar">
        <a href="mainOfficer.php"><i class="fas fa-home"></i> Home</a>
        <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
        <a href="index.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <p>You are successfully logged in as an Officer.</p>
    </div>
    <div class="button-container">
        <a href="mainOfficer.php">
            <a href="viewALLaplications.php"><button class="btn"><i class="fas fa-folder-open"></i> View All Aplications</button></a>
            </a>
    </div>
 

</body>
</html>
