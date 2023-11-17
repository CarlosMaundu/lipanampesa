# Mpesa Payment Gateway Module for WHMCS

This module integrates Safaricom's Mpesa payment gateway with WHMCS, allowing for both Customer-to-Business (C2B) and STK/USSD Push transactions. Clients can pay their invoices using Mpesa's Paybill or Buy Goods services, and transactions are automatically confirmed and recorded in WHMCS.

## Features

- STK/USSD Push Integration: Clients can initiate payments directly to their phones.
- C2B Integration: Clients can use Paybill and Buy Goods options to make payments.
- Automatic Transaction Recording: Payments are automatically recorded in WHMCS.
- Secure Callback Handling: Validates Mpesa callbacks for payment confirmation.

## Installation

1. Copy the `lipanampesa` folder to the `modules/gateways` directory of your WHMCS installation.
2. Go to the WHMCS Admin area, navigate to `Setup > Payment Gateways`, and activate the Mpesa Payment Gateway.
3. Configure the gateway settings with your Mpesa API credentials and other required parameters.

## Configuration

After installation, you must configure the module with the following details:

- `Consumer Key` and `Consumer Secret` from your Safaricom Developer account.
- `Short Code`: Your Paybill or Till number.
- `Pass Key`: Provided by Safaricom for STK Push transactions.
- `Callback URLs`: Set in your Safaricom Developer account and in the module settings.

## Usage

Clients will see Mpesa as a payment option during checkout. They can choose between STK Push or C2B.

For STK Push, they'll enter their phone number and receive a prompt to complete the payment.

For C2B, they'll make the payment using their Mpesa menu and then enter the transaction code to verify the payment.

## Files Structure

- `callback/mpesa_callback.php`: Handles the Mpesa callback for payment confirmation.
- `lang/english.php`: Language file for custom module strings.
- `lib/admin_module.php`: Administrative functions for the module.
- `lib/mpesa_api.php`: Functions interacting with Mpesa API.
- `lib/mpesa_functions.php`: Functions for processing and recording transactions.
- `templates/payment_form.tpl`: Payment form template for client-side interactions.
- `addonmodule.php`: Main module file defining metadata and configuration.
- `hooks.php`: Hook functions interacting with WHMCS core.

## Support

If you need help or encounter any issues, please contact support at https://carlosmaundu.github.io/.

## License

The Mpesa Payment Gateway Module for WHMCS is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Author

Carlos Maundu
