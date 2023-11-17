
<?php

use WHMCS\Database\Capsule;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

/**
 * Define the module's admin configuration options.
 */
function mpesa_adminmodule_config() {
    return array(
        'name' => 'Mpesa Admin Module',
        'description' => 'Administrative interface for the Mpesa Payment Gateway.',
        'author' => 'CarlosMaundu',
        'language' => 'english',
        'version' => '1.0',
        'fields' => [
            'consumerKey' => [
                'FriendlyName' => 'Consumer Key',
                'Type' => 'text',
                'Size' => '25',
                'Default' => '',
                'Description' => 'Enter your Mpesa Consumer Key.',
            ],
            'consumerSecret' => [
                'FriendlyName' => 'Consumer Secret',
                'Type' => 'text',
                'Size' => '50',
                'Default' => '',
                'Description' => 'Enter your Mpesa Consumer Secret.',
            ],
            'tillNumber' => [
                'FriendlyName' => 'Till Number',
                'Type' => 'text',
                'Size' => '10',
                'Default' => '',
                'Description' => 'Enter your Mpesa Till Number.',
            ],
            'storeNumber' => [
                'FriendlyName' => 'Store Number',
                'Type' => 'text',
                'Size' => '6',
                'Default' => '',
                'Description' => 'Store Number (Applicable for till numbers)',
            ],
            'paybillNumber' => [
                'FriendlyName' => 'Paybill Number',
                'Type' => 'text',
                'Size' => '10',
                'Default' => '',
                'Description' => 'Enter your Mpesa Paybill Number.',
            ],
            'passKey' => [
                'FriendlyName' => 'Pass Key',
                'Type' => 'password',
                'Size' => '50',
                'Default' => '',
                'Description' => 'Enter your Mpesa Pass Key.',
            ],
            'mode' => [
                'FriendlyName' => 'Mode',
                'Type' => 'radio',
                'Options' => 'Till,Paybill',
                'Default' => 'Till',
                'Description' => 'Select the mode of transaction (Till or Paybill).',
            ],
            // Additional configuration options can be added here
        ],
    );
}

/**
 * Admin Area Output.
 */
function mpesa_adminmodule_output($vars) {
    echo '<p>Welcome to the Mpesa Admin Module. Configure your Mpesa settings below.</p>';

    // Display current settings and provide a form for updating them
    $settings = mpesa_adminmodule_getSettings();

    echo '<form method="post" action="addonmodules.php?module=mpesa_adminmodule&save=true">';
    foreach ($settings as $setting => $value) {
        $friendlyName = $vars['fields'][$setting]['FriendlyName'];
        $fieldType = $vars['fields'][$setting]['Type'];
        $description = $vars['fields'][$setting]['Description'];
        echo "<label>{$friendlyName}</label><br />";
        if ($fieldType == 'text' || $fieldType == 'password') {
            echo "<input type='{$fieldType}' name='{$setting}' value='{$value}'><br />";
        }
        // Add other field types handling as needed
        echo "<small>{$description}</small><br /><br />";
    }
    echo '<input type="submit" value="Save Changes">';
    echo '</form>';
}

/**
 * Retrieve current settings from the database.
 */
function mpesa_adminmodule_getSettings() {
    return Capsule::table('tbladdonmodules')
                   ->where('module', 'mpesa_addon')
                   ->pluck('value', 'setting');
}

/**
 * Handle saving settings if submitted.
 */
if (isset($_REQUEST['save'])) {
    foreach ($_POST as $setting => $value) {
        Capsule::table('tbladdonmodules')
               ->where('module', 'mpesa_addon')
               ->where('setting', $setting)
               ->update(['value' => $value]);
    }
}

/**
 * View Transactions Functionality.
 */
function viewTransactions() {
    echo '<h2>Transaction Logs</h2>';

    $transactions = Capsule::table('tblpbtransactions')->get();

    if ($transactions->isEmpty()) {
        echo '<p>No transactions found.</p>';
        return;
    }

    echo '<table style="width:100%; text-align:left;">';
    echo '<tr><th>ID</th><th>Amount</th><th>Transaction Code</th><th>Date</th><th>Status</th></tr>';
    foreach ($transactions as $transaction) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($transaction->id) . '</td>';
        echo '<td>' . htmlspecialchars($transaction->mpesa_amt) . '</td>';
        echo '<td>' . htmlspecialchars($transaction->mpesa_code) . '</td>';
        echo '<td>' . htmlspecialchars($transaction->tstamp) . '</td>';
        echo '<td>' . htmlspecialchars($transaction->status) . '</td>';
        echo '</tr>';
    }
    echo '</table>';
}

/**
 * Sidebar Links for Admin Area.
 */
function mpesa_adminmodule_sidebar($vars) {
    $sidebar = '<ul>';
    $sidebar .= '<li><a href="addonmodules.php?module=mpesa_adminmodule&action=view_transactions">View Transactions</a></li>';
    $sidebar .= '</ul>';
    return $sidebar;
}

// Implement other necessary admin functions or hooks