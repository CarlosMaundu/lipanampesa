<?php

use WHMCS\Database\Capsule;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

/**
 * Hook to perform actions after a successful payment. This hook is triggered when an invoice is marked as paid. 
 * We check if the payment method for the invoice is 'mpesa'.
 */
/**add_hook('InvoicePaid', 1, function($vars) {
    $invoiceId = $vars['invoiceid'];

    // Retrieve payment details from the invoice
    $paymentDetails = Capsule::table('tblinvoices')
        ->where('id', $invoiceId)
        ->first();

    // Check if payment was made via Mpesa
    if ($paymentDetails->paymentmethod == 'mpesa') {
        // Update tblpbtransactions or perform other actions as required
        try {
            Capsule::table('tblpbtransactions')->insert([
                'id' => 'unique_transaction_id', // Generate or fetch this ID
                'orig' => 'payment_origin',
                'dest' => 'payment_destination',
                'tstamp' => now(),
                'text' => 'Transaction details',
                'mpesa_code' => 'Mpesa_Transaction_Code', // From Mpesa
                'mpesa_acc' => 'Account_Details',
                'mpesa_msisdn' => 'Customer_Phone_Number',
                'mpesa_trx_date' => 'Transaction_Date',
                'mpesa_trx_time' => 'Transaction_Time',
                'mpesa_amt' => $paymentDetails->total,
                'mpesa_sender' => 'Sender_Details',
                'status' => 'Completed', // Or other relevant status
                'invoiceid' => $invoiceId,
                'archivedate' => now(),
            ]);
        } catch (Exception $e) {
            // Log error
            logActivity("Error updating tblpbtransactions: " . $e->getMessage());
        }
    }
});

/**
 * Additional hooks can be added here to handle other events, such as failed payments, refunds, etc.
 */

