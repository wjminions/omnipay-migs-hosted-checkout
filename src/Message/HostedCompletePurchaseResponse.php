<?php

namespace Omnipay\MigsHostedCheckout\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Class HostedCompletePurchaseResponse
 * @package Omnipay\MigsHostedCheckout\Message
 */
class HostedCompletePurchaseResponse extends AbstractResponse
{

    public function isPaid()
    {
        return $this->data['is_paid'];
    }


    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return $this->data['verify_success'];
    }
}
