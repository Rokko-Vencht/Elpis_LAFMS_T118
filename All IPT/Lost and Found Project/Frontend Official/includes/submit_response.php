<?php
include 'db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

if (!isset($_POST['transaction_id']) || !isset($_POST['foundloc_respo']) || !isset($_POST['storeloc_respo'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$user_id = $_SESSION['user_id'];
$transaction_id = $_POST['transaction_id'];
$foundloc_respo = $_POST['foundloc_respo'];
$storeloc_respo = $_POST['storeloc_respo'];
$other_info = isset($_POST['other_info']) ? $_POST['other_info'] : '';

try {
    // Insert the response
    $stmt = $conn->prepare("INSERT INTO response_table (user_respo, transaction_id, foundloc_respo, storeloc_respo, other_info) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $user_id, $transaction_id, $foundloc_respo, $storeloc_respo, $other_info);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        // Update the transaction table to mark it as responded
        $response_id = $stmt->insert_id;
        $stmt = $conn->prepare("UPDATE transaction_table SET response_status = 'Responded', response_id = ? WHERE transaction_id = ?");
        $stmt->bind_param("is", $response_id, $transaction_id);
        $stmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'Response submitted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to submit response']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} 