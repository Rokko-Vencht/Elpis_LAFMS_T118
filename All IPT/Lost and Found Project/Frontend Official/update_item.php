<?php
session_start();
require '../includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'You must be logged in to perform this action']);
    exit;
}

// Get the input data
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['transaction_id']) || !isset($input['item_name']) || !isset($input['category_id'])) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

$transactionId = $input['transaction_id'];
$itemName = $input['item_name'];
$categoryId = $input['category_id'];
$locationId = $input['location_id'] ?? null;
$foundLocationId = $input['found_location_id'] ?? null;
$storedLocationId = $input['stored_location_id'] ?? null;
$reportStatus = $input['report_status'];
$itemDetails = $input['item_details'] ?? null;

// First, check if the user is the publisher of this item
$checkSql = "SELECT pub_id, item_id FROM transaction_table WHERE transaction_id = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("s", $transactionId);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Item not found']);
    exit;
}

$row = $result->fetch_assoc();
$pubId = $row['pub_id'];
$itemId = $row['item_id'];

// Verify the user is the publisher
if ($pubId != $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'error' => 'You do not have permission to edit this item']);
    exit;
}

// Update the item information
$updateItemSql = "UPDATE item_table SET item_name = ?, categ_id = ?";
$params = [$itemName, $categoryId];
$types = "ss";

// Add item details if provided
if ($itemDetails !== null) {
    $updateItemSql .= ", item_details = ?";
    $params[] = $itemDetails;
    $types .= "s";
}

// Add location fields based on report status
if ($reportStatus === 'Lost' && $locationId) {
    $updateItemSql .= ", loc_found_id = ?";
    $params[] = $locationId;
    $types .= "s";
} else if ($reportStatus === 'Found') {
    // For found items, update both found and stored locations
    if ($foundLocationId) {
        $updateItemSql .= ", loc_found_id = ?";
        $params[] = $foundLocationId;
        $types .= "s";
    }
    
    if ($storedLocationId) {
        $updateItemSql .= ", loc_stored_id = ?";
        $params[] = $storedLocationId;
        $types .= "s";
    }
}

$updateItemSql .= " WHERE item_id = ?";
$params[] = $itemId;
$types .= "s";

$updateItemStmt = $conn->prepare($updateItemSql);
$updateItemStmt->bind_param($types, ...$params);
$itemResult = $updateItemStmt->execute();

if (!$itemResult) {
    echo json_encode(['success' => false, 'error' => 'Failed to update item: ' . $conn->error]);
    exit;
}

echo json_encode(['success' => true, 'message' => 'Item updated successfully']);
?> 