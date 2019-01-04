<?php

namespace Omnipay\MigsHostedCheckout\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\MigsHostedCheckout\Helper;

/**
 * Class HostedPurchaseResponse
 * @package Omnipay\MigsHostedCheckout\Message
 */
class HostedPurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{

    public function isSuccessful()
    {
        return true;
    }


    public function isRedirect()
    {
        return true;
    }


    public function getRedirectUrl()
    {
        return false;
    }


    public function getRedirectMethod()
    {
        return 'POST';
    }


    public function getRedirectData()
    {
        return false;
    }


    public function getMessage()
    {
        return $this->data;
    }


    public function getRedirectHtml()
    {
        $data = $this->data;

        if (isset($_SESSION['successIndicator']) && isset( $_SESSION['orderID'])) {
            $orderID = $_SESSION['orderID'];

            $request_assoc_array = array("apiOperation"=>"RETRIEVE_ORDER",
                                         "order.id"=>$orderID
            );

            $request = Helper::ParseRequest($data, $request_assoc_array);
            $response = Helper::SendTransaction($data['gatewayUrl'], $data, $request);

            $parsed_array = Helper::parse_from_nvp($response);

            if ($parsed_array['result'] === "SUCCESS" && $parsed_array['status'] === "AUTHORIZED" && $parsed_array['transaction[0].authorizationResponse.responseCode'] === "00") {
                header("Location: " . $data['return_url'] . "?resultIndicator=" . $_SESSION['successIndicator']);
                die('Already paid');
            }
        }

        $order_amount           = $data["amount"];
        $order_currency         = $data["currency"];

        //Use a method to create a unique Order ID. Store this for later use in the receipt page or receipt function.

        //Form the array to obtain the checkout session ID.
        $request_assoc_array = array("apiOperation"   => "CREATE_CHECKOUT_SESSION",
                                     "order.id"       => $data['order_id'],
                                     "order.amount"   => $order_amount,
                                     "order.currency" => $order_currency,
                                     'interaction.action.bypass3DSecure' => true,
                                     'risk.bypassMerchantRiskRules' => 'ALL'
        );

        //This builds the request adding in the merchant name, api user and password.
        $request = Helper::ParseRequest($data, $request_assoc_array);

        //Submit the transaction request to the payment server
        $response = Helper::SendTransaction($data['gatewayUrl'], $data, $request);

        //Parse the response
        $parsed_array = Helper::parse_from_nvp($response);

        if (isset($parsed_array['error.cause'])) {
            die($parsed_array['error.explanation']);
        }

        //Store the successIndicator for later use in the receipt page or receipt function.
        $successIndicator = $parsed_array['successIndicator'];

        //The session ID is passed to the Checkout.configure() function below.
        $sessionId = $parsed_array['session.id'];

        //Store the variables in the session, or a database could be used for example
        $_SESSION['successIndicator'] = $successIndicator;
        $_SESSION['orderID']          = $data['order_id'];

        $merchantID = $data['merchantId'];

        $checkout_method = 'Checkout.showLightbox();';
        if ($data['checkout_method'] == 'showPaymentPage') {
            $checkout_method = 'Checkout.showPaymentPage();';
        }

        $html = <<<eot
<html>

<head>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0;" name="viewport" />
    <script src="https://fdhk.gateway.mastercard.com/checkout/version/42/checkout.js"
            data-error="errorCallback"
            data-cancel="cancelCallback"
            data-complete="{$data['return_url']}"
        >
    </script>
    <!-data-complete="{$data['return_url']}"-->
    <script type="text/javascript">
        function errorCallback(error) {
            alert(JSON.stringify(error));
        }

        function completeCallback(resultIndicator, sessionVersion) {
            alert("Result Indicator");
            alert(JSON.stringify(resultIndicator));
            alert("Session Version:");
            alert(JSON.stringify(sessionVersion));
            alert("Successful Payment");
        }

        function cancelCallback() {
            alert('Payment cancelled');

        }


        Checkout.configure({
            merchant: '{$merchantID}',
            order: {
                amount: "{$order_amount}",
                currency: '{$order_currency}',
                description: '{$order_amount}{$order_currency}',
                id: '{$data['order_id']}',
                item: {
                    brand: 'Mastercard',
                    description: '',
                    name: 'HostedCheckoutItem',
                    quantity: '1',
                    unitPrice: '{$order_amount}',
                    unitTaxAmount: '{$order_amount}'
                }
            },
            interaction: {
                merchant: {
                    name: '{$data['merchant_name']}',
                    address: {
                        line1: ''
                    },
                    logo: '{$data['merchant_logo']}'
                }
            },
            session: {
                id: '{$sessionId}'
            }
        });

    </script>
    <script>
    window.onload=function()
    {
       {$checkout_method}
    }
    </script>
</head>

<body>
<p style="text-align:center;">
    <img src="https://c.ap1.content.force.com/servlet/servlet.ImageServer?id=01590000008h62j&oid=00D90000000sUDO" alt="Main Order Home Page"/>
</p>

<br><br><br><br>

<h1 align="center"> Please Wait for a moment</h1>

<h2 align="center"><u>Order Summary</u></h2>

<p style="text-align:center;"><strong> Order Amount : {$order_amount}</p>

<p style="text-align:center;">
    Currency&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp: {$order_currency}</strong> </p>
<br>

<!-- Note in reality only one of the following functions will be called -->
<!--<p style="text-align:center;">
    <input type="button" value="Pay with Lightbox" onclick="Checkout.showLightbox();"/>
</p>

<p style="text-align:center;">
    <input type="button" value="Pay with Payment Page" onclick="Checkout.showPaymentPage();"/>
</p>-->
</body>
</html>
eot;

        return $html;
    }
}
