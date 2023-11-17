<?php

use WHMCS\Database\Capsule;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

/**
 * Generates an access token for Mpesa API requests.
 */
function getMpesaAccessToken($consumerKey, $consumerSecret) {
    $credentials = base64_encode($consumerKey . ':' . $consumerSecret);
    $url = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic '.$credentials));
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $curl_response = curl_exec($curl);
    $response = json_decode($curl_response);

    curl_close($curl);

    return $response->access_token;
    
    if (curl_errno($curl)) {
        logActivity("Curl error in getMpesaAccessToken: " . curl_error($curl));
        return null; // or handle the error as appropriate
    }

    if ($response === false) {
        logActivity("Invalid response in getMpesaAccessToken");
        return null; // or handle the error as appropriate
    }
}

/**
 * Initiates an STK Push to the customer's phone.
 */
function initiateSTKPush($params, $amount, $phone, $invoiceId) {
    $accessToken = getMpesaAccessToken($params['consumerKey'], $params['consumerSecret']);
    $url = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

    $timestamp = date('YmdHis');
    $password = base64_encode($params['shortCode'].$params['passKey'].$timestamp);

    $curl_post_data = array(
        'BusinessShortCode' => $params['shortCode'],
        'Password' => $password,
        'Timestamp' => $timestamp,
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => $amount,
        'PartyA' => $phone,
        'PartyB' => $params['shortCode'],
        'PhoneNumber' => $phone,
        'CallBackURL' => 'https://yourdomain.com/mpesa_callback.php',
        'AccountReference' => $invoiceId,
        'TransactionDesc' => 'Payment for Invoice ' . $invoiceId
    );

    $data_string = json_encode($curl_post_data);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$accessToken));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $curl_response = curl_exec($curl);
    curl_close($curl);

    return json_decode($curl_response, true);
}

/**
 * Registers the C2B URL for transaction confirmation.
 */
function registerC2BURL($params) {
    $accessToken = getMpesaAccessToken($params['consumerKey'], $params['consumerSecret']);
    $url = 'https://api.safaricom.co.ke/mpesa/c2b/v1/registerurl';

    $curl_post_data = array(
        'ShortCode' => $params['shortCode'],
        'ResponseType' => 'Completed',
        'ConfirmationURL' => 'https://yourdomain.com/confirmation_url',
        'ValidationURL' => 'https://yourdomain.com/validation_url'
    );

    $data_string = json_encode($curl_post_data);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$accessToken));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $curl_response = curl_exec($curl);
    curl_close($curl);

    return json_decode($curl_response, true);
}
