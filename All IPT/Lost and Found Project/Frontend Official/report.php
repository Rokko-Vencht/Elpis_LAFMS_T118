<?php
include 'header.php';
include '../includes/db.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoundIt: Publish an Item</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="view.css">
    <link rel="stylesheet" href="css/report.css">
    
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
                    <input type="search" name="search" placeholder="Search for an item...">
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
                        <li>
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
                        <li class="active">
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
        
        
    <div id="main-cont">
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error']; ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success']; ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div class="main-cont-header">
            <h1>Report an Item</h1>
            <p>Report a lost or found item. It might help you seek your item or find it's owner</p>
        </div>

        <form id="addItemForm" action="report_validate.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Report Status:</label>
                <div class="radio-options">
                    <label><input type="radio" id="reportStatusLost" name="reportStatus" value="Lost" required> Lost</label>
                    <label><input type="radio" id="reportStatusFound" name="reportStatus" value="Found" required> Found</label>
                </div>
            </div>
            <div class="form-group">
                <label for="reportItemName">Item Name:</label>
                <input type="text" id="reportItemName" name="reportItemName" maxlength="30" placeholder="30 Maximum Characters" required>
            </div>
            <div class="form-group">
                <label for="reportItemCategory">Category:</label>
                <select name="reportItemCategory" id="reportItemCategory" required>
                    <option value="" disabled selected>Select Category</option>
                    <option value="CAT001">Electronics</option>
                    <option value="CAT002">Sports/Recreation</option>
                    <option value="CAT003">Documents</option>
                    <option value="CAT004">Personal Care/Items</option>
                    <option value="CAT005">Education</option>
                    <option value="CAT006">Academe</option>
                    <option value="CAT007">Jewelry</option>
                    <option value="CAT008">Cash</option>
                    <option value="CAT009">Perishables (Food and Items)</option>
                    <option value="CAT010">Furnitures</option>
                    <option value="CAT011">Others</option>
                </select>
            </div>
            <div class="form-group">
                <label for="reportItemDetails">Item Details:</label>
                <textarea id="reportItemDetails" name="reportItemDetails" rows="4" maxlength="300" placeholder="Enter details here..."  style="font-family:Arial, Helvetica, sans-serif;"></textarea>
            </div>
            <div class="form-group">
                <label for="reportItemImage">Upload Photo (Optional):</label>
                <input type="file" id="reportItemImage" name="reportItemImage" accept="image/*">
            </div>
            <!-- Lost fields -->
            <div id="lostItemFields" class="status-specific-fields" style="display:none;">
                <div class="form-group">
                    <label for="lostLocationLastSeen">Location Last Seen:</label>
                    <select name="lostLocationLastSeen" id="lostLocationLastSeen">
                        <option value="" disabled selected>Select Location</option>
                        <option value="LF00-0001">ESL</option>
                        <option value="LF00-0002">Library</option>
                        <option value="LF00-0003">Canteen</option>
                        <option value="LF00-0004">Gym</option>
                        <option value="LF00-0005">Auditorium</option>
                        <option value="LF00-0006">CPAG  Bldg.</option>
                        <option value="LF00-0007">COB Bldg.</option>
                        <option value="LF00-0008">COL  Bldg.</option>
                        <option value="LF00-0009">COED  Bldg.</option>
                        <option value="LF00-0010">CON  Bldg.</option>
                        <option value="LF00-0011">Computer Laboratory</option>
                        <option value="LF00-0012">CSDT</option>
                    </select>
                </div>
            </div>
            <!-- Found fields -->
            <div id="foundItemFields" class="status-specific-fields" style="display:none;">
                <div class="form-group">
                    <label for="foundLocationFound">Location Found:</label>
                    <select name="foundLocationFound" id="foundLocationFound">
                        <option value="" disabled selected>Select Location</option>
                        <option value="LF00-0001">ESL</option>
                        <option value="LF00-0002">Library</option>
                        <option value="LF00-0003">Canteen</option>
                        <option value="LF00-0004">Gym</option>
                        <option value="LF00-0005">Auditorium</option>
                        <option value="LF00-0006">CPAG  Bldg.</option>
                        <option value="LF00-0007">COB Bldg.</option>
                        <option value="LF00-0008">COL  Bldg.</option>
                        <option value="LF00-0009">COED  Bldg.</option>
                        <option value="LF00-0010">CON  Bldg.</option>
                        <option value="LF00-0011">Computer Laboratory</option>
                        <option value="LF00-0012">CSDT</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="foundLocationStored">Item Stored Location:</label>
                    <select name="foundLocationStored" id="foundLocationStored">
                        <option value="" disabled selected>Select Location</option>
                        <option value="CZIT-1100">BPED Society Office</option>
                        <option value="CZIT-1101">CPAG Faculty Office</option>
                        <option value="CZIT-1102">CPAG Society Office</option>
                        <option value="CZIT-1103">CON SBO Office</option>
                        <option value="CZIT-1104">COT SBO Office</option>
                        <option value="CZIT-1105">CAS SBO Office</option>
                        <option value="CZIT-1106">COB SBO Office</option>
                        <option value="CZIT-1107">CON SBO Office</option>
                        <option value="CZIT-1108">COE SBO Office</option>
                        <option value="CZIT-1109">CAS Society Office</option>
                        <option value="CZIT-1110">COB Society Office</option>
                        <option value="CZIT-1111">COT Society Office</option>
                        <option value="CZIT-1112">COT Society Office</option>
                        <option value="CZIT-1113">Finance</option>
                        <option value="CZIT-1114">Main Entrance Guard</option>
                        <option value="CZIT-1115">Exit Guard</option>
                        <option value="CZIT-1116">Dormitory Guard</option>
                    </select>
                </div>
            </div>
            <div class="button-container" style="margin-top: 5px;">
                <button type="submit" class="submit-button" id="addItemSubmit">Submit Report</button>
            </div>
        </form>
    </div>

    <script>
        // Show/hide lost/found fields
        const statusLostRadio = document.getElementById('reportStatusLost');
        const statusFoundRadio = document.getElementById('reportStatusFound');
        const lostFieldsDiv = document.getElementById('lostItemFields');
        const foundFieldsDiv = document.getElementById('foundItemFields');

        function updateFormVisibility() {
            const isLost = statusLostRadio.checked;
            const isFound = statusFoundRadio.checked;
            lostFieldsDiv.style.display = isLost ? 'block' : 'none';
            foundFieldsDiv.style.display = isFound ? 'block' : 'none';
            // Required logic
            document.getElementById('lostLocationLastSeen').required = isLost;
            document.getElementById('foundLocationFound').required = isFound;
            document.getElementById('foundLocationStored').required = isFound;
        }
        statusLostRadio.addEventListener('change', updateFormVisibility);
        statusFoundRadio.addEventListener('change', updateFormVisibility);
        // On load, ensure correct fields are shown
        window.addEventListener('DOMContentLoaded', updateFormVisibility);

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
            
            console.log('Nav toggle:', navToggle);
            console.log('Sidebar:', sidebar);
            console.log('Main content:', mainContent);
            
            navToggle.addEventListener('click', function() {
                console.log('Toggle clicked');
                sidebar.classList.toggle('sb-closed');
                mainContent.classList.toggle('expanded');
                console.log('Sidebar classes:', sidebar.classList);
                console.log('Main content classes:', mainContent.classList);
            });
        });
    </script>
</body>
</html>