<?php
session_start();
include 'connect.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || !isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// Fetch subsidies from the database
$subsidies = [];
try {
    $stmt = sqlsrv_query($conn, "{CALL GetAllSubsidies()}");

    if ($stmt === false) {
        throw new Exception("SQL query failed: " . print_r(sqlsrv_errors(), true));
    }

    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $subsidies[] = $row;
    }
    sqlsrv_free_stmt($stmt);
} catch (Exception $e) {
    die("Error fetching subsidies: " . $e->getMessage());
}

if (isset($_FILES['document'])) {
    echo "<pre>";
    print_r($_FILES['document']);
    echo "</pre>";
}


// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $user_id = $_POST['user_id'] ?? null;
    $subsidy_name = $_POST['subsidy_name'] ?? null;
    $car_id = $_POST['car_id'] ?? null;  // Car ID could be optional
    $newAppId = 0; // Output parameter for the new Application ID
    $doc_id = 0; // To capture document ID if uploaded
    $uploadDir = '/home/students/cs/2021/vmarou01/public_html/documents/'; // Path for the uploaded document

    // Check required fields
    if (empty($user_id) || empty($subsidy_name)) {
    } else {
        try {
            // Insert the application into the database
            $params = [
                [$user_id, SQLSRV_PARAM_IN],
                [$subsidy_name, SQLSRV_PARAM_IN],
                [$car_id ? $car_id : NULL, SQLSRV_PARAM_IN], // Include car_id if provided
                [&$newAppId, SQLSRV_PARAM_OUT], // Bind output parameter
            ];

            $query = "{CALL InsertApplication(?, ?, ?, ?)}";
            $stmt = sqlsrv_query($conn, $query, $params);

            if ($stmt === false) {
                // Get SQL Server errors and show them
                $errors = sqlsrv_errors();
                $errorMessage = isset($errors[0]['message']) ? $errors[0]['message'] : "An unknown error occurred.";
                throw new Exception("SQL Error: " . $errorMessage);
            }
            sqlsrv_free_stmt($stmt);

            if (!$newAppId) {
                throw new Exception("Failed to retrieve the new Application ID.");
            }

            // Handle file upload if a file is provided
            if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['document']['tmp_name'];
                $fileName = $_FILES['document']['name'];
                $fileSize = $_FILES['document']['size'];
                $fileType = $_FILES['document']['type'];
                $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                $category = 'certificate';
                // Check if the directory exists and is writable
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true); // Create directory if not exists
                }

                // Generate a unique file name
                $uniqueFileName = uniqid() . '.' . $fileExtension;
                $uploadPath = $uploadDir . $uniqueFileName;

                // Move the file to the desired directory
                if (move_uploaded_file($fileTmpPath, $uploadPath)) {
                    // Insert the document metadata into the database
                    $docParams = [
                        [$fileType, SQLSRV_PARAM_IN],
                        [$fileSize, SQLSRV_PARAM_IN],
                        [$uploadPath, SQLSRV_PARAM_IN],
                        [$newAppId, SQLSRV_PARAM_IN], // Link to the new Application ID
                        [$fileName, SQLSRV_PARAM_IN],
                        [$category, SQLSRV_PARAM_IN],
                        [&$doc_id, SQLSRV_PARAM_OUT], // Output Document ID
                    ];

                    $docQuery = "{CALL InsertDocument(?, ?, ?, ?, ?, ?, ?)}";
                    $docStmt = sqlsrv_query($conn, $docQuery, $docParams);

                    if ($docStmt === false) {
                        throw new Exception("Failed to insert document: " . print_r(sqlsrv_errors(), true));
                    }
                    sqlsrv_free_stmt($docStmt);

                    // If document upload is successful, inform the user
                    echo "<script>alert('Application and Document submitted successfully! Document ID: $doc_id'); window.location.href = 'main.php';</script>";
                    exit();
                } else {
                    throw new Exception("Failed to upload the document.");
                }
            }

            // Success without document
            echo "<script>alert('Application submitted successfully!'); window.location.href = 'main.php';</script>";
            exit();
       
    } catch (Exception $e) {
        // Handle errors and display them to the user
        echo "<script>alert('Error: " . addslashes($e->getMessage()) . "'); window.location.href = 'main.php';</script>";
        exit();
    }

    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Application</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General reset */
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Open Sans', sans-serif;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #2C2C2C; /* Dark background for a futuristic look */
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

        /* Centered Container Style for Application Form */
        .container {
            background: rgba(0, 0, 0, 0.5); /* Transparent background for frosted glass effect */
            backdrop-filter: blur(8px); /* Frosted glass effect */
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 500px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 255, 153, 0.4);
            transition: all 0.3s ease;
        }

        .container:hover {
            transform: scale(1.02); /* Slight zoom effect on hover */
        }

        h1 {
            font-size: 2.5em;
            color: #00FF99; /* Neon green title */
            margin-bottom: 20px;
        }

        label {
            font-weight: 600;
            margin-top: 15px;
            display: block;
            color: #ddd;
            text-align: left;
        }

        /* Styling for form fields to make them symmetrical */
        input[type="text"], input[type="email"], select, input[type="file"] {
            width: 100%; /* Ensure all fields take up full width */
            padding: 14px; /* Adjusted padding for consistency */
            border: 2px solid #ddd;
            border-radius: 10px; /* Symmetric border radius */
            margin-top: 8px;
            font-size: 16px;
            outline: none;
            transition: all 0.3s ease;
            background: #1f1f1f;
            color: #fff;
        }

        /select {
    width: 100%;  /* Ensure full width */
    padding: 14px; /* Same padding as other inputs */
    border: 2px solid #ddd; /* Same border as other inputs */
    border-radius: 10px; /* Same border radius */
    margin-top: 8px;
    font-size: 16px; /* Same font size as text inputs */
    outline: none;
    transition: all 0.3s ease;
    background: #1f1f1f; /* Same background as other inputs */
    color: #fff; /* Text color for consistency */
}

select:focus {
    border-color: #00FF99; /* Glowing border on focus */
    box-shadow: 0 0 8px rgba(0, 255, 153, 0.7); /* Glowing effect on focus */
}


        input[type="text"]:focus, input[type="email"]:focus, select:focus, input[type="file"]:focus {
            border-color: #00FF99;
            box-shadow: 0 0 8px rgba(0, 255, 153, 0.7); /* Glowing effect on focus */
        }

        button {
    background-color: #1F1F1F;  /* Dark background */
    color: #00FF99; /* Neon green text */
    border: 2px solid #00FF99; /* Neon green border */
    padding: 15px 30px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 20px;
}

button:hover {
    background-color: #00FF99; /* Neon green background on hover */
    color: #1F1F1F; /* Dark text on hover */
    transform: translateY(-3px); /* Slight lift effect */
    box-shadow: 0 6px 15px rgba(0, 255, 153, 0.7); /* Neon glow effect */
}


        .hidden {
            display: none;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 30px;
            }

            .navbar {
                flex-direction: column;
                padding: 20px;
            }

            .navbar a {
                margin-bottom: 10px;
                font-size: 14px;
            }
        }

    </style>
    <script>
        function toggleFields() {
            const subsidySelect = document.getElementById('subsidy_name');
            const carIdField = document.getElementById('car_id_field');
            const documentUploadField = document.getElementById('document_upload_field');

            // Show/Hide Car ID field for subsidies 'C1', 'C2', 'C3', 'C4'
            if (['C1', 'C2', 'C3', 'C4'].includes(subsidySelect.value)) {
                carIdField.classList.remove('hidden');
            } else {
                carIdField.classList.add('hidden');
            }

            // Show/Hide Document Upload field for subsidies 'C3', 'C4', 'C7', 'C8'
            if (['C3', 'C4', 'C7', 'C8'].includes(subsidySelect.value)) {
                documentUploadField.classList.remove('hidden');
            } else {
                documentUploadField.classList.add('hidden');
            }
        }
    </script>
</head>
<body>

    <!-- Enhanced Navbar with Icons -->
    <div class="navbar">
        <a href="main.php"><i class="fas fa-home"></i> Home</a>
        <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
        <a href="index.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Main Content (Centered Form) -->
    <div class="container">
        <h1>Submit Application</h1>
        <form action="makeApplication.php" method="POST" enctype="multipart/form-data">
            
            <!-- Subsidy Selection -->
            <div class="form-group">
                <label for="subsidy_name">Choose Subsidy:</label>
                <select name="subsidy_name" id="subsidy_name" onchange="toggleFields()" required>
                    <option value="" disabled selected>-- Select Subsidy --</option>
                    <?php foreach ($subsidies as $subsidy): ?>
                        <option value="<?php echo htmlspecialchars($subsidy['name']); ?>">
                            <?php echo htmlspecialchars($subsidy['name']); ?> - <?php echo htmlspecialchars($subsidy['amount']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- User ID -->
            <div class="form-group">
                <label for="user_id">User ID:</label>
                <input type="text" name="user_id" id="user_id" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>" readonly>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" readonly>
            </div>

            <!-- Car ID (Visible for certain subsidies) -->
            <div id="car_id_field" class="form-group hidden">
                <label for="car_id">Car ID:</label>
                <input type="text" name="car_id" id="car_id" placeholder="Enter your Car ID">
            </div>

            <!-- Document Upload (Visible for certain subsidies) -->
            <div id="document_upload_field" class="form-group hidden">
                <label for="document">Upload Document:</label>
                <input type="file" name="document" id="document">
            </div>

            <!-- Submit Button -->
            <button type="submit"><i class="fas fa-check"></i> Submit Application</button>
        </form>
    </div>

</body>
</html>
