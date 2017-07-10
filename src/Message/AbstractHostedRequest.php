<?php

namespace Omnipay\MigsHostedCheckout\Message;

use Omnipay\Common\Message\AbstractRequest;
use Omnipay\MigsHostedCheckout\Helper;

/**
 * Class AbstractHostedRequest
 * @package Omnipay\MigsHostedCheckout\Message
 */
abstract class AbstractHostedRequest extends AbstractRequest
{

    protected $sandboxEndpoint = 'https://sandbox.itunes.Hosted.com/';

    protected $productionEndpoint = 'https://buy.itunes.Hosted.com/';

    protected $methods = array (
        'query' => 'verifyReceipt',
    );


    public function getEndpoint($type)
    {
        if ($this->getEnvironment() == 'production') {
            return $this->productionEndpoint . $this->methods[$type];
        } else {
            return $this->sandboxEndpoint . $this->methods[$type];
        }
    }


    public function getEnvironment()
    {
        return $this->getParameter('environment');
    }


    public function setEnvironment($value)
    {
        return $this->setParameter('environment', $value);
    }


    public function setOrderId($value)
    {
        return $this->setParameter('order_id', $value);
    }


    public function getOrderId()
    {
        return $this->getParameter('order_id');
    }


    public function setAmount($value)
    {
        return $this->setParameter('amount', $value);
    }


    public function getAmount()
    {
        return $this->getParameter('amount');
    }
}
