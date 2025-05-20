<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

session_start();
include '../includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

// Get POST data (JSON)
$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if (!isset($data['foundloc_respo']) || !isset($data['storeloc_respo']) || !isset($data['transaction_id'])) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

$foundloc_respo = $data['foundloc_respo'];
$storeloc_respo = $data['storeloc_respo'];
$other_info = $data['other_info'] ?? '';
$transaction_id = $data['transaction_id'];
$user_respo = $_SESSION['user_id']; 

try {
    $conn->begin_transaction();

    $check_user = $conn->prepare("SELECT pub_id, first_name, last_name, email FROM user_info WHERE pub_id = ?");
    $check_user->bind_param("s", $user_respo);
    $check_user->execute();
    $user_result = $check_user->get_result();
    
    if ($user_result->num_rows === 0) {

        throw new Exception("User not found in user_info table");
    }

    $responder_info = $user_result->fetch_assoc();


/**FETCH TRANSACTION DETAILS */
    $check_transaction = $conn->prepare("
        SELECT t.transaction_id, t.pub_id, i.item_name, 
               u.email as publisher_email, u.first_name as publisher_fname, u.last_name as publisher_lname
        FROM transaction_table t
        JOIN item_table i ON t.item_id = i.item_id
        JOIN user_info u ON t.pub_id = u.pub_id
        WHERE t.transaction_id = ?
    ");
    $check_transaction->bind_param("s", $transaction_id);
    $check_transaction->execute();
    $transaction_result = $check_transaction->get_result();

    if ($transaction_result->num_rows === 0) {
        throw new Exception("Transaction not found");
    }

    $transaction = $transaction_result->fetch_assoc();


    /**ADD NEW RESPONSE */
    $stmt1 = $conn->prepare("INSERT INTO response_table (foundloc_respo, storeloc_respo, other_info) VALUES (?, ?, ?)");
    $stmt1->bind_param("sss", $foundloc_respo, $storeloc_respo, $other_info);

    
    if (!$stmt1->execute()) {
        throw new Exception("Error inserting response: " . $stmt1->error);
    }
    

    /**GET RECENTLY ADDED RESPONSE ID */
    $get_id = $conn->prepare("SELECT response_id FROM response_table ORDER BY response_id DESC LIMIT 1");
    $get_id->execute();
    $id_result = $get_id->get_result();
    
    if ($id_result->num_rows > 0) {
        $row = $id_result->fetch_assoc();
        $response_id = $row['response_id'];
    } else {
        throw new Exception("Could not retrieve the new response_id");
    }


    /**UPDATING TRANSACTION TABLE */
    $stmt2 = $conn->prepare("UPDATE transaction_table SET user_respo = ?, response_status = 'Responded', response_id = ? WHERE transaction_id = ?");
    $stmt2->bind_param("sss", $user_respo, $response_id, $transaction_id);
    
    if (!$stmt2->execute()) {
        throw new Exception("Error updating transaction: " . $stmt2->error);
    }



    /**SEND EMAIL HERE */
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
        $mail->Subject = "New Response to Your Lost Item - " . $transaction['item_name'];

        // Email body
        $mail->Body = "
            <h2>LostLink: Reported Item Response Notification</h2>
            <p>Hello {$transaction['publisher_fname']},</p>
            <p>Someone has responded to your lost item report for '{$transaction['item_name']}' (Transaction ID: {$transaction_id}).</p>
            <p>Response Details:</p>
            <ul>
                <li>Responder: {$responder_info['first_name']} {$responder_info['last_name']}</li>
                <li>Found Location: {$foundloc_respo}</li>
                <li>Storage Location: {$storeloc_respo}</li>
                " . ($other_info ? "<li>Additional Information: {$other_info}</li>" : "") . "
            </ul>
            <p>You can contact the responder at: {$responder_info['email']}</p>
            <p>Please verify this response and mark the item as resolved if it has been successfully returned to you.</p>
            <p>Best regards,<br>LostLink Team</p>
        ";

        $mail->AltBody = "
            New Response Notification\n\n
            Hello {$transaction['publisher_fname']},\n
            Someone has responded to your lost item report for '{$transaction['item_name']}' (Transaction ID: {$transaction_id}).\n
            Response Details:\n
            - Responder: {$responder_info['first_name']} {$responder_info['last_name']}\n
            - Found Location: {$foundloc_respo}\n
            - Storage Location: {$storeloc_respo}\n" .
            ($other_info ? "- Additional Information: {$other_info}\n" : "") . "
            You can contact the responder at: {$responder_info['email']}\n
            Please verify this response and mark the item as resolved if it has been successfully returned to you.\n
            Best regards,\nLostLink Team
        ";

        $mail->send();
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
        // Don't throw exception here, as the response process should continue even if email fails
    }

    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Response submitted successfully',
        'response_id' => $response_id
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

// Close statements
if (isset($check_user)) $check_user->close();
if (isset($stmt1)) $stmt1->close();
if (isset($get_id)) $get_id->close();
if (isset($stmt2)) $stmt2->close();
?> 