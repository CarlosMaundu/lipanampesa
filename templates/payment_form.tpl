<div id="mpesa-payment-form">
    <h3>MPESA Payment</h3>
    <img src="{$systemUrl}/modules/gateways/lipanampesa/logo.png" alt="MPESA Logo" style="width:100px; height:auto;">
    <form method="post" action="{$systemUrl}/modules/gateways/lipanampesa/lib/mpesa_functions.php">
        <input type="hidden" name="action" value="stk_push">
        <input type="hidden" name="invoiceId" value="{$invoiceId}">
        <input type="hidden" name="amount" value="{$amount}">
        <div>
            <label>Enter phone number:</label>
            <input type="text" name="mpesa_phone" placeholder="e.g., 0723612154" required>
        </div>
        <button type="submit" class="btn btn-success">Send STK Push</button>
    </form>
    <form method="post" action="{$systemUrl}/modules/gateways/lipanampesa/lib/mpesa_functions.php">
        <input type="hidden" name="action" value="c2b_payment">
        <input type="hidden" name="invoiceId" value="{$invoiceId}">
        <input type="hidden" name="amount" value="{$amount}">
        <div>
            <label>Enter business no:</label>
            <input type="text" value="515467" readonly>
        </div>
        <div>
            <label>Enter account no:</label>
            <input type="text" value="21828" readonly>
        </div>
        <button type="submit" class="btn btn-primary">Verify Payment</button>
    </form>
</div>
