<?php

namespace Omnipay\MigsHostedCheckout;

use Omnipay\Common\AbstractGateway;

/**
 * Class HostedGateway
 * @package Omnipay\MigsHostedCheckout
 */
class HostedGateway extends AbstractGateway
{

    /**
     * Get gateway display name
     *
     * This can be used by carts to get the display name for each gateway.
     */
    public function getName()
    {
        return 'migs_hosted_checkout';
    }


    public function setEnvironment($value)
    {
        return $this->setParameter('environment', $value);
    }


    public function getEnvironment()
    {
        return $this->getParameter('environment');
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


    public function setProxyServer($value)
    {
        return $this->setParameter('proxyServer', $value);
    }


    public function getProxyServer()
    {
        return $this->getParameter('proxyServer');
    }


    public function setProxyAuth($value)
    {
        return $this->setParameter('proxyAuth', $value);
    }


    public function getProxyAuth()
    {
        return $this->getParameter('proxyAuth');
    }


    public function setProxyCurlOption($value)
    {
        return $this->setParameter('proxyCurlOption', $value);
    }


    public function getProxyCurlOption()
    {
        return $this->getParameter('proxyCurlOption');
    }


    public function setProxyCurlValue($value)
    {
        return $this->setParameter('proxyCurlValue', $value);
    }


    public function getProxyCurlValue()
    {
        return $this->getParameter('proxyCurlValue');
    }


    public function setCertificatePath($value)
    {
        return $this->setParameter('certificatePath', $value);
    }


    public function getCertificatePath()
    {
        return $this->getParameter('certificatePath');
    }


    public function setCertificateVerifyPeer($value)
    {
        return $this->setParameter('certificateVerifyPeer', $value);
    }


    public function getCertificateVerifyPeer()
    {
        return $this->getParameter('certificateVerifyPeer');
    }


    public function setCertificateVerifyHost($value)
    {
        return $this->setParameter('certificateVerifyHost', $value);
    }


    public function getCertificateVerifyHost()
    {
        return $this->getParameter('certificateVerifyHost');
    }


    public function setGatewayUrl($value)
    {
        return $this->setParameter('gatewayUrl', $value);
    }


    public function getGatewayUrl()
    {
        return $this->getParameter('gatewayUrl');
    }


    public function setDebug($value)
    {
        return $this->setParameter('debug', $value);
    }


    public function getDebug()
    {
        return $this->getParameter('debug');
    }


    public function setVersion($value)
    {
        return $this->setParameter('version', $value);
    }


    public function getVersion()
    {
        return $this->getParameter('version');
    }


    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }


    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }


    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }


    public function getPassword()
    {
        return $this->getParameter('password');
    }


    public function setApiUsername($value)
    {
        return $this->setParameter('apiUsername', $value);
    }


    public function getApiUsername()
    {
        return $this->getParameter('apiUsername');
    }


    public function setReturnUrl($value)
    {
        return $this->setParameter('return_url', $value);
    }


    public function getReturnUrl()
    {
        return $this->getParameter('return_url');
    }


    public function setCheckoutMethod($value)
    {
        return $this->setParameter('checkout_method', $value);
    }


    public function getCheckoutMethod()
    {
        return $this->getParameter('checkout_method');
    }


    public function setRefundOrder($value)
    {
        return $this->setParameter('refund_order', $value);
    }


    public function getRefundOrder()
    {
        return $this->getParameter('refund_order');
    }


    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\MigsHostedCheckout\Message\HostedPurchaseRequest', $parameters);
    }


    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\MigsHostedCheckout\Message\HostedCompletePurchaseRequest', $parameters);
    }


    public function query(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\MigsHostedCheckout\Message\HostedQueryRequest', $parameters);
    }


    public function refund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\MigsHostedCheckout\Message\HostedRefundRequest', $parameters);
    }


    public function void(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\MigsHostedCheckout\Message\HostedVoidRequest', $parameters);
    }


    public function capture(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\MigsHostedCheckout\Message\HostedCaptureRequest', $parameters);
    }
}
