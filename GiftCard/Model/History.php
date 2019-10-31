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

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Bdcrops\GiftCard\Helper\Data as DataHelper;
use Bdcrops\GiftCard\Model\GiftCard\Action;

/**
 * Class History
 * @package Bdcrops\GiftCard\Model
 */
class History extends AbstractModel implements IdentityInterface
{
    /**
     * Cache tag
     */
    const CACHE_TAG = 'bdcrops_giftcard_history';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'giftcard_history';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'object';

    /**
     * @var DataHelper
     */
    protected $_helper;

    /**
     * Gift Card constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param DataHelper $helper
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        DataHelper $helper,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_helper = $helper;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Bdcrops\GiftCard\Model\ResourceModel\History');
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get history action label
     *
     * @param null $action
     *
     * @return Phrase
     */
    public function getActionLabel($action = null)
    {
        if (is_null($action)) {
            $action = $this->getAction();
        }

        $allStatus = Action::getOptionArray();

        return isset($allStatus[$action]) ? $allStatus[$action] : __('Undefined');
    }

    /**
     * @inheritdoc
     */
    public function beforeSave()
    {
        parent::beforeSave();

        $giftCard = $this->getGiftCard();
        $actionVars = $this->getActionVars() ?: $giftCard->getActionVars();

        $this->setData([
            'giftcard_id'   => $giftCard->getId(),
            'code'          => $giftCard->getCode(),
            'action'        => $this->getAction() ?: $giftCard->getAction(),
            'balance'       => $giftCard->getBalance(),
            'amount'        => $giftCard->getData('balance') - $giftCard->getOrigData('balance'),
            'status'        => $giftCard->getStatus(),
            'store_id'      => $giftCard->getStoreId(),
            'extra_content' => is_array($actionVars) ? DataHelper::jsonEncode($actionVars) : $actionVars
        ]);

        return $this;
    }
}
