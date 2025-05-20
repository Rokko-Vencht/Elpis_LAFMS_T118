<?php
include 'header.php';
include '../includes/db.php'
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoundIt: View Lost Items</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="css/view.css">
    <link rel="stylesheet" href="css/modals.css">
    <script>
        // Make sure the currentUserId is properly defined
        window.currentUserId = "<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>";
        console.log("Initial currentUserId from PHP:", window.currentUserId);
    </script>
    <script src="js/view.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
                
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

        /* Modal margin styles specific to view.php */
        .lf-modal-container .lf-modal,
        .lf-modal-container-f .lf-modal,
        .lf-modal-container-response .lf-modal,
        .lf-modal-container-view-response .lf-modal {
            margin-top: 2px;
            margin-bottom: 2px;
        }

        /* Sidebar Toggle Styles */
        #sb {
            transition: transform 0.3s ease, width 0.3s ease, margin 0.3s ease, opacity 0.3s ease;
        }

        #sb.sb-closed {
            transform: translateX(-100%);
            width: 0;
            margin: 0;
            padding: 0;
            opacity: 0;
            pointer-events: none;
        }

        #main-cont {
            transition: all 0.3s ease;
        }

        #main-cont.expanded {
            margin-left: 30px !important;
        }

        @media (max-width: 768px) {
            #sb.sb-closed {
                transform: translateX(-100%);
                width: 0;
                margin: 0;
            }
            
            #main-cont.expanded {
                margin-left: 20px !important;
            }
        }
    </style>
</head>
<body>
    <div id="pge-cont">
        <!-- Add loading overlay -->
        <div id="loading-overlay" class="loading-overlay">
            <div class="loading-spinner"></div>
        </div>
        
        <div id="topbar">
            <div class="top-left">
                <button id="nav-toggle"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M0 96C0 78.3 14.3 64 32 64l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 128C14.3 128 0 113.7 0 96zM0 256c0-17.7 14.3-32 32-32l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 288c-17.7 0-32-14.3-32-32zM448 416c0 17.7-14.3 32-32 32L32 448c-17.7 0-32-14.3-32-32s14.3-32 32-32l384 0c17.7 0 32 14.3 32 32z"/></svg></button>
                <a href="index.php" style="text-decoration: none;"> 
                    <img src="img/lostlink_logo.png" class="logo" alt="">
                    <p>Lostlink</p>
                </a>
            </div>
            
            <div class="srch-cont">
                <form action="view.php" method="GET" class="srch-grp">
                    <input type="search" name="search" placeholder="Search for an item..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit"> 
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/></svg>
                    </button>
                </form>
            </div>

            <div class="top-right">
                <div class="user-info">
                    <p>Welcome, <?php echo $fullName; ?></p>
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
                        <li class="active">
                            <svg class="sb-icon"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z"/></svg>
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
                            <svg class="sb-icon"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160L0 416c0 53 43 96 96 96l256 0c53 0 96-43 96-96l0-96c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 96c0 17.7-14.3 32-32 32L96 448c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l96 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 64z"/></svg>
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
                        <li>
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

            <!-- THIS IS WHERE ALL OF THE ITEM CARDS ARE GOING TO BE DISPLAYED -->
            <div id="main-cont">
                        
                <?php if (!empty($searchTerm)): ?>
                <div class="search-results-header">
                    <p>Search results for: <strong><?php echo htmlspecialchars($_GET['search']); ?></strong></p>
                    <a href="view.php" class="clear-search-btn">Clear Search</a>
                </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success" style="background-color: #dff0d8; color: #3c763d; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                            <?= $_SESSION['success']; ?>
                            <?php unset($_SESSION['success']); ?>
                        </div>
                <?php endif; ?>

                
                <div class="main-cont-header">
                    <h1>View Reported Items</h1>
                    <p>Find and seek all reported and unresolved items.</p>
                </div>
                
                <div class="lf-items-container">
                    
                    <!-- GET TRANSACTION VALUES BY ESTABLISHING VARIABLES WITH THEIR RESPECTIVE TABLES -->
                    <?php
                    // Check if search query exists
                    $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
                    
                    $sql = "SELECT t.*, 
                           i.item_name, 
                           i.item_image, 
                           i.item_details, 
                           c.categ_item AS category_name,
                           lf.loc_found_name AS found_location_name,
                           ls.loc_stored_name AS stored_location_name,
                           pub.first_name AS pub_first_name,
                           pub.last_name AS pub_last_name,
                           res.first_name AS res_first_name,
                           res.last_name AS res_last_name,
                           r.response_id,
                           r.foundloc_respo,
                           r.storeloc_respo,
                           r.other_info
                           FROM transaction_table t 
                           JOIN item_table i ON t.item_id = i.item_id 
                           JOIN category_table c ON i.categ_id = c.categ_id
                           LEFT JOIN loc_found_table lf ON i.loc_found_id = lf.loc_found_id
                           LEFT JOIN loc_stored_table ls ON i.loc_stored_id = ls.loc_stored_id
                           LEFT JOIN user_info pub ON t.pub_id = pub.pub_id
                           LEFT JOIN user_info res ON t.user_respo = res.pub_id
                           LEFT JOIN response_table r ON t.response_id = r.response_id
                           WHERE t.transaction_status != 'Resolved'";
                    
                    // Add search condition if search term is provided
                    if (!empty($searchTerm)) {
                        $searchTerm = $conn->real_escape_string($searchTerm);
                        $searchTermWithWildcards = "%{$searchTerm}%";
                        $sql .= " AND (i.item_name LIKE '{$searchTermWithWildcards}' 
                                OR i.item_details LIKE '{$searchTermWithWildcards}' 
                                OR c.categ_item LIKE '{$searchTermWithWildcards}' 
                                OR lf.loc_found_name LIKE '{$searchTermWithWildcards}' 
                                OR ls.loc_stored_name LIKE '{$searchTermWithWildcards}')";
                    }
                    
                    // Add order by clause
                    $sql .= " ORDER BY t.date_filed DESC";
                    
                    /**CONNECT TO THE SQL */
                    $result = $conn->query($sql);
                    


                    /**LOADOUT CARDS HERE !!!!!!
                     * What were gonna do is create cards for the item-cards
                     * 
                     * 
                    */


                    if ($result->num_rows > 0) {

                        /**LOOPS BACK UNTIL ALL ROWS FROM TRANSACTION TABLE IS TRAVERSED -- YUH */
                        while($row = $result->fetch_assoc()) {
                            $itemImage = !empty($row['item_image']) ? $row['item_image'] : 'uploads/items/EMPTY_IMG.jpg';
                            $modalClass = $row['report_status'] == 'Lost' ? 'lf-open-lost' : 'lf-open-found';
                            ?>

                            <!-- INSERT HTML FORMATTING HERE WHILE ALSO INJECTING ALL OF THE INPUTS -->
                            <div class="lf-item-card" data-transaction-id="<?php echo htmlspecialchars($row['transaction_id']); ?>">
                                <img src="<?php echo htmlspecialchars($itemImage); ?>" alt="">
                                <div class="lf-item-details-container">
                                    <p id="item-name" class="lf-item-name"><?php echo htmlspecialchars($row['item_name']); ?></p>
                                    
                                    <div class="form-group">
                                        <p class="lf-item-detail-head">Report Status:</p>
                                        <p class="lf-item-detail-cont"><?php echo htmlspecialchars($row['report_status']); ?></p>
                                    </div>
                                    
                                    <div class="form-group">
                                        <p class="lf-item-detail-head">Publisher:</p>
                                        <p class="lf-item-detail-cont"><?php echo htmlspecialchars(trim($row['pub_first_name'] . ' ' . $row['pub_last_name'])); ?></p>    
                                    </div>

                                    <div class="form-group">
                                        <p class="lf-item-detail-head">Transaction ID:</p>
                                        <p class="lf-item-detail-cont" id="transaction-id"><?php echo htmlspecialchars($row['transaction_id']); ?></p>
                                    </div>
                                </div>
                                

                                <button class="lf-view-details-btn <?php echo $modalClass; ?>" 
                                        data-transaction-id="<?php echo htmlspecialchars($row['transaction_id']); ?>"
                                        data-item-name="<?php echo htmlspecialchars($row['item_name']); ?>"
                                        data-item-image="<?php echo htmlspecialchars($itemImage); ?>"
                                        data-item-details="<?php echo htmlspecialchars($row['item_details']); ?>"
                                        data-report-status="<?php echo htmlspecialchars($row['report_status']); ?>"
                                        data-category-name="<?php echo htmlspecialchars($row['category_name']); ?>"
                                        data-found-location="<?php echo htmlspecialchars($row['found_location_name']); ?>"
                                        data-stored-location="<?php echo htmlspecialchars($row['stored_location_name']); ?>"
                                        data-date-filed="<?php echo htmlspecialchars($row['date_filed']); ?>"
                                        data-pub-name="<?php echo htmlspecialchars(trim($row['pub_first_name'] . ' ' . $row['pub_last_name'])); ?>"
                                        data-pub-id="<?php echo htmlspecialchars($row['pub_id']); ?>"
                                        data-user-respo-name="<?php echo ($row['res_first_name'] || $row['res_last_name']) ? htmlspecialchars(trim($row['res_first_name'] . ' ' . $row['res_last_name'])) : ''; ?>"
                                        data-claim-status="<?php echo htmlspecialchars($row['claim_status']); ?>"
                                        data-user-respo="<?php echo htmlspecialchars($row['user_respo']); ?>"
                                        data-transaction-status="<?php echo htmlspecialchars($row['transaction_status']); ?>"
                                        data-response-status="<?php echo htmlspecialchars($row['response_status']); ?>"
                                        data-response-id="<?php echo htmlspecialchars($row['response_id']); ?>"
                                        data-foundloc-respo="<?php echo htmlspecialchars($row['foundloc_respo']); ?>"
                                        data-storeloc-respo="<?php echo htmlspecialchars($row['storeloc_respo']); ?>"
                                        data-other-info="<?php echo htmlspecialchars($row['other_info']); ?>"
                                        data-user-respo-name="<?php echo htmlspecialchars($row['res_first_name'] . ' ' . $row['res_last_name']); ?>">
                                    View Details
                                </button>
                            </div>
                            <?php
                        }
                    } else {
                        if (!empty($searchTerm)) {
                            echo '<div class="no-items-found">No items found matching your search. Please try different keywords.</div>';
                        } else {
                            echo '<div class="no-items-found">No items found.</div>';
                        }
                    }
                    ?>
                </div>
                
            </div> 
             <!-- end for main content -->

            <!-- INPUT HERE ALL OF THE MODALS FOR EVERY INTERACTABLES -->
            
            
            <!-- LOST ITEM DETAILS MODAL -->
            <div class="lf-modal-container" id="lf-modal-container">
                <div class="lf-modal"> 
                    <div class="lf-form-group-header">
                        <h1>Item Information</h1>
                        <svg id="lf-close" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg>
                    </div>

                    <hr>
                    
                    <div id="lf-info-control">
                        
                        <div id="lf-photo-cont">
                            <img id="lf-item-holder-img" src="" alt="">
                        </div>
                        
                        <div class="lf-info-details">

                            <div id="lf-form-group">
                                <p id="lf-item-holder-title">Report Status:</p>
                                <p id="lf-item-holder-details" class="lf-report-status"></p>
                            </div>
                            <div id="lf-form-group">
                                <p id="lf-item-holder-title">Item Name:</p>
                                <p id="lf-item-holder-details" class="lf-item-name"></p>
                            </div>
                            <div id="lf-form-group">
                                <p id="lf-item-holder-title">Category:</p>
                                <p id="lf-item-holder-details" class="lf-category-name"></p>
                            </div>
                            <div id="lf-form-group">
                                <p id="lf-item-holder-title">Date Filed:</p>
                                <p id="lf-item-holder-details" class="lf-date-filed"></p>
                            </div>
                            <div id="lf-form-group">
                                <p id="lf-item-holder-title">Last Seen Location:</p>
                                <p id="lf-item-holder-details" class="lf-found-location"></p>
                            </div>
                            <div id="lf-form-group">
                                <p id="lf-item-holder-title">Reported By:</p>
                                <p id="lf-item-holder-details" class="lf-pub-id"></p>
                            </div>
                            <hr>

                        </div>

                        <div class="lf-report-section">
                            <div class="lf-item-holder-title">
                                Response Section
                            </div>

                            <div class="lf-report-section-innie"> 
                                <div class="lf-add-response-btn-cont" style="display:none;">
                                    <button id="lf-add-report" class="lf-add-response-btn">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                        Add Response
                                    </button>
                                </div>
                                <div class="lf-view-response-btn-cont" style="display:none;">
                                    <button id="lf-view-response-btn"
                                    style="
                                            width: 100%;
                                            height: 50px;
                                            background: #fff;
                                            color: black;
                                            border: 1.5px solid rgb(33, 33, 79);
                                            border-radius: 8px;
                                            text-align: center;
                                            padding-left: 20px;
                                            cursor: pointer;
                                            display: flex;
                                            align-items: center;
                                            font-size: 16px;
                                            font-weight: 600;
                                            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
                                            margin: 0;"

                                        onmouseover="this.style.backgroundColor='rgb(33,33,79)';this.style.color='white'; this.querySelector('i').style.color='white';"
                                        onmouseout="this.style.backgroundColor='#fff';this.style.color='black'; this.querySelector('i').style.color='rgb(33,33,79)';"
                                    >
                                        View Response
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="lf-form-group-desc" class="lf-desc-box">
                        <div class="lf-desc-label">Description:</div>
                        <div class="lf-desc-content"></div>
                    </div>

                    <button id="lf-mark-resolved">Mark As Resolved</button>
                </div>
            </div>

            <!-- FOUND ITEM DETAILS MODAL -->
            <div class="lf-modal-container-f" id="lf-modal-container-f">
                <div class="lf-modal"> 
                    <div class="lf-form-group-header">
                        <h1>Item Information</h1>
                        <svg id="lf-close2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg>
                    </div>

                    <hr>
                    
                    <div id="lf-info-control">
                        <div id="lf-photo-cont">
                            <img id="lf-item-holder-img" src="" alt="">
                        </div>
                        
                        <div class="lf-info-details">
                            <div id="lf-form-group">
                                <p id="lf-item-holder-title">Report Status:</p>
                                <p id="lf-item-holder-details" class="lf-report-status"></p>
                            </div>
                            <div id="lf-form-group">
                                <p id="lf-item-holder-title">Item Name:</p>
                                <p id="lf-item-holder-details" class="lf-item-name"></p>
                            </div>
                            <div id="lf-form-group">
                                <p id="lf-item-holder-title">Category:</p>
                                <p id="lf-item-holder-details" class="lf-category-name"></p>
                            </div>
                            <div id="lf-form-group">
                                <p id="lf-item-holder-title">Found Location:</p>
                                <p id="lf-item-holder-details" class="lf-found-location"></p>
                            </div>
                            <div id="lf-form-group">
                                <p id="lf-item-holder-title">Stored Location:</p>
                                <p id="lf-item-holder-details" class="lf-stored-location"></p>
                            </div>
                            <div id="lf-form-group">
                                <p id="lf-item-holder-title">Date Filed:</p>
                                <p id="lf-item-holder-details" class="lf-date-filed"></p>
                            </div>
                            <div id="lf-form-group">
                                <p id="lf-item-holder-title">Reported By:</p>
                                <p id="lf-item-holder-details" class="lf-pub-id"></p>
                            </div>
                            <div id="lf-form-group">
                                <p id="lf-item-holder-title">Claim Status:</p>
                                <p id="lf-item-holder-details" class="lf-claim-status"></p>
                            </div>
                            <div id="lf-form-group">
                                <p id="lf-item-holder-title">Claimed by:</p>
                                <p id="lf-item-holder-details" class="lf-user-respo"></p>
                            </div>
                            <div id="lf-form-group">
                                <p id="lf-item-holder-title">Transaction Status:</p>
                                <p id="lf-item-holder-details" class="lf-transaction-status"></p>
                            </div>
                            <hr>
                        </div>
                    </div>

                    <div id="lf-form-group-desc" class="lf-desc-box">
                        <div class="lf-desc-label">Description:</div>
                        <div class="lf-desc-content"></div>
                    </div>

                    <button id="lf-mark-claimed">Mark As Claimed</button>
                    <button id="lf-mark-resolved">Mark As Resolved</button>
                </div>
            </div>

            <!-- VIEW RESPONSE MODAL -->
            <div class="lf-modal-container-view-response" id="lf-modal-container-view-response">
                <div class="lf-modal"> 
                    <div class="lf-form-group-header">
                        <h1>Response Details</h1>
                        <svg id="lf-close-view-response" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg>
                    </div>

                    <div class="lf-form-scrollable-container">
                        <div class="lf-form-content">
                            <div class="lf-response-header">
                                <div class="lf-response-id-display">Response ID: <span id="view-response-id"></span></div>
                                <div class="lf-response-by-display">Submitted By: <span id="view-response-by"></span></div>
                            </div>

                            <div class="lf-info-control">
                                <div class="lf-info-details">
                                    <p class="lf-output-title">Location Where Item Was Found:</p>
                                    <div class="lf-output-content" id="view-location-found"></div>
                                </div>
                            </div>
                            <div class="lf-info-control">
                                <div class="lf-info-details">
                                    <p class="lf-output-title">Current Storage Location:</p>
                                    <div class="lf-output-content" id="view-storage-location"></div>
                                </div>
                            </div>
                            <div class="lf-info-control">
                                <div class="lf-info-details">
                                    <p class="lf-output-title">Item Description:</p>
                                    <div class="lf-output-content" id="view-item-description"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <!-- VIEW RESPONSE MODAL -->
            <div class="lf-modal-container-view-response" id="lf-modal-container-view-response">
                <div class="lf-modal"> 
                    <div class="lf-form-group-header">
                        <h1>Response Details</h1>
                        <svg id="lf-close-view-response" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg>
                    </div>

                    <div class="lf-form-scrollable-container">
                        <div class="lf-form-content">
                            <div class="lf-response-header">
                                <div class="lf-response-id-display">Response ID: <span id="view-response-id"></span></div>
                                <div class="lf-response-by-display">Submitted By: <span id="view-response-by"></span></div>
                            </div>

                            <div class="lf-info-control">
                                <div class="lf-info-details">
                                    <p class="lf-output-title">Location Where Item Was Found:</p>
                                    <div class="lf-output-content" id="view-location-found"></div>
                                </div>
                            </div>
                            <div class="lf-info-control">
                                <div class="lf-info-details">
                                    <p class="lf-output-title">Current Storage Location:</p>
                                    <div class="lf-output-content" id="view-storage-location"></div>
                                </div>
                            </div>
                            <div class="lf-info-control">
                                <div class="lf-info-details">
                                    <p class="lf-output-title">Item Description:</p>
                                    <div class="lf-output-content" id="view-item-description"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ADD RESPONSE MODAL -->
            <div class="lf-modal-container-response" id="lf-modal-container-response">
                <div class="lf-modal"> 
                    <div class="lf-form-group-header">
                        <h1>Submit a Response</h1>
                        <svg id="lf-close-response" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg>
                    </div>

                    <form action="">
                        <div class="lf-form-scrollable-container">
                            <div class="lf-form-content">
                                <div class="lf-info-control">
                                    <div class="lf-info-details">
                                        <div id="lf-form-group">
                                            <p id="lf-item-holder-title">1. In which location did you saw the item? Perhaps share the details of its whereabouts. <span style="color: red;">*</span></p>
                                            <textarea id="lf-foundloc-respo" class="lf-response-input" name="foundloc-respo" required></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="lf-info-control">
                                    <div class="lf-info-details">
                                        <div id="lf-form-group">
                                            <p id="lf-item-holder-title">2. Where is the item currently stored? If you're not sure, just type <span style="font-weight: bold;">UNKNOWN</span>'. <span style="color: red;">*</span></p>
                                            <textarea id="lf-storeloc-respo" class="lf-response-input" name="storeloc-respo" required></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="lf-info-control">
                                    <div class="lf-info-details">
                                        <div id="lf-form-group">
                                            <p id="lf-item-holder-title">3. Could you please describe the item's attributes/appearance. (Optional)</p>
                                            <textarea id="lf-other-info" class="lf-response-input" name="other-info"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="lf-button-container">
                            <button type="submit" class="lf-submit-response-btn">Submit Response</button>
                        </div>
                    </form>
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