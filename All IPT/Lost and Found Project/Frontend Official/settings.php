<?php
include 'header.php';
include '../includes/db.php';

// The header.php already initializes these variables:
// $username = $_SESSION['username'];
// $firstName = $_SESSION['first_name'];
// $lastName = $_SESSION['last_name'];
// $fullName = $firstName . ' ' . $lastName;
// $email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
// $contactNum = isset($_SESSION['contact_num']) ? $_SESSION['contact_num'] : '';


if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $userQuery = "SELECT * FROM user_info WHERE pub_id = ?";
    $stmt = $conn->prepare($userQuery);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();
    
    if ($userData) {
        $firstName = $userData['first_name'];
        $lastName = $userData['last_name'];
        $email = $userData['email'];
        $contactNum = $userData['contact_num'];
        $username = $userData['user_name'];
        
        $fullName = $firstName . ' ' . $lastName;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $newFirstName = $_POST['first_name'];
    $newLastName = $_POST['last_name'];
    $newEmail = $_POST['email'];
    $newContactNum = $_POST['contact_num'];
    

    $userId = $_SESSION['user_id']; 
    
    if (empty($userId)) {
        $error = "Error: User ID is missing. Please log in again.";
    } 
    else {


        $checkUserQuery = "SELECT COUNT(*) as count FROM user_info WHERE pub_id = ?";
        $checkUserStmt = $conn->prepare($checkUserQuery);
        $checkUserStmt->bind_param("s", $userId);
        $checkUserStmt->execute();
        $checkResult = $checkUserStmt->get_result();
        $userCount = $checkResult->fetch_assoc()['count'];
        
        if ($userCount != 1) {
            $error = "Error: Could not find your user account.";
        } else {
            // Debug info
            error_log("Update Profile - User ID: " . $userId);
            error_log("Update Profile - New Email: " . $newEmail);
            
            // Proceed with the update now that we've verified the user exists
            $updateQuery = "UPDATE user_info SET first_name = ?, last_name = ?, email = ?, contact_num = ? WHERE pub_id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            
            // Ensure correct parameter binding
            $updateStmt->bind_param("sssss", $newFirstName, $newLastName, $newEmail, $newContactNum, $userId);
            
            if ($updateStmt->execute()) {
                // Always show success notice, even if no rows were changed
                $_SESSION['first_name'] = $newFirstName;
                $_SESSION['last_name'] = $newLastName;
                $_SESSION['email'] = $newEmail;
                $_SESSION['contact_num'] = $newContactNum;
                $_SESSION['success'] = "Profile information updated successfully!";
                header("Location: settings.php");
                exit();
            } else {
                $error = "Error updating profile: " . $conn->error;
            }
        }
    }
}

// Process password update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $currentPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];
    
    // Store the user ID in a variable and check that it's not empty
    $userId = $_SESSION['user_id']; 
    
    // Server-side password validation - only check minimum length
    if (strlen($newPassword) < 5) {
        $passwordError = "Password must be at least 5 characters long";
    } else if (empty($userId)) {
        $passwordError = "Error: User ID is missing. Please log in again.";
    } else {
        // First, verify that the user exists in the database
        $checkUserQuery = "SELECT password FROM user_info WHERE pub_id = ?";
        $checkUserStmt = $conn->prepare($checkUserQuery);
        $checkUserStmt->bind_param("s", $userId);
        $checkUserStmt->execute();
        $checkResult = $checkUserStmt->get_result();
        
        if ($checkResult->num_rows != 1) {
            $passwordError = "Error: Could not find your user account.";
        } else {
            $userData = $checkResult->fetch_assoc();
            
            // Debug info
            error_log("Update Password - User ID: " . $userId);
            
            // Verify current password
            if (password_verify($currentPassword, $userData['password'])) {
                if ($newPassword === $confirmPassword) {
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    
                    $updatePasswordQuery = "UPDATE user_info SET password = ? WHERE pub_id = ?";
                    $updatePasswordStmt = $conn->prepare($updatePasswordQuery);
                    $updatePasswordStmt->bind_param("ss", $hashedPassword, $userId);
                    
                    if ($updatePasswordStmt->execute()) {
                        // Verify that only one row was affected
                        if ($updatePasswordStmt->affected_rows == 1) {
                            $_SESSION['success'] = "Password updated successfully!";
                            header("Location: settings.php");
                            exit();
                        } else {
                            $passwordError = "Error: Update did not modify any records. Please try again.";
                        }
                    } else {
                        $passwordError = "Error updating password: " . $conn->error;
                    }
                } else {
                    $passwordError = "New passwords do not match.";
                }
            } else {
                $passwordError = "Current password is incorrect.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoundIt: Settings</title>
    <link rel="stylesheet" href="index.css">
        
    <style>
        /* Settings page specific styling */
        .main-cont-header {
            margin-bottom: 30px;
            border-bottom: 1px solid #e1e8ed;
            padding-bottom: 15px;
        }
        
        .main-cont-header h1 {
            color: #123458;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .main-cont-header p {
            color: #657786;
            font-size: 16px;
        }
        
        .settings-section {
            margin-bottom: 20px;
            background-color: white;
            border-radius: 8px;
            padding: 20px;
        }
        
        .settings-section h2 {
            color: #14171a;
            font-size: 20px;
            margin-top: 0;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e1e8ed;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #14171a;
        }
        
        input, select {
            width: 100%;
            padding: 12px;
            margin-bottom: 5px;
            box-sizing: border-box;
            border: 1px solid #e1e8ed;
            border-radius: 6px;
            font-size: 15px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        
        input:focus, select:focus {
            border-color: #123458;
            box-shadow: 0 0 0 2px rgba(18, 52, 88, 0.2);
            outline: none;
        }
        
        .btn {
            padding: 12px 20px;
            background-color: #123458;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #0c2440;
        }
        
        .form-hint {
            font-size: 13px;
            color: #657786;
            margin-top: 5px;
        }
        
        .alert {
            margin-bottom: 25px;
            padding: 15px;
            border-radius: 6px;
        }
        
        .alert-success {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }
        
        .alert-danger {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }
        
        .logout-description {
            color: #657786;
            margin-bottom: 20px;
        }
        
        .logout-button-container {
            display: flex;
            justify-content: flex-start;
        }
        
        .logout-btn {
            display: flex;
            align-items: center;
            background-color: #e74c3c;
            transition: background-color 0.3s;
            padding: 12px 20px;
        }
        
        .logout-btn:hover {
            background-color: #c0392b;
        }
        
        .logout-icon {
            width: 18px;
            height: 18px;
            margin-right: 10px;
            fill: white;
        }
    </style>

</head>
<body>
    <div id="pge-cont">
        <div id="topbar">

            <div class="top-left">
                <button id="nav-toggle"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M0 96C0 78.3 14.3 64 32 64l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 128C14.3 128 0 113.7 0 96zM0 256c0-17.7 14.3-32 32-32l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 288c-17.7 0-32-14.3-32-32zM448 416c0 17.7-14.3 32-32 32L32 448c-17.7 0-32-14.3-32-32s14.3-32 32-32l384 0c17.7 0 32 14.3 32 32z"/></svg></button>
                <a href="index.php" style="text-decoration: none;"> 
                    <img src="img/lostlink_logo.png" class="logo" alt="">
                    <p>Lostlink</p>
                </a>

            </div>
            
            
            <div class="srch-cont">
                <form action="" class="srch-grp">
                    <i class="fas fa-search"></i>
                    <input type="search" placeholder="Search for an item...">
                    <button type="submit"> 
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/></svg>
                    </button>
                </form>
            </div>

            
            <div class="top-right">
                
                <div class="user-info">
                    <p>Welcome, <?php echo htmlspecialchars($fullName); ?></p>
                </div>
                <img src="img/man.avif" alt="">
                
            </div>
                

        </div>

        <div id="bttm-cont">
            <div id="sb" class="sb-open">
                <ul>
                    <a href="index.php">
                        <li>
                            <svg class="sb-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zm64 64l0 256 160 0 0-256L64 160zm384 0l-160 0 0 256 160 0 0-256z"/></svg>
                            <p>Dashboard</p>
                        </li>
                    </a>
                    <a href="view.php">
                        <li>
                            <svg class="sb-icon"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z"/></svg>
                            <p>View Items</p>
                        </li>
                    </a>
                    <a href="archives.php">
                        <li class="">
                            <svg class="sb-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M163.9 136.9c-29.4-29.8-29.4-78.2 0-108s77-29.8 106.4 0l17.7 18 17.7-18c29.4-29.8 77-29.8 106.4 0s29.4 78.2 0 108L310.5 240.1c-6.2 6.3-14.3 9.4-22.5 9.4s-16.3-3.1-22.5-9.4L163.9 136.9zM568.2 336.3c13.1 17.8 9.3 42.8-8.5 55.9L433.1 485.5c-23.4 17.2-51.6 26.5-80.7 26.5L192 512 32 512c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l36.8 0 44.9-36c22.7-18.2 50.9-28 80-28l78.3 0 16 0 64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0-16 0c-8.8 0-16 7.2-16 16s7.2 16 16 16l120.6 0 119.7-88.2c17.8-13.1 42.8-9.3 55.9 8.5zM193.6 384c0 0 0 0 0 0l-.9 0c.3 0 .6 0 .9 0z"/></svg>
                            <p>Reunited Items</p>
                        </li>
                    </a>
                    <a href="report.php">
                        <li>
                        <svg class="sb-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160L0 416c0 53 43 96 96 96l256 0c53 0 96-43 96-96l0-96c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 96c0 17.7-14.3 32-32 32L96 448c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l96 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 64z"/></svg>
                            <p>Report an Item</p>
                        </li>
                    </a>
                    <a href="publish.php">
                        <li>
                        <svg class="sb-icon"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M0 64C0 28.7 28.7 0 64 0L224 0l0 128c0 17.7 14.3 32 32 32l128 0 0 125.7-86.8 86.8c-10.3 10.3-17.5 23.1-21 37.2l-18.7 74.9c-2.3 9.2-1.8 18.8 1.3 27.5L64 512c-35.3 0-64-28.7-64-64L0 64zm384 64l-128 0L256 0 384 128zM549.8 235.7l14.4 14.4c15.6 15.6 15.6 40.9 0 56.6l-29.4 29.4-71-71 29.4-29.4c15.6-15.6 40.9-15.6 56.6 0zM311.9 417L441.1 287.8l71 71L382.9 487.9c-4.1 4.1-9.2 7-14.9 8.4l-60.1 15c-5.5 1.4-11.2-.2-15.2-4.2s-5.6-9.7-4.2-15.2l15-60.1c1.4-5.6 4.3-10.8 8.4-14.9z"/></svg>                
                        <p>My Publishes</p>
                        </li>

                    </a>
                    <a href="settings.php">
                        <li class="active">
                            <svg class="sb-icon"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M495.9 166.6c3.2 8.7 .5 18.4-6.4 24.6l-43.3 39.4c1.1 8.3 1.7 16.8 1.7 25.4s-.6 17.1-1.7 25.4l43.3 39.4c6.9 6.2 9.6 15.9 6.4 24.6c-4.4 11.9-9.7 23.3-15.8 34.3l-4.7 8.1c-6.6 11-14 21.4-22.1 31.2c-5.9 7.2-15.7 9.6-24.5 6.8l-55.7-17.7c-13.4 10.3-28.2 18.9-44 25.4l-12.5 57.1c-2 9.1-9 16.3-18.2 17.8c-13.8 2.3-28 3.5-42.5 3.5s-28.7-1.2-42.5-3.5c-9.2-1.5-16.2-8.7-18.2-17.8l-12.5-57.1c-15.8-6.5-30.6-15.1-44-25.4L83.1 425.9c-8.8 2.8-18.6 .3-24.5-6.8c-8.1-9.8-15.5-20.2-22.1-31.2l-4.7-8.1c-6.1-11-11.4-22.4-15.8-34.3c-3.2-8.7-.5-18.4 6.4-24.6l43.3-39.4C64.6 273.1 64 264.6 64 256s.6-17.1 1.7-25.4L22.4 191.2c-6.9-6.2-9.6-15.9-6.4-24.6c4.4-11.9 9.7-23.3 15.8-34.3l4.7-8.1c6.6-11 14-21.4 22.1-31.2c5.9-7.2 15.7-9.6 24.5-6.8l55.7 17.7c13.4-10.3 28.2-18.9 44-25.4l12.5-57.1c2-9.1 9-16.3 18.2-17.8C227.3 1.2 241.5 0 256 0s28.7 1.2 42.5 3.5c9.2 1.5 16.2 8.7 18.2 17.8l12.5 57.1c15.8 6.5 30.6 15.1 44 25.4l55.7-17.7c8.8-2.8 18.6-.3 24.5 6.8c8.1 9.8 15.5 20.2 22.1 31.2l4.7 8.1c6.1 11 11.4 22.4 15.8 34.3zM256 336a80 80 0 1 0 0-160 80 80 0 1 0 0 160z"/></svg>
                            <p>Settings</p>
                        </li>
                    </a>


                    <a href="logout.php" onclick="return confirmLogout(event)">
                        <li>
                            <svg class="sb-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"/></svg>
                            <p>Logout</p>

                        </li>
                    </a>
                    
                    
                </ul>

            </div>
            <div id="main-cont">
                <div class="main-content-wrapper">
                    <div class="main-cont-header">
                        <h1>Account Settings</h1>
                        <p>Manage your account information and preferences</p>
                    </div>
                    
                    <!-- SESSION NOTICE DIV: Shows after saving personal info -->
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success" id="session-notice">
                            <?= $_SESSION['success']; ?>
                            <?php unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="settings-section">
                        <h2>Profile Information</h2>
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger">
                                <?= $error; ?>
                            </div>
                        <?php endif; ?>
                        <form id="profileForm" method="POST" action="settings.php">
                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <input type="text" id="first_name" name="first_name" placeholder="Enter your first name" value="<?php echo htmlspecialchars($firstName); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <input type="text" id="last_name" name="last_name" placeholder="Enter your last name" value="<?php echo htmlspecialchars($lastName); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" id="username" name="username" placeholder="Your username" value="<?php echo htmlspecialchars($username); ?>" readonly>
                                <div class="form-hint">Username cannot be changed</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($email); ?>" required>
                                <div class="form-hint">We'll use this email for notifications and account recovery</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="contact_num">Contact Number</label>
                                <input type="tel" id="contact_num" name="contact_num" placeholder="Enter your contact number" value="<?php echo htmlspecialchars($contactNum); ?>">
                                <div class="form-hint">Your phone number for verification purposes</div>
                            </div>
                            
                            <button class="btn" type="submit" name="update_profile">Save Changes</button>
                        </form>
                    </div>
                    
                    <div class="settings-section">
                        <h2>Security</h2>
                        <?php if (isset($passwordError)): ?>
                            <div class="alert alert-danger">
                                <?= $passwordError; ?>
                            </div>
                        <?php endif; ?>
                        <form id="passwordForm" method="POST" action="settings.php">
                            <div class="form-group">
                                <label for="oldPassword">Current Password</label>
                                <input type="password" id="oldPassword" name="oldPassword" placeholder="Enter your current password" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="newPassword">New Password</label>
                                <input type="password" id="newPassword" name="newPassword" placeholder="Enter new password" required>
                                <div class="form-hint">Password must be at least 5 characters long</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="confirmPassword">Confirm New Password</label>
                                <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm your new password" required>
                            </div>
                            
                            <button class="btn" type="submit" name="update_password">Update Password</button>
                        </form>
                    </div>
                    
                    <div class="settings-section">
                        <h2>Logout</h2>
                        <p class="logout-description">Click the button below to log out of your account.</p>
                        <div class="logout-button-container">
                            <a href="logout.php" class="btn logout-btn" onclick="return confirmLogout(event)" style="text-decoration: none;">
                                <svg class="logout-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                    <path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"/>
                                </svg>
                                Logout
                            </a>
                        </div>
                    </div>
                    
                    <script>
                    // Basic password validation on form submission
                    document.getElementById('passwordForm').addEventListener('submit', function(event) {
                        const newPassword = document.getElementById('newPassword').value;
                        const confirmPassword = document.getElementById('confirmPassword').value;
                        
                        // Check if password is long enough
                        if (newPassword.length < 5) {
                            event.preventDefault();
                            alert('Password must be at least 5 characters long.');
                            return;
                        }
                        
                        // Check if passwords match
                        if (newPassword !== confirmPassword) {
                            event.preventDefault();
                            alert('New passwords do not match.');
                            return;
                        }
                    });
                    </script>
                </div>
            </div>

        </div>

    </div>
    
    <script>
        function confirmLogout(event) {
            if (!confirm('Are you sure you want to logout?')) {
                event.preventDefault();
                return false;
            }
            return true;
        }

        // Navigation Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const navToggle = document.getElementById('nav-toggle');
            const sidebar = document.getElementById('sb');
            const mainContent = document.getElementById('main-cont');
            
            navToggle.addEventListener('click', function() {
                sidebar.classList.toggle('sb-closed');
                mainContent.classList.toggle('expanded');
            });
        });
    </script>
</body>
</html>