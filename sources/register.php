<?php
// Include database connection
include 'connect.php'; // Ensure this file connects to your SQL Server database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect User data from the form 
    $user_id = $_POST['user_id'];
    $role = $_POST['role'];
    $birthdate = $_POST['birthdate'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure password hashing
    $email = $_POST['email'];
    $address = $_POST['address'];
    $nomiko_fisiko = $_POST['nomiko_fisiko'];
    try {
        // Prepare parameters for the stored procedure
        $registerUserParams = array(
            array($user_id, SQLSRV_PARAM_IN),
            array($role, SQLSRV_PARAM_IN),
            array($birthdate, SQLSRV_PARAM_IN),
            array($first_name, SQLSRV_PARAM_IN),
            array($last_name, SQLSRV_PARAM_IN),
            array($username, SQLSRV_PARAM_IN),
            array($password, SQLSRV_PARAM_IN),
            array($email, SQLSRV_PARAM_IN),
            array($address, SQLSRV_PARAM_IN),
            array($nomiko_fisiko, SQLSRV_PARAM_IN)
        );
        // Call the stored procedure
        $registerUserQuery = "{CALL RegisterUser(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)}";
        $stmt = sqlsrv_query($conn, $registerUserQuery, $registerUserParams);
        // Check for errors during execution
        if ($stmt === false) {
            $errors = sqlsrv_errors();
            $errorMessage = "An error occurred. Please try again.";

            // Parse and identify specific errors
            foreach ($errors as $error) {
                if (strpos($error['message'], 'The email is already registered') !== false) {
                    $errorMessage = "The email is already registered. Please use a different email.";
                } elseif (strpos($error['message'], 'The username is already taken') !== false) {
                    $errorMessage = "The username is already taken. Please choose a different username.";
                }
            }

            // Throw exception with the error message
            throw new Exception($errorMessage);
        }

        sqlsrv_free_stmt($stmt);
        $success = "User registered successfully!";
    } catch (Exception $e) {
        // Catch and store the error message
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <!-- Link to FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Arial', sans-serif;
            background: #2C2C2C; /* Dark background */
        }

        /* Center the form container */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            width: 100%;
            max-width: 500px;
            background: rgba(0, 0, 0, 0.6); /* Semi-transparent background */
            backdrop-filter: blur(8px); /* Frosted glass effect */
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 255, 153, 0.5); /* Neon glow */
            color: #fff; /* White text */
            text-align: center;
            transition: all 0.3s ease-in-out;
        }

        .form-container:hover {
            transform: scale(1.05); /* Slight zoom effect */
            box-shadow: 0 0 40px rgba(0, 255, 153, 1); /* Stronger glow on hover */
        }

        .form-container h2 {
            font-size: 24px;
            margin-bottom: 20px;
            text-shadow: 0 0 8px rgba(0, 255, 153, 0.6); /* Neon glow effect */
        }

        .form-container label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #fff;
        }

        .form-container input,
        .form-container select {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 2px solid #444; /* Dark border */
            border-radius: 8px;
            font-size: 16px;
            background: #1f1f1f; /* Dark background */
            color: #fff; /* White text */
            box-sizing: border-box;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-container input:focus,
        .form-container select:focus {
            border-color: #00FF99; /* Neon green border */
            box-shadow: 0 0 8px rgba(0, 255, 153, 0.7); /* Neon glow */
            outline: none;
        }

        .form-container button {
            width: 100%;
            padding: 15px;
            font-size: 16px;
            font-weight: bold;
            background-color: #00FF99; /* Neon green */
            color: #1f1f1f; /* Dark text */
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .form-container button:hover {
            background-color: #1f1f1f; /* Dark background */
            color: #00FF99; /* Neon green text */
            transform: translateY(-3px); /* Slight lift effect */
            box-shadow: 0 6px 15px rgba(0, 255, 153, 0.6); /* Glowing effect */
        }

        .error, .success {
            font-size: 14px;
            margin-bottom: 15px;
        }

        .error {
            color: red;
        }

        .success {
            color: green;
        }

        /* Responsive design for smaller screens */
        @media (max-width: 768px) {
            .form-container {
                width: 90%;
            }
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
     <!-- Enhanced Navbar with Icons -->
     <div class="navbar">
        
        <a href="index.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> LOGIN</a>
    </div>
    <div class="form-container">
        <h2>Register</h2>

        <!-- Display messages -->
        <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
        <?php if (isset($success)) echo "<div class='success'>$success</div>"; ?>

        <!-- Registration Form -->
        <form method="POST" action="">
            <label for="user_id">User ID:</label>
            <input type="text" id="user_id" name="user_id" required>

            <label for="role">Role:</label>
            <input type="text" id="role" name="role" required>

            <label for="birthdate">Birthdate:</label>
            <input type="date" id="birthdate" name="birthdate" required>

            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required>

            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="address">Address:</label>
            <input type="text" id="address" name="address" required>

            <label for="nomiko_fisiko">Legal Entity:</label>
            <select id="nomiko_fisiko" name="nomiko_fisiko" required>
                <option value="1">Yes</option>
                <option value="0">No</option>
            </select>

            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
