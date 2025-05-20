<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

error_log("mark_as_claimed.php called at " . date('Y-m-d H:i:s'));

session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    error_log("User not logged in");
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

// Log the current user ID
error_log("User ID: " . $_SESSION['user_id']);

// Get POST data (JSON)
$input = file_get_contents('php://input');
error_log("Raw input: " . $input);
$data = json_decode($input, true);

// Validate required fields
if (!isset($data['transaction_id'])) {
    error_log("Missing transaction ID");
    echo json_encode(['success' => false, 'error' => 'Missing transaction ID']);
    exit;
}

$transaction_id = $data['transaction_id'];
$user_respo = $_SESSION['user_id']; // Current session user ID

error_log("Transaction ID: " . $transaction_id);
error_log("User response ID: " . $user_respo);

try {
    // Start transaction
    $conn->begin_transaction();

    // First, verify the user exists in user_info
    $check_user = $conn->prepare("SELECT pub_id, first_name, last_name, email FROM user_info WHERE pub_id = ?");
    $check_user->bind_param("s", $user_respo);
    $check_user->execute();
    $user_result = $check_user->get_result();
    
    if ($user_result->num_rows === 0) {
        error_log("User not found in user_info table");
        throw new Exception("User not found in user_info table");
    }

    $claimer_info = $user_result->fetch_assoc();

    // Verify that the transaction exists and is a "Found" item
    $check_transaction = $conn->prepare("SELECT t.transaction_id, t.pub_id, t.report_status, t.claim_status, i.item_name, u.email as publisher_email, u.first_name as publisher_fname, u.last_name as publisher_lname 
                                       FROM transaction_table t 
                                       JOIN item_table i ON t.item_id = i.item_id
                                       JOIN user_info u ON t.pub_id = u.pub_id 
                                       WHERE t.transaction_id = ?");
    $check_transaction->bind_param("s", $transaction_id);
    $check_transaction->execute();
    $transaction_result = $check_transaction->get_result();
    
    if ($transaction_result->num_rows === 0) {
        error_log("Transaction not found");
        throw new Exception("Transaction not found");
    }
    
    $transaction = $transaction_result->fetch_assoc();
    error_log("Transaction data: " . print_r($transaction, true));
    
    // Check that the item is reported as "Found"
    if ($transaction['report_status'] !== 'Found') {
        error_log("This operation is only valid for found items");
        throw new Exception("This operation is only valid for found items");
    }
    
    // Check that the item is not already claimed
    if ($transaction['claim_status'] === 'Claimed') {
        error_log("This item has already been claimed");
        throw new Exception("This item has already been claimed");
    }
    
    // Check that the publisher is not the same as the current user
    if ($transaction['pub_id'] === $user_respo) {
        error_log("User cannot claim their own item");
        throw new Exception("You cannot claim your own item");
    }

    // Update transaction_table to mark the item as claimed
    $stmt = $conn->prepare("UPDATE transaction_table SET claim_status = 'Claimed', user_respo = ? WHERE transaction_id = ?");
    $stmt->bind_param("ss", $user_respo, $transaction_id);
    
    if (!$stmt->execute()) {
        error_log("Error updating transaction: " . $stmt->error);
        throw new Exception("Error updating transaction: " . $stmt->error);
    }

    // Send email notification to the publisher
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'robertjamesnahial@gmail.com';
        $mail->Password = 'fkqt kyyd rhxy qfdc';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('robertjamesnahial@gmail.com', 'LostLink System');
        $mail->addAddress($transaction['publisher_email'], $transaction['publisher_fname'] . ' ' . $transaction['publisher_lname']);

        $mail->isHTML(true);
        $mail->Subject = "Item Claimed Notification - " . $transaction['item_name'];

        // Email body
        $mail->Body = "
            <h2>LostLink: Reported Item Claim Notification</h2>
            <p>Hello {$transaction['publisher_fname']},</p>
            <p>Your item '{$transaction['item_name']}' (Transaction ID: {$transaction_id}) has been claimed by {$claimer_info['first_name']} {$claimer_info['last_name']}.</p>
            <p>Claimer's Details:</p>
            <ul>
                <li>Name: {$claimer_info['first_name']} {$claimer_info['last_name']}</li>
                <li>Email: {$claimer_info['email']}</li>
            </ul>
            <p>Please verify this claim and mark the item as resolved if it has been successfully returned to its rightful owner.</p>
            <p>Best regards,<br>LostLink Team</p>
        ";

        $mail->AltBody = "
            Item Claim Notification\n\n
            Hello {$transaction['publisher_fname']},\n
            Your item '{$transaction['item_name']}' (Transaction ID: {$transaction_id}) has been claimed by {$claimer_info['first_name']} {$claimer_info['last_name']}.\n
            Claimer's Details:\n
            - Name: {$claimer_info['first_name']} {$claimer_info['last_name']}\n
            - Email: {$claimer_info['email']}\n
            Please verify this claim and mark the item as resolved if it has been successfully returned to its rightful owner.\n
            Best regards,\nLostLink Team
        ";

        $mail->send();
        error_log("Email sent successfully to publisher");
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
        // Don't throw exception here, as the claim process should continue even if email fails
    }

    error_log("Transaction updated successfully");

    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Item marked as claimed successfully'
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    error_log("Exception: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

// Close statements
if (isset($check_user)) $check_user->close();
if (isset($check_transaction)) $check_transaction->close();
if (isset($stmt)) $stmt->close();
?> 