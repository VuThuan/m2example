<?php
/**
 * Magento 2 extensions for Afterpay Payment
 *
 * @author Afterpay
 * @copyright 2016-2019 Afterpay https://www.afterpay.com
 */
namespace Bdcrops\Afterpay\Model\PaymentInformationManagement;

class Plugin
{
    /**
     * @var \Bdcrops\Afterpay\Model\Token
     */
    protected $token;

    /**
     * Plugin constructor.
     * @param \Bdcrops\Afterpay\Model\Token $token
     */
    public function __construct(\Bdcrops\Afterpay\Model\Token $token)
    {
        $this->token = $token;
    }

    /**
     * @param \Magento\Checkout\Model\PaymentInformationManagement $subject
     * @param $returnValue
     * @return string
     */
    public function afterSavePaymentInformationAndPlaceOrder(
        \Magento\Checkout\Model\PaymentInformationManagement $subject,
        $returnValue
    ) {
        return $this->token->saveAndReturnToken($returnValue);
    }
}
