<?php

require_once __DIR__ . '/mpesa_api.php';
use WHMCS\Database\Capsule;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

// Function to process STK Push response and record the transaction
function processSTKPushResponse($response, $invoiceId) {
    if ($response['ResponseCode'] == '0' && validateInvoiceID($invoiceId)) {
        recordTransaction($response, $invoiceId, 'STK Push Initiated');
        return 'Success';
    } else {
        logActivity('STK Push failed for Invoice ' . $invoiceId . ': ' . $response['ResponseDescription']);
        return 'Failed';
    }
}

// Function to record a transaction in the tblpbtransactions table
function recordTransaction($transactionData, $invoiceId, $status) {
    try {
        Capsule::table('tblpbtransactions')->insert([
            'id' => $transactionData['TransactionId'],
            'orig' => 'Mpesa',
            'dest' => 'WHMCS',
            'tstamp' => date('Y-m-d H:i:s'),
            'text' => $transactionData['ResponseDescription'],
            'mpesa_code' => $transactionData['MpesaReceiptNumber'],
            'mpesa_acc' => $invoiceId,
            'mpesa_msisdn' => $transactionData['PhoneNumber'],
            'mpesa_trx_date' => date('Y-m-d'),
            'mpesa_trx_time' => date('H:i:s'),
            'mpesa_amt' => $transactionData['Amount'],
            'mpesa_sender' => $transactionData['FirstName'].' '.$transactionData['LastName'],
            'status' => $status,
            'invoiceid' => $invoiceId,
            'archivedate' => date('Y-m-d H:i:s'),
        ]);
    } catch (Exception $e) {
        logActivity("Error recording Mpesa transaction: " . $e->getMessage());
    }
}

// Function to process C2B confirmation and record the transaction
function processC2BConfirmation($transactionData) {
    if (isset($transactionData['TransID'], $transactionData['TransAmount'], $transactionData['MSISDN'], $transactionData['BillRefNumber']) && validateInvoiceID($transactionData['BillRefNumber'])) {
        recordTransaction($transactionData, $transactionData['BillRefNumber'], 'Completed');
        return 'Success';
    } else {
        logActivity("Invalid or incomplete C2B transaction data");
        return 'Failed';
    }
}
// Function to validate Invoice ID
function validateInvoiceID($invoiceId) {
    $invoice = Capsule::table('tblinvoices')->where('id', $invoiceId)->first();
    return !is_null($invoice);
}
