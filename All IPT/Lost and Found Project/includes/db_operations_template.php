<?php
/**
 * Lost and Found CMS - Database Operations Template
 * 
 * This is a template file demonstrating common database operations for the Lost and Found system.
 * Use these patterns as a reference when implementing new database functionality.
 */

// Include database connection
require_once 'db_connect.php';

/**
 * ===============================================================
 * SECTION 1: DATABASE READ OPERATIONS
 * ===============================================================
 */

/**
 * Example 1: Simple SELECT query
 * 
 * This function demonstrates a basic SELECT query to retrieve 
 * a single record by its ID.
 * 
 * @param string $itemId The ID of the item to retrieve
 * @return array|null The item data as an associative array, or null if not found
 */
function getItemById($itemId) {
    global $conn;
    
    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT * FROM item_table WHERE item_id = ?");
    
    // Bind the parameter
    $stmt->bind_param("s", $itemId);
    
    // Execute the query
    $stmt->execute();
    
    // Get the result
    $result = $stmt->get_result();
    
    // Check if a record was found
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

/**
 * Example 2: SELECT query with multiple conditions
 * 
 * This function demonstrates a more complex SELECT query
 * with multiple conditions and JOINs between tables.
 * 
 * @param string $userId The user ID
 * @param string $status The report status (Lost or Found)
 * @return array The matching transactions
 */
function getUserTransactions($userId, $status) {
    global $conn;
    
    // Prepare the SQL statement with a JOIN
    $sql = "SELECT t.*, i.item_name, i.item_details 
            FROM transaction_table t
            JOIN item_table i ON t.item_id = i.item_id
            WHERE t.pub_id = ? AND t.report_status = ?
            ORDER BY t.date_filed DESC";
            
    $stmt = $conn->prepare($sql);
    
    // Bind multiple parameters
    $stmt->bind_param("ss", $userId, $status);
    
    // Execute the query
    $stmt->execute();
    
    // Get the result
    $result = $stmt->get_result();
    
    // Fetch all matching records
    $transactions = [];
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }
    
    return $transactions;
}

/**
 * ===============================================================
 * SECTION 2: DATABASE WRITE OPERATIONS
 * ===============================================================
 */

/**
 * Example 3: INSERT operation
 * 
 * This function demonstrates how to insert a new record
 * into a database table with prepared statements.
 * 
 * @param array $itemData Associative array of item data
 * @return string|null The inserted item_id on success, null on failure
 */
function insertNewItem($itemData) {
    global $conn;
    
    try {
        // Generate a new ID (as shown in submit_report.php)
        $sqlMaxId = "SELECT MAX(SUBSTRING(item_id, 4)) AS max_id FROM item_table";
        $result = $conn->query($sqlMaxId);
        $row = $result->fetch_assoc();
        $maxId = $row['max_id'];
        
        if ($maxId) {
            $newIdNumber = intval($maxId) + 1;
        } else {
            $newIdNumber = 1;
        }
        
        $itemId = "ITE" . sprintf("%04d", $newIdNumber);
        
        // Prepare the SQL statement
        $sql = "INSERT INTO item_table (
                    item_id, item_name, item_details, categ_id, 
                    loc_found_id, loc_stored_id, item_image
                ) VALUES (?, ?, ?, ?, ?, ?, ?)";
                
        $stmt = $conn->prepare($sql);
        
        // Bind the parameters
        $stmt->bind_param(
            "sssssss", 
            $itemId,
            $itemData['name'],
            $itemData['details'],
            $itemData['category'],
            $itemData['found_location'],
            $itemData['stored_location'],
            $itemData['image_path']
        );
        
        // Execute the query
        $stmt->execute();
        
        // Check if the insert was successful
        if ($stmt->affected_rows > 0) {
            return $itemId;
        } else {
            return null;
        }
    } catch (Exception $e) {
        // Log error or handle exception
        error_log("Error inserting item: " . $e->getMessage());
        return null;
    }
}

/**
 * Example 4: UPDATE operation
 * 
 * This function demonstrates how to update an existing record
 * in a database table with prepared statements.
 * 
 * @param string $transactionId The ID of the transaction to update
 * @param string $newStatus The new status to set
 * @return bool True on success, false on failure
 */
function updateTransactionStatus($transactionId, $newStatus) {
    global $conn;
    
    try {
        // Prepare the SQL statement
        $sql = "UPDATE transaction_table 
                SET transaction_status = ? 
                WHERE transaction_id = ?";
                
        $stmt = $conn->prepare($sql);
        
        // Bind the parameters
        $stmt->bind_param("ss", $newStatus, $transactionId);
        
        // Execute the query
        $stmt->execute();
        
        // Check if the update was successful
        if ($stmt->affected_rows > 0) {
            return true;
        } else {
            // No rows were updated (transaction ID might not exist)
            return false;
        }
    } catch (Exception $e) {
        // Log error or handle exception
        error_log("Error updating transaction: " . $e->getMessage());
        return false;
    }
}

/**
 * ===============================================================
 * SECTION 3: TRANSACTIONS AND COMPLEX OPERATIONS
 * ===============================================================
 */

/**
 * Example 5: Database transaction for multiple operations
 * 
 * This function demonstrates how to use transactions to ensure
 * that multiple database operations succeed or fail together.
 * 
 * @param string $userId The user ID
 * @param array $itemData The item data
 * @return bool True on success, false on failure
 */
function createItemAndTransaction($userId, $itemData) {
    global $conn;
    
    try {
        // Start a transaction
        $conn->begin_transaction();
        
        // Insert the item and get its ID
        $itemId = insertNewItem($itemData);
        
        if (!$itemId) {
            // If item insertion failed, roll back and return false
            $conn->rollback();
            return false;
        }
        
        // Generate a transaction ID
        $sqlMaxTransId = "SELECT MAX(SUBSTRING(transaction_id, 7)) AS max_id FROM transaction_table";
        $result = $conn->query($sqlMaxTransId);
        $row = $result->fetch_assoc();
        $maxId = $row['max_id'] ? intval($row['max_id']) + 1 : 1;
        $transactionId = "TRNSC " . sprintf("%04d", $maxId);
        
        // Set up transaction data
        $currentDate = date('Y-m-d');
        $reportStatus = $itemData['report_status'];
        $claimStatus = ($reportStatus === 'Found') ? 'Unclaimed' : null;
        $responseStatus = ($reportStatus === 'Lost') ? 'Pending' : null;
        
        // Insert the transaction
        $sql = "INSERT INTO transaction_table (
                    transaction_id, pub_id, item_id, report_status,
                    claim_status, response_status, transaction_status, date_filed
                ) VALUES (?, ?, ?, ?, ?, ?, 'Yet To Be Resolved', ?)";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sssssss",
            $transactionId,
            $userId,
            $itemId,
            $reportStatus,
            $claimStatus,
            $responseStatus,
            $currentDate
        );
        
        $stmt->execute();
        
        // If we've gotten this far without errors, commit the transaction
        $conn->commit();
        return true;
        
    } catch (Exception $e) {
        // If anything goes wrong, roll back the transaction
        $conn->rollback();
        error_log("Transaction error: " . $e->getMessage());
        return false;
    }
}

/**
 * ===============================================================
 * SAMPLE USAGE EXAMPLES
 * ===============================================================
 */

// Example usage of getItemById
// $item = getItemById("ITE0001");
// if ($item) {
//     echo "Found item: " . $item['item_name'];
// } else {
//     echo "Item not found";
// }

// Example usage of getUserTransactions
// $transactions = getUserTransactions("TX001 X4441", "Found");
// echo "User has " . count($transactions) . " found items";

// Example usage of updateTransactionStatus
// if (updateTransactionStatus("TRNSC 0001", "Resolved")) {
//     echo "Transaction status updated successfully";
// } else {
//     echo "Failed to update transaction status";
// }

// Example usage of createItemAndTransaction
// $itemData = [
//     'name' => 'Smartphone',
//     'details' => 'Black iPhone 12',
//     'category' => 'CAT001',
//     'found_location' => 'LF00-0002',
//     'stored_location' => 'CZIT-1104',
//     'image_path' => null,
//     'report_status' => 'Found'
// ];
// 
// if (createItemAndTransaction("TX001 X4441", $itemData)) {
//     echo "Item and transaction created successfully";
// } else {
//     echo "Failed to create item and transaction";
// }
?> 