<?php
// actions/reminder_action.php
session_start();
require_once '../config/database.php';
require_once '../config/sendgrid.php';
require_once '../includes/functions.php';

requireRole('vendor');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $conn = $db->getConnection();
    
    $action = $_POST['action'] ?? '';
    $user_id = $_SESSION['user_id'];
    $vendor_username = $_SESSION['username'] ?? 'Vendor';

    if ($action === 'send_reminder') {
        // Get customer IDs (can be single or array)
        $customer_ids = $_POST['customer_ids'] ?? [];
        
        if (empty($customer_ids)) {
            redirect("../vendor/customers/index.php?error=Please select at least one customer");
        }
        
        // Ensure it's an array
        if (!is_array($customer_ids)) {
            $customer_ids = [$customer_ids];
        }
        
        // Fetch customers that belong to this vendor
        $placeholders = implode(',', array_fill(0, count($customer_ids), '?'));
        $stmt = $conn->prepare("SELECT customer_id, name, email, current_balance FROM customers WHERE customer_id IN ($placeholders) AND vendor_id = ?");
        $params = array_merge($customer_ids, [$user_id]);
        $stmt->execute($params);
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($customers)) {
            redirect("../vendor/customers/index.php?error=No valid customers found");
        }
        
        // Get SendGrid configuration
        $fromEmail = SendGridConfig::getFromEmail();
        $mpesaTill = SendGridConfig::getMpesaTill();
        $templateId = SendGridConfig::getTemplateId();
        $apiKey = SendGridConfig::getApiKey();
        
        if (empty($templateId)) {
            redirect("../vendor/customers/index.php?error=SendGrid template not configured. Please set SENDGRID_TEMPLATE_ID in .env");
        }
        
        $successCount = 0;
        $errorCount = 0;
        $noEmailCount = 0;
        $errorDetails = [];
        
        // Send emails using SendGrid API
        foreach ($customers as $customer) {
            if (empty($customer['email'])) {
                $noEmailCount++;
                continue;
            }
            
            if (!validateEmail($customer['email'])) {
                $errorCount++;
                $errorDetails[] = "Invalid email: {$customer['email']}";
                continue;
            }
            
            // Prepare email data
            $emailData = [
                'customer_name' => $customer['name'],
                'balance' => formatCurrency($customer['current_balance']),
                'vendor_name' => $vendor_username,
                'mpesa_till' => $mpesaTill
            ];
            
            // Send via SendGrid API
            $errorMsg = '';
            $result = sendReminderEmail($customer['email'], $vendor_username, $fromEmail, $templateId, $emailData, $apiKey, $errorMsg);
            
            if ($result) {
                $successCount++;
            } else {
                $errorCount++;
                $errorDetails[] = "{$customer['name']}: " . ($errorMsg ?: 'Unknown error');
                // Log error for debugging
                error_log("SendGrid error for {$customer['email']}: " . ($errorMsg ?: 'Unknown error'));
            }
        }
        
        // Build success/error message
        if ($successCount > 0 && $errorCount == 0) {
            $message = "Reminders sent: $successCount";
            if ($noEmailCount > 0) {
                $message .= ", Skipped (no email): $noEmailCount";
            }
            redirect("../vendor/customers/index.php?success=" . urlencode($message));
        } else {
            $message = "Reminders sent: $successCount";
            if ($noEmailCount > 0) {
                $message .= ", Skipped (no email): $noEmailCount";
            }
            if ($errorCount > 0) {
                $message .= ", Failed: $errorCount";
                // Include first error detail if available
                if (!empty($errorDetails)) {
                    $message .= " - " . $errorDetails[0];
                }
            }
            redirect("../vendor/customers/index.php?error=" . urlencode($message));
        }
        
    }
} else {
    redirect("../vendor/dashboard.php");
}

function sendReminderEmail($toEmail, $vendorUsername, $fromEmail, $templateId, $emailData, $apiKey, &$errorMsg = null) {
    // Prepare SendGrid API request data
    $data = [
        'from' => [
            'email' => $fromEmail
        ],
        'personalizations' => [
            [
                'to' => [
                    ['email' => $toEmail]
                ],
                'dynamic_template_data' => [
                    'customer_name' => $emailData['customer_name'],
                    'balance' => $emailData['balance'],
                    'vendor_name' => $emailData['vendor_name'],
                    'mpesa_till' => $emailData['mpesa_till']
                ]
            ]
        ],
        'template_id' => $templateId,
        'subject' => "Payment Reminder - $vendorUsername"
    ];
    
    $jsonData = json_encode($data);
    
    if ($jsonData === false) {
        $errorMsg = 'Failed to encode JSON data';
        return false;
    }
    
    // Send request to SendGrid API
    $url = 'https://api.sendgrid.com/v3/mail/send';
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => [
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json',
                'Content-Length: ' . strlen($jsonData)
            ],
            'content' => $jsonData,
            'ignore_errors' => true,
            'timeout' => 30
        ]
    ];
    
    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    
    // Get HTTP response code
    $httpCode = 0;
    if (isset($http_response_header) && is_array($http_response_header) && count($http_response_header) > 0) {
        $statusLine = $http_response_header[0];
        if (preg_match('/HTTP\/\d\.\d\s+(\d+)/', $statusLine, $matches)) {
            $httpCode = (int)$matches[1];
        }
    }
    
    // Check if request failed
    if ($response === false) {
        $error = error_get_last();
        $errorMsg = $error ? $error['message'] : 'Failed to connect to SendGrid API';
        return false;
    }
    
    // SendGrid returns 202 for successful sends
    if ($httpCode >= 200 && $httpCode < 300) {
        return true;
    } else {
        // Parse error response from SendGrid
        $errorResponse = json_decode($response, true);
        if ($errorResponse && isset($errorResponse['errors'])) {
            $errors = [];
            foreach ($errorResponse['errors'] as $err) {
                $errors[] = $err['message'] ?? 'Unknown error';
            }
            $errorMsg = implode(', ', $errors);
        } else {
            $errorMsg = "HTTP $httpCode: " . ($response ?: 'No response from SendGrid');
        }
        return false;
    }
}
?>

