<?php

    session_start();

    require '../includes/db.php';

    /**CHECKS IF IT WAS SUBMITTED */
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        
        /*EXTRACTED SESSION DATA FROM LOGIN-VALIDATE */
        $pub_id = $_SESSION['user_id'];

        /*GET THE DATA FROM THE FORM */
        $report_status = $_POST['reportStatus'];
        $item_name = $_POST['reportItemName'];
        $categ_id  = $_POST['reportItemCategory'];
        $item_details = $_POST['reportItemDetails'];

        /*ESTABLISHING VARIABLES FOR THE LOCATION FOR THE INPUTS VARY FOR THE LOST AND FOUND*/
        $loc_found_id = null;
        $loc_stored_id = null;

        /*TUNNEL THE VALUES FOR SEPARATE REPORT STATUSES */
        if($report_status == 'Lost'){
            $loc_found_id = $_POST['lostLocationLastSeen'];
        }
        else{
            $loc_found_id = $_POST['foundLocationFound'];
            $loc_stored_id = $_POST['foundLocationStored'];
        }

        /*HANDLE IMAGES HERE IF NOT NULL */

        $item_image = null;

        if(isset($_FILES['reportItemImage']) && $_FILES['reportItemImage']['error'] === UPLOAD_ERR_OK){
            
            /**DIRECTORY PATH FOR STORING UPLOADED PHOTOS */
            $upload_dir = 'uploads/items/';

            /**GENERATE DIRECTORY IF NOT FOUND */
            if(!file_exists($upload_dir)){
                mkdir($upload_dir, 0777, true);
            }

            /**GENERATE FILENAME */
            $file_extension = pathinfo($_FILES['reportItemImage']['name'], PATHINFO_EXTENSION); //.JPG .PNG etc.
            $unique_filename = uniqid() .  '.' . $file_extension; //01202 + .PNG
            $upload_path = $upload_dir . $unique_filename; // /uploads/items/122121.JPG

            /**MOVING THE FILE ON THAT DIRECTORY */
            if(move_uploaded_file($_FILES['reportItemImage']['tmp_name'], $upload_path)){
                $item_image = $upload_path;
            }

        }


        try{
            /**INSERTION INSIDE THE ITEM TABLE*/
            $conn->begin_transaction();

            $sqlInsertItem = "INSERT INTO item_table(item_name, item_details, categ_id, loc_found_id, loc_stored_id, item_image)
                            VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmtItem = $conn->prepare($sqlInsertItem);
            $stmtItem->bind_param("ssssss", $item_name, $item_details, $categ_id, $loc_found_id, $loc_stored_id, $item_image);
            $stmtItem->execute();


            /**IDENTIFY THE RECENTLY ADDED ITEM ID */
            $sqlGetItemId = "SELECT item_id FROM item_table 
                            WHERE item_name = ? 
                            AND item_details = ? 
                            AND categ_id = ?
                            ORDER BY item_id DESC LIMIT 1";

            $stmtGetId = $conn->prepare($sqlGetItemId);
            $stmtGetId->bind_param("sss", $item_name, $item_details, $categ_id);
            $stmtGetId->execute();
            
            /**GETS THE ID BY SEARCHING IY */
            $itemIdResult = $stmtGetId->get_result();
            $itemIdRow = $itemIdResult->fetch_assoc();
            $item_id = $itemIdRow['item_id'];

            /**FETCHING THE CURRENT DATE */
            $date_filed = date('Y-m-d');



            /**------------ TRANSACTION STATUS SECTION ------------*/

            $claim_status = ($report_status === 'Found') ? 'Unclaimed' : null; /**SINCE FOUND ITEMS HAS CLAIMING INTERACTABLES */
            $response_status = ($report_status === 'Lost') ? 'Pending' : null; /**SINCE LOST ITEMS HAS RESPONSE PRIVILEGES (COMMENTS IN SOCMED) */
            $transaction_status = 'Yet To Be Resolved'; /**INITIAL VALUE FOR EACH NEW REPORTS */
            
            /**INSERTION OF VALUES TO TRANSACTION TABLE */
            $sqlInsertTransaction = "INSERT INTO transaction_table( 
                                                pub_id, item_id, report_status, claim_status, 
                                                response_status, transaction_status, date_filed)             
                                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            /**NULLIFIED ENTRIES FOR response_id, user_respo, response_status, transaction_date SINCE TRANSACTIONS ON THESE ITEMS HAS YET TO OCCUR*/
            $stmtTransaction=$conn->prepare($sqlInsertTransaction);
            $stmtTransaction->bind_param("sssssss", $pub_id, $item_id, $report_status, $claim_status, 
                                                    $response_status, $transaction_status, $date_filed);
            $stmtTransaction->execute();
            
            $sqlGetTransactionId = "SELECT transaction_id FROM transaction_table 
                                WHERE item_id = ? 
                                ORDER BY transaction_id DESC LIMIT 1";

            $stmtGetTransId = $conn->prepare($sqlGetTransactionId);
            $stmtGetTransId->bind_param("s", $item_id);
            $stmtGetTransId->execute();
            $transactionIdResult = $stmtGetTransId->get_result();
            $transactionIdRow = $transactionIdResult->fetch_assoc();
            $transaction_id = $transactionIdRow['transaction_id'];


            $conn->commit();

            $_SESSION['success'] = "Item reported successfully! Transaction ID: $transaction_id";
            header('Location: report.php');
            exit();
        }
        catch (Exception $e){
            $conn->rollback();

            $_SESSION['success'] = "Error: " . $e->getMessage();
            header('Location: report.php');
            exit();
        };

    } else{
        $_SESSION['success'] = "Inputs Error: Please try again.";
            header('Location: report.php');
            exit();
    }

?>