<?php

require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/mpesa_functions.php';

App::load_function('gateway');
App::load_function('invoice');

$gatewayModuleName = basename(__FILE__, '.php');
$gatewayParams = getGatewayVariables($gatewayModuleName);

if (!$gatewayParams['type']) {
    die("Module Not Activated");
}

// Fetch the incoming data from the callback
$transactionData = json_decode(file_get_contents('php://input'), true);

// Validate incoming JSON payload
if (json_last_error() !== JSON_ERROR_NONE) {
    logActivity("Invalid JSON in callback");
    http_response_code(400); // Bad request response
    exit;
}

// Handle STK Push callback
if (isset($transactionData['Body']['stkCallback'])) {
    $response = processSTKPushResponse($transactionData['Body']['stkCallback'], $transactionData['Body']['stkCallback']['MerchantRequestID']);
    $invoiceId = $transactionData['Body']['stkCallback']['MerchantRequestID'];

    // Perform additional validation on $invoiceId if necessary

    if ($response['ResultCode'] == 0) {
        // Redirect customer to invoice with success message
        header('Location: ' . $gatewayParams['systemurl'] . 'viewinvoice.php?id=' . $invoiceId . '&paymentsuccess=true');
    } else {
        // Redirect customer to invoice with failure message
        header('Location: ' . $gatewayParams['systemurl'] . 'viewinvoice.php?id=' . $invoiceId . '&paymentfailed=true');
    }
}

// Handle C2B confirmation
if (isset($transactionData['TransactionType']) && $transactionData['TransactionType'] == 'PayBill') {
    $response = processC2BConfirmation($transactionData);

    // Perform additional validation on $response if necessary

    if ($response['ResultCode'] == 0) {
        // Send an acknowledgment to M-Pesa
        header('Content-Type: application/json');
        echo json_encode(['ResultCode' => 0, 'ResultDesc' => 'Confirmation received successfully']);
    }
}

function recordSTKPayment($invoiceId, $amount, $transactionId, $phoneNumber, $transactionDate, $gatewayModuleName) {
    // This function will record the STK payment in the database and add it to the WHMCS invoice
    addInvoicePayment($invoiceId, $transactionId, $amount, 0, $gatewayModuleName);
    // Additional logic to record the transaction can be added here
}

function recordC2BPayment($invoiceId, $amount, $transactionId, $phoneNumber, $transactionDate, $gatewayModuleName) {
    // This function will record the C2B payment in the database and add it to the WHMCS invoice
    addInvoicePayment($invoiceId, $transactionId, $amount, 0, $gatewayModuleName);
    // Additional logic to record the transaction can be added here
}
