<?php

namespace Omnipay\MigsHostedCheckout\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Class HostedCompletePurchaseResponse
 * @package Omnipay\MigsHostedCheckout\Message
 */
class HostedRefundResponse extends AbstractResponse
{
    public function isRedirect()
    {
        return false;
    }


    public function getRedirectMethod()
    {
        return 'POST';
    }


    public function getRedirectUrl()
    {
        return false;
    }


    public function getRedirectHtml()
    {
        return false;
    }


    public function getTransactionNo()
    {
        return isset($this->data['transaction.receipt']) ? $this->data['transaction.receipt'] : '';
    }


    public function isPaid()
    {
        if ($this->data['is_paid']) {
            return true;
        }

        return false;
    }


    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        if ($this->data['result'] == 'SUCCESS') {
            return true;
        }

        return false;
    }

    public function getMessage()
    {
        return isset($this->data['error.explanation']) ? $this->data['error.explanation'] : $this->data['result'];
    }
}
