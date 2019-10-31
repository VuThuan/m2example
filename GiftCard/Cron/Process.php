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

namespace Bdcrops\GiftCard\Cron;

use DateTimeZone;
use Exception;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Website;
use Bdcrops\GiftCard\Helper\Data;
use Bdcrops\GiftCard\Model\GiftCard;
use Bdcrops\GiftCard\Model\GiftCard\Status;
use Bdcrops\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Process
 * @package Bdcrops\GiftCard\Cron
 */
class Process
{
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Resource
     */
    protected $_resource;

    /**
     * @var DateTime
     */
    protected $_dateTime;

    /**
     * @var TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var AdapterInterface
     */
    protected $_connection;

    /**
     * @var CollectionFactory
     */
    protected $_collection;

    /**
     * @var Data
     */
    protected $_dataHelper;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * Process constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param ResourceConnection $resource
     * @param DateTime $dateTime
     * @param TimezoneInterface $localeDate
     * @param CollectionFactory $collectionFactory
     * @param Data $dataHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ResourceConnection $resource,
        DateTime $dateTime,
        TimezoneInterface $localeDate,
        CollectionFactory $collectionFactory,
        Data $dataHelper,
        LoggerInterface $logger
    ) {
        $this->_storeManager = $storeManager;
        $this->_resource = $resource;
        $this->_dateTime = $dateTime;
        $this->_localeDate = $localeDate;
        $this->_collection = $collectionFactory;
        $this->_dataHelper = $dataHelper;
        $this->_logger = $logger;
    }

    /**
     * process gift card status & email
     */
    public function execute()
    {
        $this->expireGiftCard()
            ->sendToRecipient();
    }

    /**
     * Expire Gift Card depend on the website timezone
     *
     * @return $this
     */
    public function expireGiftCard()
    {
        $connection = $this->_getConnection();

        /** @var Website $website */
        foreach ($this->_storeManager->getWebsites(true) as $website) {
            $timestamp = $this->_localeDate->scopeTimeStamp($website->getDefaultStore());
            $currDate = $this->_dateTime->formatDate($timestamp, false);
            $currDateExpr = $connection->quote($currDate);

            // timestamp is locale based
            if (date('H', $timestamp) == '00') {
                $where = [
                    'status'          => Status::STATUS_ACTIVE,
                    'store_id IN (?)' => $website->getStoreIds(),
                    'expired_at < ?'  => $connection->getDateFormatSql($currDateExpr, '%Y-%m-%d')
                ];
                $connection->update(
                    $this->_resource->getTableName('bdcrops_giftcard'),
                    ['status' => Status::STATUS_EXPIRED],
                    $where
                );
            }
        }

        return $this;
    }

    /**
     * Send Gift Card To Recipient
     *
     * @return $this
     */
    public function sendToRecipient()
    {
        $now = date('Y-m-d', strtotime('+1 day'));
        $collection = $this->_collection->create()
            ->addFieldToFilter('status', Status::STATUS_ACTIVE)
            ->addFieldToFilter('is_sent', 0)
            ->addFieldToFilter('delivery_date', ['notnull' => true])
            ->addFieldToFilter('delivery_date', ['lteq' => $now])
            ->setPageSize(100)
            ->setCurPage(1);

        /** @var GiftCard $giftCard */
        foreach ($collection as $giftCard) {
            $timezone = $giftCard->getTimezone()
                ? new DateTimeZone($giftCard->getTimezone())
                : $this->_dataHelper->getGiftCardTimeZone($giftCard);
            $currentDate = (new \DateTime(null, $timezone))->format('Y-m-d');
            $deliveryDate = (new \DateTime($giftCard->getDeliveryDate()))->format('Y-m-d');
            if ($deliveryDate == $currentDate) {
                try {
                    $giftCard->setData('send_to_recipient', true)
                        ->save();
                } catch (Exception $e) {
                    $this->_logger->critical($e);
                }
            }
        }

        return $this;
    }

    /**
     * Retrieve write connection instance
     *
     * @return bool|AdapterInterface
     */
    protected function _getConnection()
    {
        if (null === $this->_connection) {
            $this->_connection = $this->_resource->getConnection();
        }

        return $this->_connection;
    }
}
