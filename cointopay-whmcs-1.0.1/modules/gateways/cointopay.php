<?php
require_once(dirname(__FILE__) . '/cointopay/init.php');
require_once(dirname(__FILE__) . '/cointopay/version.php');

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

function cointopay_MetaData()
{
    return array(
        'DisplayName' => 'Cointopay',
        'APIVersion' => '1.1',
        'DisableLocalCreditCardInput' => false,
        'TokenisedStorage' => false,
    );
}

function cointopay_config()
{
    return array(
        'FriendlyName' => array(
            'Type' => 'System',
            'Value' => 'Cointopay',
        ),
        'MerchantID' => array(
            'FriendlyName' => 'Merchant ID',
            'Type' => 'text',
            'Description' => 'Merchant ID from cointopay.com',
        ),
        'APIKey' => array(
            'FriendlyName' => 'API Key',
            'Type' => 'text',
            'Description' => 'API Key from cointopay.com',
        ),
        'SecurityCode' => array(
            'FriendlyName' => 'Security Code',
            'Type' => 'text',
            'Description' => 'Security Code from cointopay.com',
        ),
    );
}

function cointopay_link($params)
{
    if (false === isset($params) || true === empty($params)) {
        die('[ERROR] In modules/gateways/cointopay.php::cointopay_link() function: Missing or invalid $params data.');
    }

    $cointopay_params = array(
        'order_id'         => $params['invoiceid'],
        'price'            => number_format($params['amount'], 2, '.', ''),
        'currency'         => $params['currency'],
        'receive_currency' => $params['ReceiveCurrency'],
        'cancel_url'       => $params['systemurl'] . 'clientarea.php',
        'callback_url'     => $params['systemurl'] . 'modules/gateways/callback/cointopay.php',
        'success_url'      => $params['systemurl'] . 'viewinvoice.php?id=' . $params['invoiceid'],
        'title'            => $params['companyname'],
        'description'      => $params['description'],
    );

    $authentication = array(
        'merchant_id'       => $params['MerchantID'],
        'security_code'     => $params['SecurityCode'],
        'api_key'           => $params['APIKey'],
        'default_currency'  => $params['currency'],
        'user_agent'        => 'Cointopay - WHMCS Extension v'.COINTOPAY_PLUGIN_VERSION,
    );

    $order = \cointopay\Merchant\Order::createOrFail($cointopay_params,array(),$authentication);

    $form = '<form action="'.$order->shortURL.'" method="GET">';
    $form .='<input type="hidden" value="'.$params['langpaynow'].'">
             <button type="submit" class="btn btn-success btn-sm" id="btnPayNow">
             <i class="fa fa-btc"></i>&nbsp; Pay Now</button>';
    $form .= '</form>';

    return $form;
}