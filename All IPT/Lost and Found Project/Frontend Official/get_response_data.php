<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include '../includes/db.php';

if (!isset($_GET['transaction_id'])) {
    echo json_encode(['success' => false, 'error' => 'Transaction ID is required']);
    exit;
}

$transaction_id = $_GET['transaction_id'];

try {
    // Get response data for the transaction
    $stmt = $conn->prepare("
        SELECT r.response_id, r.foundloc_respo, r.storeloc_respo, r.other_info, 
               u.first_name, u.last_name 
        FROM transaction_table t
        JOIN response_table r ON t.response_id = r.response_id
        LEFT JOIN user_info u ON t.user_respo = u.pub_id
        WHERE t.transaction_id = ?
    ");
    
    $stmt->bind_param("s", $transaction_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $response = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'response' => [
                'response_id' => $response['response_id'],
                'foundloc_respo' => $response['foundloc_respo'],
                'storeloc_respo' => $response['storeloc_respo'],
                'other_info' => $response['other_info'],
                'user_respo' => $response['first_name'] . ' ' . $response['last_name']
            ]
        ]);
    } else {
        echo json_encode(['success' => true, 'response' => null]);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

if (isset($stmt)) $stmt->close();
?> 