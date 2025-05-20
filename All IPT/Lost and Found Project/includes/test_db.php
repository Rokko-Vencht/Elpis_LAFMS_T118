<?php
/**
 * Lost and Found CMS - Database Connection Test
 * 
 * This script tests the database connection and verifies that all required tables 
 * for the Lost and Found system exist and are accessible. It will:
 * 1. Test connection to the database
 * 2. Check if the user_info table exists and count its records
 * 3. Check if the item_table exists and count its records
 * 4. Check if the transaction_table exists and count its records
 * 
 * This is a diagnostic tool to help troubleshoot database issues.
 */

// Include the database connection file
require_once 'db_connect.php';

/**
 * STEP 1: TEST DATABASE CONNECTION
 * 
 * Check if the connection to the database was successful.
 * If not, display error message and stop execution.
 */
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connection successful!";
    
    /**
     * STEP 2: TEST USER_INFO TABLE
     * 
     * Verify that the user_info table exists and is accessible.
     * Count the number of records to ensure it contains data.
     */
    $result = $conn->query("SELECT COUNT(*) as count FROM user_info");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<br>Number of users in database: " . $row['count'];
    } else {
        echo "<br>Error testing user_info table: " . $conn->error;
    }
    
    /**
     * STEP 3: TEST ITEM_TABLE
     * 
     * Verify that the item_table exists and is accessible.
     * Count the number of records to ensure it contains data.
     */
    $result = $conn->query("SELECT COUNT(*) as count FROM item_table");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<br>Number of items in database: " . $row['count'];
    } else {
        echo "<br>Error testing item_table: " . $conn->error;
    }
    
    /**
     * STEP 4: TEST TRANSACTION_TABLE
     * 
     * Verify that the transaction_table exists and is accessible.
     * Count the number of records to ensure it contains data.
     */
    $result = $conn->query("SELECT COUNT(*) as count FROM transaction_table");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<br>Number of transactions in database: " . $row['count'];
    } else {
        echo "<br>Error testing transaction_table: " . $conn->error;
    }
}

/**
 * STEP 5: CLEAN UP
 * 
 * Close the database connection to free up resources.
 */
$conn->close();
?> 