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
        return false;
    }


    public function getRedirectUrl()
    {
        return false;
    }


    public function getRedirectMethod()
    {
        return false;
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
        return false;
    }
}
