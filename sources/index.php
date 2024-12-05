<?php
session_start(); // Start the session
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password']; // The plain text password entered by the user

    try {
        // Call the stored procedure
        $loginQuery = "{CALL GetUserDetails(?)}";
        $params = array($username);

        $stmt = sqlsrv_query($conn, $loginQuery, $params);

        if ($stmt === false) {
            throw new Exception("SQL query failed: " . print_r(sqlsrv_errors(), true));
        }

        // Fetch the result
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

        if ($row) {
            // Debugging: Output the fetched role for inspection
            var_dump($row['role']); // Check role value
            
            // Verify the password
            if (password_verify($password, $row['password'])) {
                // Check if the user'.s role is 'Simple User' (case-insensitive)
                if (strtolower(trim($row['role'])) == 'simple user') {
                    // Store user data in session
                    $_SESSION['username'] = $username;
                    $_SESSION['user_id'] = $row['user_id'];
                    $_SESSION['email'] = $row['email'];

                    // Redirect to main.php for Simple User
                    header("Location: main.php");
                    exit();
                }
                // Check if the user's role is 'Dealer' (case-insensitive)
                elseif (strtolower(trim($row['role'])) == 'dealer') {
                    // Store user data in session
                    $_SESSION['username'] = $username;
                    $_SESSION['user_id'] = $row['user_id'];
                    $_SESSION['email'] = $row['email'];

                    // Redirect to dealer.php for Dealer
                    header("Location: mainDealer.php");
                    exit();
                } 
                // Check if the user's role is 'TOM Officer'
                elseif (strtolower(trim($row['role'])) == 'tom officer') {
                    // Store user data in session and redirect to mainOfficer.php
                    $_SESSION['username'] = $username;
                    $_SESSION['user_id'] = $row['user_id'];
                    $_SESSION['email'] = $row['email'];
                    header("Location: mainOfficer.php");
                    exit();
                } 
                elseif (strtolower(trim($row['role'])) == 'admin') {
                    // Store user data in session and redirect to mainOfficer.php
                    $_SESSION['username'] = $username;
                    $_SESSION['user_id'] = $row['user_id'];
                    $_SESSION['email'] = $row['email'];
                    header("Location: mainAdmin.php");
                    exit();
                } 
               
                else {
                    // Handle cases where the user role is neither 'Simple user' nor 'Dealer'
                    echo "<div class='error-message'>You do not have permission to access this page.</div>";
                }
            } else {
                echo "<div class='error-message'>Invalid username or password.</div>";
            }
        } else {
            echo "<div class='error-message'>Invalid username or password.</div>";
        }

        sqlsrv_free_stmt($stmt);
    } catch (Exception $e) {
        echo "<div class='error-message'>Error: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
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
            justify-content: center;
            height: 100%;
            background: #2C2C2C; /* Dark background for a futuristic look */
        }

        /* Title animation */
        .title {
            font-size: 60px;
            font-weight: bold;
            color: #00FF99; /* Neon green title */
            margin-bottom: 29px;
            text-shadow: 0 0 8px rgba(0, 255, 153, 0.6); /* Neon glow effect */
            animation: fadeInSlideDown 2s ease-out; /* Animation */
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
        .login-container {
            text-align: center;
            width: 400px;
            padding: 30px;
            background: rgba(0, 0, 0, 0.5); /* Transparent dark background */
            backdrop-filter: blur(8px); /* Frosted glass effect */
            border-radius: 20px;
            box-shadow: 0px 10px 15px rgba(0, 255, 153, 0.5); /* Neon glow effect */
            color: #ffffff; /* White text */
            transition: all 0.3s ease-in-out;
        }

        .login-container:hover {
            transform: scale(1.05); /* Slight zoom effect */
            box-shadow: 0px 15px 25px rgba(0, 255, 153, 0.7); /* Stronger glow on hover */
        }

        /* User icon */
        .user-icon {
            width: 80px;
            height: 80px;
            background-color: #00FF99; /* Neon green icon background */
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .user-icon svg {
            width: 40px;
            height: 40px;
            fill: #FFFFFF; /* White user icon */
        }

        /* Input fields with neon glow on focus */
        input[type="text"], input[type="password"] {
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

        input[type="text"]:hover, input[type="password"]:hover {
            border-color: #00FF99;
            box-shadow: 0 0 5px rgba(0, 255, 153, 0.5);
        }

        input[type="text"]:focus, input[type="password"]:focus {
            border-color: #00FF99;
            box-shadow: 0 0 8px rgba(0, 255, 153, 0.7);
            outline: none;
        }

        /* Buttons with neon glow effects */
        .btn-login, .btn-register {
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

        .btn-login:hover, .btn-register:hover {
            background-color: #1F1F1F; /* Dark background */
            color: #00FF99; /* Neon green text */
            transform: translateY(-3px); /* Slight raise effect */
            box-shadow: 0 6px 15px rgba(0, 255, 153, 0.6); /* Glowing effect on hover */
        }

        .btn-login:focus, .btn-register:focus {
            outline: none;
        }

        /* Error or success messages */
        .error-message {
            color: red;
            margin-bottom: 20px;
        }

        .success-message {
            color: green;
            margin-bottom: 20px;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .login-container {
                width: 90%;
            }

            .btn-login, .btn-register {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="title">EV MANAGER</div>
    
    <div class="login-container">
        <div class="user-icon">
            <!-- User icon using SVG -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M12 12c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm0 2c-3.31 0-10 1.67-10 5v3h20v-3c0-3.33-6.69-5-10-5z"/>
            </svg>
        </div>
        <h1>User Login</h1>

        <!-- Display error message -->
        <?php if (isset($error)) echo "<div class='error-message'>$error</div>"; ?>

        <!-- Login form -->
        <form action="index.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn-login">Login</button>
            <button type="button" onclick="window.location.href='register.php';" class="btn-register">Register</button>
        </form>
    </div>

</body>
</html>
