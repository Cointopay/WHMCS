<?php

include('../../../includes/functions.php');
include('../../../includes/gatewayfunctions.php');
include('../../../includes/invoicefunctions.php');

if (file_exists('../../../dbconnect.php'))
    include '../../../dbconnect.php';
else if (file_exists('../../../init.php'))
    include '../../../init.php';
else
    die('[ERROR] In modules/gateways/callback/cointopay.php: include error: Cannot find dbconnect.php or init.php');

$gateway_module = 'cointopay';
$GATEWAY = getGatewayVariables($gateway_module);

if (!$GATEWAY['type']) {
    logTransaction($GATEWAY['name'], $_REQUEST, 'Not activated');
    die('[ERROR] In modules/gateways/callback/cointopay.php: Cointopay module not activated.');
}

$order_id = $_REQUEST['CustomerReferenceNr'];
$invoice_id = checkCbInvoiceID($order_id, $GATEWAY['name']);

if (!$invoice_id)
    throw new Exception('Order #' . $invoiceid . ' does not exists');

$transaction_id = $_REQUEST['TransactionID'];

checkCbTransID($transaction_id);
$fee = 0;
$amount = '';


/*$input_currency= "&inputCurrency=".$_REQUEST['inputCurrency'];
$value="MerchantID=".$_REQUEST['MerchantID']."&AltCoinID=".$_REQUEST['AltCoinID']."&TransactionID=".$_REQUEST['TransactionID']."&coinAddress=".$_REQUEST['CoinAddressUsed']."&CustomerReferenceNr=".$_REQUEST['CustomerReferenceNr']."&SecurityCode=".$_REQUEST['SecurityCode']."".$input_currency;

if ($_REQUEST['ConfirmCode'] != hash_hmac("sha256", $value, $GATEWAY['APIKey'])) {
    $transactionStatus = 'Hash Verification Failure';
    $_REQUEST['status'] = "failed";
}*/

require_once('../cointopay/init.php');
require_once('../cointopay/version.php');

echo "<style>
    .button {
        background-color: #337ab7;
        border: none;
        color: white;
        padding: 15px 32px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
    }
</style>";

$client_area=$GATEWAY['systemurl'].'clientarea.php';
switch ($_REQUEST['status'])
{
    case 'paid':

        addInvoicePayment($invoice_id, $transaction_id, $amount, $fee, $gateway_module);
        logTransaction($GATEWAY['name'], $_REQUEST, 'The payment has been received and confirmed.');

        echo '<div class="container" style="text-align: center;"><div><div>
             <br><br> 
            <h2 style="color:#0fad00">Success!</h2>
            <img src="'.$GATEWAY['systemurl'].'modules/gateways/cointopay/images/check.png">
            <p style="font-size:20px;color:#5C5C5C;">The payment has been received and confirmed successfully.</p>
            <a href="'.$GATEWAY['systemurl'].'clientarea.php" class="button" >    Back     </a>
            <br><br>
            <p>Redirecting in 5 Seconds.</p>
            <br><br>
            </div>
            </div>
            </div>';

    case 'failed':

        logTransaction($GATEWAY['name'], $_REQUEST, 'The payment is failed.');

        echo '<div class="container" style="text-align: center;"><div><div>
             <br><br> 
            <h2 style="color:#0fad00">Success!</h2>
            <img src="'.$GATEWAY['systemurl'].'modules/gateways/cointopay/images/fail.png">
            <p style="font-size:20px;color:#5C5C5C;">The payment has been received and confirmed successfully.</p>
            <a href="'.$GATEWAY['systemurl'].'/clientarea.php" class="button" >    Back     </a>
            <br><br>
            <p>Redirecting in 5 Seconds.</p>
            <br><br>
            </div>
            </div>
            </div>';
}

echo "<script>
setTimeout(function () {
   window.location.href= '$client_area';
}, 5000);
</script>";