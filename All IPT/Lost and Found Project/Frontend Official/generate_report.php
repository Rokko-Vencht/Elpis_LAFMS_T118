<?php
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        require_once __DIR__ .'/../vendor/autoload.php';
        require_once __DIR__ .'/../includes/db.php';

        $mpdf = new \Mpdf\Mpdf;
        
        header('Content-Type: application/pdf');

        // Query to get all resolved transactions with related information
        $sql = "SELECT 
                t.transaction_id,
                i.item_name,
                pub.user_name as publisher_username,
                resp.user_name as responder_username,
                c.categ_item as category,
                t.report_status,
                t.claim_status,
                t.transaction_status,
                t.date_filed
            FROM transaction_table t
            JOIN item_table i ON t.item_id = i.item_id
            JOIN category_table c ON i.categ_id = c.categ_id
            JOIN user_info pub ON t.pub_id = pub.pub_id
            LEFT JOIN user_info resp ON t.user_respo = resp.pub_id
            WHERE t.transaction_status = 'Resolved'
            ORDER BY t.date_filed DESC";
        
        $result = $conn->query($sql);
        $transactions = $result->fetch_all(MYSQLI_ASSOC);
        $count = 1;

        // Get statistics
        // Most common category
        $topCategoryQuery = "SELECT c.categ_item, COUNT(*) as count 
            FROM transaction_table t 
            JOIN item_table i ON t.item_id = i.item_id 
            JOIN category_table c ON i.categ_id = c.categ_id 
            GROUP BY c.categ_id 
            ORDER BY count DESC 
            LIMIT 1";
        $topCategoryResult = $conn->query($topCategoryQuery);
        $topCategory = $topCategoryResult->fetch_assoc();

        // Total items
        $totalItemsQuery = "SELECT COUNT(*) as total FROM transaction_table";
        $totalItemsResult = $conn->query($totalItemsQuery);
        $totalItems = $totalItemsResult->fetch_assoc()['total'];

        // Lost items count
        $lostItemsQuery = "SELECT COUNT(*) as lost FROM transaction_table WHERE report_status = 'Lost'";
        $lostItemsResult = $conn->query($lostItemsQuery);
        $lostItems = $lostItemsResult->fetch_assoc()['lost'];

        // Found items count
        $foundItemsQuery = "SELECT COUNT(*) as found FROM transaction_table WHERE report_status = 'Found'";
        $foundItemsResult = $conn->query($foundItemsQuery);
        $foundItems = $foundItemsResult->fetch_assoc()['found'];

        // Unresolved items
        $unresolvedQuery = "SELECT COUNT(*) as unresolved FROM transaction_table WHERE transaction_status != 'Resolved'";
        $unresolvedResult = $conn->query($unresolvedQuery);
        $unresolved = $unresolvedResult->fetch_assoc()['unresolved'];

        // Resolved items
        $resolvedQuery = "SELECT COUNT(*) as resolved FROM transaction_table WHERE transaction_status = 'Resolved'";
        $resolvedResult = $conn->query($resolvedQuery);
        $resolved = $resolvedResult->fetch_assoc()['resolved'];

        // Calculate percentages
        $resolvedPercentage = ($totalItems > 0) ? round(($resolved / $totalItems) * 100, 1) : 0;
        $unresolvedPercentage = ($totalItems > 0) ? round(($unresolved / $totalItems) * 100, 1) : 0;
        $lostPercentage = ($totalItems > 0) ? round(($lostItems / $totalItems) * 100, 1) : 0;
        $foundPercentage = ($totalItems > 0) ? round(($foundItems / $totalItems) * 100, 1) : 0;

        // Query for RESOLVED items
        $resolvedSql = "SELECT 
                t.transaction_id,
                i.item_name,
                pub.user_name as publisher_username,
                resp.user_name as responder_username,
                c.categ_item as category,
                t.report_status,
                t.claim_status,
                t.transaction_status,
                t.date_filed
            FROM transaction_table t
            JOIN item_table i ON t.item_id = i.item_id
            JOIN category_table c ON i.categ_id = c.categ_id
            JOIN user_info pub ON t.pub_id = pub.pub_id
            LEFT JOIN user_info resp ON t.user_respo = resp.pub_id
            WHERE t.transaction_status = 'Resolved'
            ORDER BY t.date_filed DESC";
        
        $resolvedResult = $conn->query($resolvedSql);
        $resolvedTransactions = $resolvedResult->fetch_all(MYSQLI_ASSOC);

        // Start building HTML
        $html = '
            <html>
                <head>
                    <style>
                        body {
                            font-family: Segoe UI, Tahoma, Geneva, Verdana, sans-serif;                          
                            font-size: 12px;
                            color: #333;
                        }
        
                        .header {
                            text-align: center;
                            margin-bottom: 20px;
                        }

                        .header h2 {
                            color: rgb(6, 28, 51);
                            font-size: 24px;
                            margin: 0;
                        }

                        .header h4 {
                            color: black;
                            font-size: 13px;
                            font-weight:normal;
                            margin: 10px 0;
                        }

                        .date-generated {
                            text-align: right;
                            color: rgb(6, 28, 51);
                            font-size: 12px;
                            margin: 20px 0;
                            font-style: italic;
                        }

                        table {
                            width: 100%;
                            border-collapse: collapse;
                            font-size: 11px;
                            margin-top: 10px;
                        }

                        th, td {
                            background-color: white;
                            border: 1px solid rgb(6, 28, 51);
                            padding: 8px;
                            text-align: center;
                        }

                        th {
                            background-color: rgb(3, 0, 99);
                            font-weight: bold;
                            color: white;
                        }

                        tbody tr:nth-child(even) {
                            background-color: #f8f9fa;
                        }

                        .signature-section {
                            margin-top: 40px;
                            width: 100%;
                            font-size: 11px;
                            text-align: right;
                        }

                        .signature {
                            float: right;
                            text-align: right;
                            padding-top: 20px;
                        }

                        .signature p {
                            margin: 0;
                            line-height: 1.5;
                        }

                        .date {
                            text-align: right;
                            margin: 20px 0;
                            font-size: 12px;
                        }

                        .section-divider {
                            margin-top: 30px;
                            margin-bottom: 15px;
                            padding-bottom: 3px;
                        }

                        .section-divider.resolved {
                            border-bottom: 5px solid  rgb(32, 57, 131);
                        }

                        .section-divider.unresolved {
                            border-bottom: 5px solid  rgb(57, 11, 95);
                        }
                        
                        .section-divider.Genreps {
                            border-bottom: 5px solid  rgb(6, 28, 51);
                        }

                        .section-divider p {
                            color: rgb(6, 28, 51);
                            font-weight: bold;
                            font-size: 14px;
                            margin: 0;
                            padding: 0;
                            white-space: nowrap;
                        }

                        .section-divider.resolved p {
                            color: rgb(32, 57, 131);
                        }

                        .section-divider.unresolved p {
                            color: rgb(57, 11, 95);
                        }

                        .section-divider.Genreps p {
                            color: rgb(6, 28, 51);
                        }

                        .stats-text {
                            margin: 5px 0;
                            font-size: 12px;
                            color: rgb(6, 28, 51);
                        }

                        .stats-text strong {
                            color: rgb(3, 0, 99);
                        }

                        .gen-reps{
                            margin-left: 20px;
                        }

                    </style>
                </head>
                <body>                    
                    <div class="header">
                        <h2>LOSTLINK REPORTS</h2>
                        <h4>Incorporates Resolved Items and General Report</h4>
                    </div>


                    <div class="section-divider resolved">
                        <p>Resolved Items:</p>
                    </div>
                    <p style:"color:"black"">This section displays all of the reported items that are resolved or completed.</p>

                    
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item Name</th>
                                <th>Publisher</th>
                                <th>Responder</th>
                                <th>Category</th>
                                <th>Report Status</th>
                                <th>Claim Status</th>
                                <th>Transaction Status</th>
                            </tr>
                        </thead>
                        <tbody>';

        // Add resolved items
        if (empty($resolvedTransactions)) {
            $html .= '
                <tr>
                    <td colspan="8" style="text-align: center; padding: 15px;">No resolved items found.</td>
                </tr>';
        } else {
            $count = 1;
            foreach($resolvedTransactions as $transaction) {
                $html .= '
                    <tr>
                        <td>' . $count++ . '</td>
                        <td>' . htmlspecialchars($transaction['item_name']) . '</td>
                        <td>' . htmlspecialchars($transaction['publisher_username']) . '</td>
                        <td>' . ($transaction['responder_username'] ? htmlspecialchars($transaction['responder_username']) : 'N/A') . '</td>
                        <td>' . htmlspecialchars($transaction['category']) . '</td>
                        <td>' . htmlspecialchars($transaction['report_status']) . '</td>
                        <td>' . htmlspecialchars($transaction['claim_status']) . '</td>
                        <td>' . htmlspecialchars($transaction['transaction_status']) . '</td>
                    </tr>';
            }
        }

        $html .= '
                        </tbody>
                    </table>

                    <div class="section-divider unresolved">
                        <p>Yet To Be Resolved Items:</p>
                    </div>
                    <p style:"color:"black"">This section displays all of the reported items that are yet to be resolved or completed.</p>

                    
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item Name</th>
                                <th>Publisher</th>
                                <th>Responder</th>
                                <th>Category</th>
                                <th>Report Status</th>
                                <th>Claim Status</th>
                                <th>Transaction Status</th>
                            </tr>
                        </thead>
                        <tbody>';

        // Query for UNRESOLVED items
        $unresolvedSql = "SELECT 
                t.transaction_id,
                i.item_name,
                pub.user_name as publisher_username,
                resp.user_name as responder_username,
                c.categ_item as category,
                t.report_status,
                t.claim_status,
                t.transaction_status,
                t.date_filed
            FROM transaction_table t
            JOIN item_table i ON t.item_id = i.item_id
            JOIN category_table c ON i.categ_id = c.categ_id
            JOIN user_info pub ON t.pub_id = pub.pub_id
            LEFT JOIN user_info resp ON t.user_respo = resp.pub_id
            WHERE t.transaction_status != 'Resolved'
            ORDER BY t.date_filed DESC";

        $unresolvedResult = $conn->query($unresolvedSql);
        $unresolvedTransactions = $unresolvedResult->fetch_all(MYSQLI_ASSOC);

        // Add unresolved items
        if (empty($unresolvedTransactions)) {
            $html .= '
                <tr>
                    <td colspan="8" style="text-align: center; padding: 15px;">No unresolved items found.</td>
                </tr>';
        } else {
            $count = 1;
            foreach($unresolvedTransactions as $transaction) {
                $html .= '
                    <tr>
                        <td>' . $count++ . '</td>
                        <td>' . htmlspecialchars($transaction['item_name']) . '</td>
                        <td>' . htmlspecialchars($transaction['publisher_username']) . '</td>
                        <td>' . ($transaction['responder_username'] ? htmlspecialchars($transaction['responder_username']) : 'N/A') . '</td>
                        <td>' . htmlspecialchars($transaction['category']) . '</td>
                        <td>' . htmlspecialchars($transaction['report_status']) . '</td>
                        <td>' . htmlspecialchars($transaction['claim_status']) . '</td>
                        <td>' . htmlspecialchars($transaction['transaction_status']) . '</td>
                    </tr>';
            }
        }

        $html .= '
                        </tbody>
                    </table>      
                    
                    <div class="section-divider Genreps">
                        <p>General Reports:</p>
                    </div>
                    <p style:"color:"black"">This section generates a summary of all the items reported within the LostLink system.</p>

                    <div class="gen-reps">
                        <p class="stats-text">Most Common Category: <strong>' . htmlspecialchars($topCategory['categ_item']) . '</strong> (' . htmlspecialchars($topCategory['count']) . ' items)</p>
                        <p class="stats-text">Total Items: <strong>' . $totalItems . '</strong></p>
                        <p class="stats-text">Lost Items: <strong>' . $lostItems . '</strong> (' . $lostPercentage . '% of total items)</p>
                        <p class="stats-text">Found Items: <strong>' . $foundItems . '</strong> (' . $foundPercentage . '% of total items)</p>
                        <p class="stats-text">Unresolved Items: <strong>' . $unresolved . '</strong> (' . $unresolvedPercentage . '% of total items)</p>
                        <p class="stats-text">Resolved Items: <strong>' . $resolved . '</strong> (' . $resolvedPercentage . '% of total items)</p>
                    </div>
                    
                    <div class="date-generated">
                        <p>Date Generated: ' . date('F d, Y') . '</p>
                    </div>

                    <div class="signature-section">
                        <div class="signature">
                            <p>Generated By:</p>
                            <p><strong style="color: rgb(6, 28, 51); font-style: italic;">LostLink</strong></p>
                        </div>
                    </div>
                </body>
            </html>';

        $mpdf->WriteHTML($html);

        $mpdf->SetHTMLFooter('
            <div style="text-align: right; font-size: 10px; color: #555;">
                Page {PAGENO}/{nbpg}
            </div>
        ');

        $mpdf->Output('Resolved_Transactions_Report.pdf', 'I');
        exit;
    }
?> 