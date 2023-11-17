<?php

use WHMCS\Database\Capsule;

/**
 * Lipa Na M-Pesa Payment Gateway Addon Module
 *
 * This module integrates M-Pesa payment gateway with WHMCS.
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

/**
 * Define module's metadata.
 *
 * @return array
 */
function lipanampesa_MetaData()
{
    return array(
        'DisplayName' => 'Lipa Na M-Pesa Payment Gateway',
        'APIVersion' => '1.1.0', // Use API Version 1.1.0
        'Description' => 'Integrates Lipa Na M-Pesa payment gateway for transactions.',
        'Author' => 'Carlos',
        'Version' => '1.0',
        'Language' => 'english',
    );
}

/**
 * Define module's configuration options.
 *
 * @return array
 */
function lipanampesa_config()
{
    return array(
        'FriendlyName' => array(
            'Type' => 'System',
            'Value' => 'Lipa Na M-Pesa Payment',
            'Description' => "A module designed to integrate Lipa Na M-Pesa payment gateway for transactions",
            'Version' => '1.0.0',
            'Author' => 'Mpesa',
            'Language' => 'english',
        ),

        'consumerKey' => array(
            'FriendlyName' => 'Consumer Key',
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Enter your Mpesa Consumer Key.',
        ),
        'consumerSecret' => array(
            'FriendlyName' => 'Consumer Secret',
            'Type' => 'text',
            'Size' => '50',
            'Default' => '',
            'Description' => 'Enter your Mpesa Consumer Secret.',
        ),
        'tillNumber' => array(
            'FriendlyName' => 'Till Number',
            'Type' => 'text',
            'Size' => '10',
            'Default' => '',
            'Description' => 'Enter your Mpesa Till Number.',
        ),
        'storeNumber' => array(
            'FriendlyName' => 'Store Number',
            'Type' => 'text',
            'Size' => '6',
            'Default' => '',
            'Description' => 'Store Number (Applicable for till numbers)',
        ),
        'paybillNumber' => array(
            'FriendlyName' => 'Paybill Number',
            'Type' => 'text',
            'Size' => '10',
            'Default' => '',
            'Description' => 'Enter your Mpesa Paybill Number.',
        ),
        'passKey' => array(
            'FriendlyName' => 'Pass Key',
            'Type' => 'password',
            'Size' => '50',
            'Default' => '',
            'Description' => 'Enter your Mpesa Pass Key.',
        ),
        'mode' => array(
            'FriendlyName' => 'Mode',
            'Type' => 'radio',
            'Options' => 'Till,Paybill',
            'Default' => 'Till',
            'Description' => 'Select the mode of transaction (Till or Paybill).',
        ),
        // Additional configuration options can be added here
    );
}

/**
 * Handle actions to be performed on module activation.
 *
 * @return array
 */
function lipanampesa_activate()
{
    try {
        if (!Capsule::schema()->hasTable('tblpbtransactions')) {
            Capsule::schema()->create('tblpbtransactions', function ($table) {
                $table->string('id', 100)->primary();
                $table->string('orig', 20);
                $table->string('dest', 100);
                $table->timestamp('tstamp');
                $table->string('text', 255);
                $table->string('mpesa_code', 50)->unique();
                $table->string('mpesa_acc', 100);
                $table->string('mpesa_msisdn', 100);
                $table->string('mpesa_trx_date', 100);
                $table->string('mpesa_trx_time', 100);
                $table->integer('mpesa_amt', false, true);
                $table->string('mpesa_sender', 100);
                $table->string('status', 10)->default('Open');
                $table->integer('invoiceid', false, true)->default(0);
                $table->dateTime('archivedate');
            });
        }
        return array('status' => 'success', 'description' => 'Mpesa addon module activated successfully.');
    } catch (Exception $e) {
        return array('status' => 'error', 'description' => 'Unable to create tblpbtransactions: ' . $e->getMessage());
    }
}

/**
 * Handle actions to be performed on module deactivation.
 *
 * @return array
 */
function lipanampesa_deactivate()
{
    // You may implement actions on deactivation like removing custom tables or settings
    return array('status' => 'success', 'description' => 'Mpesa addon module deactivated successfully.');
}

/**
 * Handle actions to be performed during module upgrade.
 *
 * @param array $vars
 */
function lipanampesa_upgrade($vars)
{
    $version = $vars['version'];
    // Perform version upgrade tasks here
}

