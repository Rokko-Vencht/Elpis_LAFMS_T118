<?php
    header('Content-Type: application/json');
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require '../vendor/autoload.php';

    $logFile = __DIR__ . '/resolve_debug.log';
    file_put_contents($logFile, "mark_as_resolved.php called at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

    session_start();
    include '../includes/db.php';

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        file_put_contents($logFile, "User not logged in\n", FILE_APPEND);
        echo json_encode(['success' => false, 'error' => 'User not logged in']);
        exit;
    }

    // Log the current user ID
    file_put_contents($logFile, "User ID: " . $_SESSION['user_id'] . "\n", FILE_APPEND);

    // Get POST data (JSON)
    $input = file_get_contents('php://input');
    file_put_contents($logFile, "Raw input: " . $input . "\n", FILE_APPEND);
    $data = json_decode($input, true);

    // Validate required fields
    if (!isset($data['transaction_id'])) {
        file_put_contents($logFile, "Missing transaction ID\n", FILE_APPEND);
        echo json_encode(['success' => false, 'error' => 'Missing transaction ID']);
        exit;
    }

    $transaction_id = $data['transaction_id'];
    $user_id = $_SESSION['user_id']; // Current session user ID

    file_put_contents($logFile, "Transaction ID: " . $transaction_id . "\n", FILE_APPEND);
    file_put_contents($logFile, "User ID: " . $user_id . "\n", FILE_APPEND);

    try {
        // Start transaction
        $conn->begin_transaction();
        file_put_contents($logFile, "Started database transaction\n", FILE_APPEND);

        // First, verify the user exists in user_info
        $check_user = $conn->prepare("SELECT pub_id, first_name, last_name, email FROM user_info WHERE pub_id = ?");
        $check_user->bind_param("s", $user_id);
        $check_user->execute();
        $user_result = $check_user->get_result();
        
        if ($user_result->num_rows === 0) {
            file_put_contents($logFile, "User not found in user_info table\n", FILE_APPEND);
            throw new Exception("User not found in user_info table");
        }

        $user_info = $user_result->fetch_assoc();
        file_put_contents($logFile, "User info: " . print_r($user_info, true) . "\n", FILE_APPEND);

        // Verify that the transaction exists and get its details
        $check_transaction = $conn->prepare("
            SELECT t.transaction_id, t.pub_id, t.report_status, t.claim_status, t.user_respo,
                   i.item_name, u.email as publisher_email, u.first_name as publisher_fname, u.last_name as publisher_lname 
            FROM transaction_table t 
            JOIN item_table i ON t.item_id = i.item_id
            JOIN user_info u ON t.pub_id = u.pub_id 
            WHERE t.transaction_id = ?
        ");
        $check_transaction->bind_param("s", $transaction_id);
        $check_transaction->execute();
        $transaction_result = $check_transaction->get_result();
        
        if ($transaction_result->num_rows === 0) {
            file_put_contents($logFile, "Transaction not found\n", FILE_APPEND);
            throw new Exception("Transaction not found");
        }
        
        $transaction = $transaction_result->fetch_assoc();
        file_put_contents($logFile, "Transaction data: " . print_r($transaction, true) . "\n", FILE_APPEND);
        
        // Check that the publisher is the same as the current user
        if ($transaction['pub_id'] !== $user_id) {
            file_put_contents($logFile, "Only the publisher can mark this item as resolved\n", FILE_APPEND);
            throw new Exception("Only the publisher can mark this item as resolved");
        }
        
        // Check that the item is claimed before resolving
        if ($transaction['report_status'] === 'Found' && $transaction['claim_status'] !== 'Claimed') {
            file_put_contents($logFile, "Found items must be claimed before they can be resolved\n", FILE_APPEND);
            throw new Exception("This item must be claimed before it can be resolved");
        }

        // Update transaction status
        $currentDate = date('Y-m-d');
        file_put_contents($logFile, "Setting transaction_status='Resolved', transaction_date='$currentDate'\n", FILE_APPEND);
        
        $stmt = $conn->prepare("UPDATE transaction_table SET transaction_status = 'Resolved', transaction_date = ? WHERE transaction_id = ?");
        $stmt->bind_param("ss", $currentDate, $transaction_id);
        
        if (!$stmt->execute()) {
            file_put_contents($logFile, "Error updating transaction: " . $stmt->error . "\n", FILE_APPEND);
            throw new Exception("Error updating transaction: " . $stmt->error);
        }

        // Send email notification to the claimer if there is one
        if ($transaction['user_respo']) {
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
                $mail->Subject = "Item Resolved - " . $transaction['item_name'];

                // Email body
                $mail->Body = "
                    <h2>LostLink: Item Resolution Notification</h2>
                    <p>Hello {$transaction['publisher_fname']},</p>
                    <p>Your item '{$transaction['item_name']}' (Transaction ID: {$transaction_id}) has been marked as resolved.</p>
                    <p>Thank you for using LostLink to help reunite lost items with their owners!</p>
                    <p>Best regards,<br>LostLink Team</p>
                ";

                $mail->AltBody = "
                    Item Resolution Notification\n\n
                    Hello {$transaction['publisher_fname']},\n
                    Your item '{$transaction['item_name']}' (Transaction ID: {$transaction_id}) has been marked as resolved.\n
                    Thank you for using LostLink to help reunite lost items with their owners!\n
                    Best regards,\nLostLink Team
                ";

                $mail->send();
            } catch (Exception $e) {
                error_log("Email sending failed: " . $mail->ErrorInfo);
                // Don't throw exception here, as the resolution process should continue even if email fails
            }
        }

        file_put_contents($logFile, "Transaction updated successfully as resolved\n", FILE_APPEND);

        // Commit transaction
        $conn->commit();
        file_put_contents($logFile, "Transaction committed to database\n", FILE_APPEND);
        
        $response = [
            'success' => true, 
            'message' => 'Item marked as resolved successfully'
        ];
        file_put_contents($logFile, "Sending success response: " . json_encode($response) . "\n", FILE_APPEND);
        echo json_encode($response);

    } 
    catch (Exception $e) {
        $conn->rollback();
        file_put_contents($logFile, "Exception: " . $e->getMessage() . "\n", FILE_APPEND);
        file_put_contents($logFile, "Transaction rolled back\n", FILE_APPEND);
        
        $error_response = ['success' => false, 'error' => $e->getMessage()];
        file_put_contents($logFile, "Sending error response: " . json_encode($error_response) . "\n", FILE_APPEND);
        echo json_encode($error_response);
    }

    // Close statements
    if (isset($check_user)) $check_user->close();
    if (isset($check_transaction)) $check_transaction->close();
    if (isset($stmt)) $stmt->close();
    file_put_contents($logFile, "mark_as_resolved.php completed\n", FILE_APPEND);
    file_put_contents($logFile, "----------------------------------------------\n", FILE_APPEND);
?> 