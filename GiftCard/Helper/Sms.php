<?php
/**
 * Bdcrops
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Bdcrops.com license that is
 * available through the world-wide-web at this URL:
 * https://www.bdcrops.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Bdcrops
 * @package     Bdcrops_GiftCard
 * @copyright   Copyright (c) Bdcrops (https://www.bdcrops.com/)
 * @license     https://www.bdcrops.com/LICENSE.txt
 */

namespace Bdcrops\GiftCard\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;
use Bdcrops\GiftCard\Model\GiftCard;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Rest\Client;

/**
 * Class Sms
 * @package Bdcrops\GiftCard\Helper
 */
class Sms extends Data
{
    /**
     * @var array
     */
    protected $attributeValue = [];

    /**
     * Sms constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param TimezoneInterface $localeDate
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        TimezoneInterface $localeDate
    ) {
        parent::__construct($context, $objectManager, $storeManager, $localeDate);
    }

    /**
     * @param $giftCard
     * @param $type
     *
     * @return $this
     * @throws ConfigurationException
     */
    public function sendSms($giftCard, $type)
    {
        if (!$this->isSmsEnable($type) || !$giftCard->getDeliveryAddress()) {
            return $this;
        }

        $storeId = $giftCard->getStoreId();

        $sid = $this->getSmsConfig('twilio_account_sid', $storeId);
        $token = $this->getSmsConfig('twilio_account_token', $storeId);
        $sender = $this->getSmsConfig('address_sender', $storeId);

        if (!$sid || !$token || !$sender) {
            return $this;
        }

        $client = new Client($sid, $token);
        $client->messages->create(
            $giftCard->getDeliveryAddress(),
            [
                'from' => $sender,
                'body' => $this->generateMessageContent($giftCard, $type)
            ]
        );

        return $this;
    }

    /**
     * Generate message content
     *
     * @param GiftCard $giftCard
     * @param string $type
     *
     * @return mixed|null
     */
    public function generateMessageContent($giftCard, $type)
    {
        $storeId = $giftCard->getStoreId();
        $message = $this->getSmsConfig($type ? $type . '/content' : 'content', $storeId);

        $patternString = '#\{\{[a-z_]*\}\}#';
        if (preg_match($patternString, $message)) {
            $message = preg_replace_callback(
                $patternString,
                function ($param) use ($giftCard) {
                    return $this->getAttributeValue(trim($param[0], '{}'), $giftCard);
                },
                $message
            );
        }

        return $message;
    }

    /**
     * @param $attribute
     * @param $giftCard
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getAttributeValue($attribute, $giftCard)
    {
        if (!isset($this->attributeValue[$giftCard->getId()])) {
            $templateFields = $giftCard->getTemplateFields() ? $this->jsonDecode($giftCard->getTemplateFields()) : [];
            $store = $this->storeManager->getStore($giftCard->getStoreId());

            $this->attributeValue[$giftCard->getId()] = [
                'sender_name'  => isset($templateFields['sender']) ? $templateFields['sender'] : '',
                'message'      => isset($templateFields['message']) ? $templateFields['message'] : '',
                'code'         => $giftCard->getCode(),
                'balance'      => $this->formatPrice($giftCard->getBalance(), false, $store->getId()),
                'status'       => $giftCard->getStatusLabel(),
                'expired_date' => $this->formatDate($giftCard->getExpiredAt()),
                'store_url'    => $store->getBaseUrl()
            ];
        }

        return isset($this->attributeValue[$giftCard->getId()][$attribute]) ? $this->attributeValue[$giftCard->getId()][$attribute] : '';
    }
}
