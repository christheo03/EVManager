<?php
session_start();
include 'connect.php'; // Ensure this file sets up a connection to your database correctly

// Check if the session has the required variables
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
    die("Session variables are not set. Please log in again.");
}

// Generate all subsidy names (C1 to C14)
$subsidyNames = [];
for ($i = 1; $i <= 14; $i++) {
    $subsidyNames[] = "C$i";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
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

        .container {
            width: 80%;
            margin: 230px auto;
            background: rgba(0, 0, 0, 0.7);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 255, 153, 0.6);
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #00FF99;
            text-shadow: 0 0 10px rgba(0, 255, 153, 0.6);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 18px;
            color: #00FF99;
            margin-bottom: 8px;
        }

        select, input[type="date"], input[type="text"] {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            color: #ffffff;
            background: #333;
            border: 2px solid #00FF99;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 255, 153, 0.4);
        }

        select:focus, input:focus {
            outline: none;
            border-color: #00e68a;
            box-shadow: 0 0 15px rgba(0, 255, 153, 0.6);
        }

        #subsidy-dropdown {
            position: relative;
        }

        #subsidy-options {
            display: none;
            position: absolute;
            background: #333;
            border: 2px solid #00FF99;
            border-radius: 5px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
        }

        #subsidy-options label {
            display: block;
            padding: 5px 10px;
            cursor: pointer;
        }

        #subsidy-options label:hover {
            background: #00FF99;
            color: #000;
        }

        .btn {
            padding: 15px;
            font-size: 18px;
            width: 100%;
            color: #00FF99;
            background-color: #333333;
            border: 2px solid #00FF99;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 255, 153, 0.4);
            transition: all 0.3s ease-in-out;
        }

        .btn:hover {
            background-color: #00FF99;
            color: #1F1F1F;
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 255, 153, 0.6);
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
        <a href="mainAdmin.php"><i class="fas fa-home"></i> Home</a>
        <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
        <a href="index.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="container">
        <h1>Generate Reports</h1>
        <form method="POST" action="generateReport.php">
            <!-- Subsidy Name Field -->
            <div class="form-group" id="subsidy-dropdown">
                <label for="subsidy">Select Subsidy Name(s):</label>
                <input type="text" id="subsidy" name="subsidy" readonly placeholder="Select Subsidy Name(s)">
                <div id="subsidy-options">
                    <?php foreach ($subsidyNames as $subsidy): ?>
                        <label>
                            <input type="checkbox" value="<?php echo htmlspecialchars($subsidy); ?>" class="subsidy-checkbox">
                            <?php echo htmlspecialchars($subsidy); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Date Range Fields -->
            <div class="form-group">
                <label for="start_date">Start Date:</label>
                <input type="date" name="start_date" id="start_date">
            </div>
            <div class="form-group">
                <label for="end_date">End Date:</label>
                <input type="date" name="end_date" id="end_date">
            </div>

            <!-- Nomiko or Fisiko -->
            <div class="form-group">
                <label for="entity_type">Select Entity Type:</label>
                <select name="entity_type" id="entity_type">
                    <option value="">-- Select Type --</option>
                    <option value="nomiko">Nomiko</option>
                    <option value="fisiko">Fisiko</option>
                </select>
            </div>

            <!-- Order By Field -->
            <div class="form-group">
                <label for="order_by">Order By:</label>
                <select name="order_by" id="order_by">
                    <option value="">-- Select Order --</option>
                    <option value="ASC">Ascending</option>
                    <option value="DESC">Descending</option>
                </select>
            </div>

            <!-- New Option Dropdown -->
            <div class="form-group">
                <label for="option_select">Select an Option:</label>
                <select name="option_select" id="option_select">
                    <option value="">-- Select Option --</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                </select>
            </div>

            <button type="submit" class="btn">Generate Report</button>
        </form>
    </div>

    <script>
        const subsidyInput = document.getElementById('subsidy');
        const subsidyOptions = document.getElementById('subsidy-options');
        const checkboxes = document.querySelectorAll('.subsidy-checkbox');

        // Toggle the dropdown visibility
        subsidyInput.addEventListener('click', () => {
            subsidyOptions.style.display = subsidyOptions.style.display === 'block' ? 'none' : 'block';
        });

        // Update the input field when checkboxes are selected
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                const selected = Array.from(checkboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.value);
                subsidyInput.value = selected.join(',');
            });
        });

        // Close the dropdown if clicked outside
        document.addEventListener('click', (e) => {
            if (!subsidyInput.contains(e.target) && !subsidyOptions.contains(e.target)) {
                subsidyOptions.style.display = 'none';
            }
        });
    </script>
</body>
</html>
