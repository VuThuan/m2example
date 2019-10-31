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

namespace Bdcrops\GiftCard\Model;

use DateInterval;
use DateTime;
use Exception;
use IntlDateFormatter;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Bdcrops\GiftCard\Helper\Data as DataHelper;
use Bdcrops\GiftCard\Helper\Email;
use Bdcrops\GiftCard\Model\GiftCard\Action;
use Bdcrops\GiftCard\Model\GiftCard\Status;
use Bdcrops\GiftCard\Model\Product\DeliveryMethods;
use Bdcrops\GiftCard\Model\ResourceModel\History\Collection;
use Bdcrops\GiftCard\Model\Source\Status as GcStatus;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Twilio\Exceptions\ConfigurationException;

/**
 * Class GiftCard
 * @package Bdcrops\GiftCard\Model
 *
 * @method string getCode()
 * @method string getStatus()
 * @method float getBalance()
 * @method string getCanRedeem()
 * @method string getTimezone()
 * @method string getDeliveryMethod()
 * @method string getDeliveryAddress()
 * @method int getStoreId()
 * @method GiftCard setCode($value)
 * @method GiftCard setTimezone($value)
 * @method GiftCard setDeliveryAddress($value)
 * @method GiftCard setDeliveryMethod($value)
 * @method GiftCard setStoreId($value)
 */
class GiftCard extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'bdcrops_giftcard';

    /**
     * @var string
     */
    protected $_eventPrefix = 'bdcrops_giftcard';

    /**
     * @var string
     */
    protected $_eventObject = 'object';

    /**
     * @var DataHelper
     */
    protected $_helper;

    /**
     * @var HistoryFactory
     */
    protected $_historyFactory;

    /**
     * @type Random
     */
    protected $_mathRandom;

    /**
     * @type CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @type PoolFactory
     */
    protected $_poolFactory;

    /**
     * @var bool Is gc active
     */
    protected $_isActive;

    /**
     * GiftCard constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param DataHelper $dataHelper
     * @param HistoryFactory $historyFactory
     * @param Random $mathRandom
     * @param CustomerFactory $customerFactory
     * @param StoreManagerInterface $storeManager
     * @param PoolFactory $poolFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        DataHelper $dataHelper,
        HistoryFactory $historyFactory,
        Random $mathRandom,
        CustomerFactory $customerFactory,
        StoreManagerInterface $storeManager,
        PoolFactory $poolFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_helper = $dataHelper;
        $this->_historyFactory = $historyFactory;
        $this->_mathRandom = $mathRandom;
        $this->_customerFactory = $customerFactory;
        $this->storeManager = $storeManager;
        $this->_poolFactory = $poolFactory;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Bdcrops\GiftCard\Model\ResourceModel\GiftCard');
    }

    /**
     * @inheritdoc
     */
    public function beforeSave()
    {
        parent::beforeSave();

        if ($this->getBalance() < 0) {
            throw new LocalizedException(__('Balance must be greater than or equal zero.'));
        }

        if ($this->isObjectNew()) {
            $this->setCode($this->generateCode());
            $this->setAction(Action::ACTION_CREATE)->setInitBalance($this->getBalance());
        } else {
            if (!$this->hasAction() &&
                (($this->getData('balance') != $this->getOrigData('balance')) ||
                 ($this->getData('status') != $this->getOrigData('status')))
            ) {
                $this->setAction(Action::ACTION_UPDATE);
            }
        }

        $this->processExpiredDate();
        $this->processStatus();

        if ($this->getData('send_to_recipient')) {
            $this->setData('is_sent', true);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @param $amount
     *
     * @return $this
     */
    public function addBalance($amount)
    {
        $this->setBalance($this->getBalance() + $amount);

        return $this;
    }

    /**
     * @param $amount
     * @param $order
     *
     * @return $this
     * @throws LocalizedException
     * @throws Exception
     */
    public function spentForOrder($amount, $order)
    {
        if (!$this->isActive() || ($this->getBalance() < $amount)) {
            throw new LocalizedException(__('Gift Card balance is not enough'));
        }

        $this->setBalance($this->getBalance() - $amount)
            ->setAction(Action::ACTION_SPEND)
            ->setActionVars(['order_increment_id' => $order->getIncrementId(), 'auth' => $order->getCustomerName()])
            ->save();

        return $this;
    }

    /**
     * Update status for gift card
     *
     * @param $status
     *
     * @return $this
     * @throws Exception
     * @throws LocalizedException
     */
    public function updateStatus($status)
    {
        if (!in_array($status, [Status::STATUS_ACTIVE, Status::STATUS_INACTIVE])) {
            throw new Exception(__('Can only update status to "Active" or "Inactive"'));
        }

        if ($this->getStatus() > Status::STATUS_INACTIVE) {
            throw new LocalizedException(__('Cannot update status for gift code "%1"', $this->getCode()));
        }

        $this->setData('status', $status)
            ->save();

        return $this;
    }

    /**
     * Update multiple status
     *
     * @param $ids
     * @param $status
     *
     * @return $this
     */
    public function updateStatuses($ids, $status)
    {
        if (!empty($ids)) {
            $this->getResource()->updateStatuses($ids, $status);
        }

        return $this;
    }

    /**
     * Get gift card status label
     *
     * @param null $status
     *
     * @return Phrase
     */
    public function getStatusLabel($status = null)
    {
        if (is_null($status)) {
            $status = $this->getStatus();
        }

        $allStatus = Status::getOptionArray();

        return isset($allStatus[$status]) ? $allStatus[$status] : __('Undefined');
    }

    /**
     * @param null $code
     *
     * @return null|string
     */
    public function getHiddenCode($code = null)
    {
        if (is_null($code)) {
            $code = $this->getCode();
        }

        if (!$this->_helper->getGeneralConfig('hidden/enable')) {
            return $code;
        }

        $codeLength = strlen($code);

        $numOfPrefix = (int) $this->_helper->getGeneralConfig('hidden/prefix');
        $numOfSuffix = (int) $this->_helper->getGeneralConfig('hidden/suffix');
        $hiddenChar = (string) $this->_helper->getGeneralConfig('hidden/character') ?: 'X';

        $prefix = $numOfPrefix ? substr($code, 0, $numOfPrefix) : '';
        $suffix = $numOfSuffix ? substr($code, -$numOfSuffix) : '';
        $character = str_repeat($hiddenChar, $codeLength - $numOfPrefix - $numOfSuffix);

        return $prefix . $character . $suffix;
    }

    /**
     * Is active gift card
     *
     * @param null $website
     *
     * @return bool
     * @throws Exception
     */
    public function isActive($website = null)
    {
        if (is_null($this->_isActive)) {
            $this->_isActive = true;

            $website = $website ?: $this->_helper->getWebsiteId();
            $giftCardWebsiteId = $this->storeManager->getStore($this->getStoreId())->getWebsiteId();
            if (!$this->getId() || ($giftCardWebsiteId != $website)) {
                $this->_isActive = false;
            } else {
                $this->processExpiredDate();
                $this->processStatus();

                if ($this->getStatus() != Status::STATUS_ACTIVE) {
                    $this->_isActive = false;
                } elseif ($poolId = $this->getPoolId()) {
                    $pool = $this->_poolFactory->create()->load($poolId);
                    $this->_isActive = $pool->isActive();
                }
            }
        }

        return $this->_isActive;
    }

    /**
     * Load Gift Card by code
     *
     * @param $code
     *
     * @return $this
     */
    public function loadByCode($code)
    {
        return $this->load($code, 'code');
    }

    /**
     * @param $ids
     *
     * @return array|null
     */
    public function loadByIds($ids)
    {
        $collection = $this->getCollection()->addFieldToFilter('giftcard_id', $ids);
        if ($collection->getSize()) {
            return $collection->getData();
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function afterSave()
    {
        parent::afterSave();

        if ($this->getAction()) {
            $this->_historyFactory->create()
                ->setGiftCard($this)
                ->save();

            if (!$this->getData('send_to_recipient') && !$this->isObjectNew()) {
                $this->sendToRecipient(Email::EMAIL_TYPE_UPDATE);
            }
        }

        if ($this->getData('send_to_recipient')) {
            $this->sendToRecipient(Email::EMAIL_TYPE_DELIVERY);
        }

        return $this;
    }

    /**
     * Generate gift code
     *
     * @param null $pattern
     *
     * @return mixed
     * @throws LocalizedException
     */
    public function generateCode($pattern = null)
    {
        if (is_null($pattern)) {
            $pattern = $this->getPattern() ?: $this->_helper->getCodePattern();
        }

        $code = $pattern = strtoupper(str_replace(' ', '', $pattern));

        $attempt = 10;
        do {
            if ($attempt-- <= 0) {
                throw new LocalizedException(__('Unable to generate gift code. Please check the setting and try again.'));
            }

            $patternString = '#\[([0-9]+)([AN]{1,2})\]#';
            if (preg_match($patternString, $pattern)) {
                $code = preg_replace_callback(
                    $patternString,
                    function ($param) {
                        $pool = (strpos($param[2], 'A')) === false ? '' : Random::CHARS_UPPERS;
                        $pool .= (strpos($param[2], 'N')) === false ? '' : Random::CHARS_DIGITS;

                        return $this->_mathRandom->getRandomString($param[1], $pool);
                    },
                    $pattern
                );
            }
        } while ($this->getResource()->checkCodeAvailable($this, $code));

        return $code;
    }

    /**
     * Process expired date
     *
     * @return $this
     * @throws Exception
     */
    protected function processExpiredDate()
    {
        $timezone = $this->_helper->getGiftCardTimeZone($this);
        if ($this->hasExpireAfter() && $this->getExpireAfter()) {
            $datetime = new DateTime(null, $timezone);
            $expiredAfter = min($this->getExpireAfter(), 36500); // 100 years
            $datetime->add(new DateInterval("P{$expiredAfter}D"));

            $this->setExpiredAt($datetime->format('Y-m-d'));
        } elseif ($this->hasExpiredAt() && $this->getExpiredAt()) {
            $expiredAt = new DateTime($this->getExpiredAt());
            $this->setExpiredAt($expiredAt->format('Y-m-d'));

            $expiredAtTimestamp = $expiredAt->setTime(23, 59);
            $nowDayTimestamp = (new DateTime(null, $timezone));

            if (($this->getStatus() == Status::STATUS_ACTIVE) && ($expiredAtTimestamp < $nowDayTimestamp)) {
                $this->setStatus(Status::STATUS_EXPIRED);
            } elseif (($this->getStatus() == Status::STATUS_EXPIRED) && ($expiredAtTimestamp >= $nowDayTimestamp)) {
                $this->setStatus(Status::STATUS_ACTIVE);
            }
        }

        return $this;
    }

    /**
     * Change status depend on active balance
     *
     * @return $this
     */
    protected function processStatus()
    {
        if (!in_array(
            $this->getStatus(),
            [Status::STATUS_PENDING, Status::STATUS_INACTIVE, Status::STATUS_EXPIRED, Status::STATUS_CANCELLED]
        )) {
            if ($this->getBalance() > 0) {
                $this->setStatus(Status::STATUS_ACTIVE);
            } else {
                $this->setStatus(Status::STATUS_USED);
            }
        }

        return $this;
    }

    /**
     * @param null $giftCard
     * @param null $storeId
     *
     * @return bool
     * @throws Exception
     */
    public function canRedeem($giftCard = null, $storeId = null)
    {
        if (is_null($giftCard)) {
            $giftCard = $this;
        }

        $configRedeemable = $this->_helper->allowRedeemGiftCard($storeId);

        return $configRedeemable && $giftCard->isActive() && $giftCard->getCanRedeem();
    }

    /**
     * @param $qty
     *
     * @return array
     * @throws LocalizedException
     */
    public function createMultiple($qty)
    {
        if (!$qty) {
            return [];
        }
        $actionVars = $this->getActionVars();
        $this->beforeSave();

        $giftCodes = $this->getResource()->createMultiple($this, $qty);
        $giftCards = $this->getCollection()->addFieldToFilter('code', ['in' => $giftCodes]);
        if ($giftCards->getSize()) {
            $this->_historyFactory->create()
                ->getResource()
                ->createMultiple($giftCards, $actionVars);
        }

        return $giftCodes;
    }

    /**
     * @param $data
     * @param $condition
     */
    public function updateMultiple($data, $condition)
    {
        $this->getResource()->updateMultiple($data, $condition);
        // todo update history for Gift Card if balance is changed
    }

    /**
     * Get Customer saved gift card
     *
     * @param $customerId
     *
     * @return array
     * @throws Exception
     */
    public function getGiftCardListForCustomer($customerId)
    {
        $giftCardList = [];

        /** @var \Bdcrops\GiftCard\Model\ResourceModel\GiftCard\Collection $giftCards */
        $giftCards = $this->getCollection()
            ->addFieldToFilter('customer_ids', ['finset' => $customerId])
            ->setOrder('status', 'asc')
            ->setOrder('expired_at', 'desc');

        /** @var GiftCard $giftCard */
        foreach ($giftCards as $giftCard) {
            $historyData = [];
            /** @var Collection $histories */
            $histories = $this->_historyFactory->create()
                ->getCollection()
                ->addFieldToFilter('giftcard_id', $giftCard->getId())
                ->setOrder('created_at', 'desc');
            foreach ($histories as $history) {
                $history->addData([
                    'created_at_formatted' => $this->_helper->formatDate(
                        $history->getCreatedAt(),
                        IntlDateFormatter::MEDIUM
                    ),
                    'action_label'         => $history->getActionLabel(),
                    'amount_formatted'     => $this->_helper->convertPrice($history->getAmount()),
                    'status_label'         => $giftCard->getStatusLabel($history->getStatus()),
                    'action_detail'        => Action::getActionLabel($history->getAction(), $history->getExtraContent())
                ]);

                $historyData[] = $history->getData();
            }

            if ($poolId = $giftCard->getPoolId()) {
                $pool = $this->_poolFactory->create()->load($poolId);
                if (($pool->getStatus() == GcStatus::STATUS_INACTIVE) && ($giftCard->getStatus() == Status::STATUS_ACTIVE)) {
                    $giftCard->setStatus(Status::STATUS_INACTIVE);
                }
            }

            $giftCard->addData([
                'expired_at_formatted' => $giftCard->getExpiredAt()
                    ? $this->_helper->formatDate($giftCard->getExpiredAt(), IntlDateFormatter::MEDIUM)
                    : __('Permanent'),
                'status_label'         => $giftCard->getStatusLabel(),
                'balance_formatted'    => $this->_helper->convertPrice($giftCard->getBalance()),
                'can_redeem'           => $this->canRedeem($giftCard),
                'hidden_code'          => $giftCard->getHiddenCode(),
                'histories'            => $historyData
            ]);

            $giftCardList[] = $giftCard->getData();
        }

        return $giftCardList;
    }

    /**
     * Send Email to recipient
     *
     * @param $type
     * @param array $params
     *
     * @return $this
     * @throws Exception
     * @throws Html2PdfException
     * @throws ConfigurationException
     */
    public function sendToRecipient($type, $params = [])
    {
        switch ($this->getDeliveryMethod()) {
            case DeliveryMethods::METHOD_PRINT:
                $params['is_print'] = true;
            // Fall-through to send email
            case DeliveryMethods::METHOD_EMAIL:
                $this->_helper->getEmailHelper()->sendDeliveryEmail($this, $type, $params);
                break;
            case DeliveryMethods::METHOD_SMS:
                $this->_helper->getSmsHelper()->sendSms($this, $type);
                break;
            case DeliveryMethods::METHOD_POST:
                if ($type != Email::EMAIL_TYPE_DELIVERY && $this->getIsSent()) {
                    $order = $this->_helper->getGiftCardOrder($this);
                    if ($order && $order->getId()) {
                        $this->setDeliveryAddress($order->getShippingAddress()->getEmail());
                        $this->_helper->getEmailHelper()->sendDeliveryEmail($this, $type, $params);
                    }
                }
                break;
        };

        if ($type == Email::EMAIL_TYPE_DELIVERY) {
            if ($this->getDeliveryMethod() != DeliveryMethods::METHOD_PRINT) {
                $this->_helper->getEmailHelper()->sendNoticeSenderEmail($this, Email::EMAIL_TYPE_NOTICE_SENDER);
            }

            $this->_historyFactory->create()
                ->setGiftCard($this)
                ->setAction(Action::ACTION_SEND)
                ->save();
        }

        return $this;
    }
}
