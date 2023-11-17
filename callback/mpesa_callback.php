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

$transactionData = json_decode(file_get_contents('php://input'), true);

// Handle STK Push callback
if (isset($transactionData['Body']['stkCallback'])) {
    $response = processSTKPushResponse($transactionData['Body']['stkCallback'], $transactionData['Body']['stkCallback']['MerchantRequestID']);
    $invoiceId = $transactionData['Body']['stkCallback']['MerchantRequestID'];
    if ($response == 'Success') {
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
    if ($response == 'Success') {
        // Send an acknowledgment to M-Pesa
        header('Content-Type: application/json');
        echo json_encode(['ResultCode' => 0, 'ResultDesc' => 'Confirmation received successfully']);
    }
}
