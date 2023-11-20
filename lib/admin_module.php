
<?php

require_once __DIR__ . '/lipanampesa.php';
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
        'description' => 'Administrative interface for Mpesa Payment Gateway.',
        'author' => 'Carlos',
        'language' => 'english',
        'version' => '1.0',
        'fields' => lipanampesa_config(), // Reuse the configurations from lipanampesa.php
    );
}


/**
 * Admin Area Output.
 */
function mpesa_adminmodule_output($vars) {
    echo '<p>Welcome to the Mpesa Admin Module. Configure your Mpesa settings below.</p>';
    $settings = mpesa_adminmodule_getSettings();
    echo '<form method="post" action="addonmodules.php?module=mpesa_adminmodule&save=true">';
    foreach ($settings as $setting => $value) {
        $field = $vars['fields'][$setting];
        echo "<label>{$field['FriendlyName']}</label><br />";
        echo "<input type='{$field['Type']}' name='{$setting}' value='{$value}'><br />";
        echo "<small>{$field['Description']}</small><br /><br />";
    }
    echo '<input type="submit" value="Save Changes">';
    echo '</form>';
}

/**
 * Retrieve current settings from the database.
 */
function mpesa_adminmodule_getSettings() {
    return Capsule::table('tbladdonmodules')
                   ->where('module', 'mpesa_adminmodule')
                   ->pluck('value', 'setting');
}

/**
 * Handle saving settings if submitted.
 */
if (isset($_REQUEST['save'])) {
    foreach ($_POST as $setting => $value) {
        Capsule::table('tbladdonmodules')
               ->where('module', 'mpesa_adminmodule')
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
