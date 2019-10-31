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
use Bdcrops\GiftCard\Model\Source\Status;
use Zend_Db_Expr;

/**
 * Class Pool
 * @package Bdcrops\GiftCard\Model
 */
class Pool extends AbstractModel implements IdentityInterface
{
    /**
     * Cache tag
     */
    const CACHE_TAG = 'bdcrops_giftcard_pool';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'bdcrops_giftcard_pool';

    /**
     * @var DataHelper
     */
    protected $_helper;

    /**
     * @var GiftCardFactory
     */
    protected $giftCardFactory;

    /**
     * Pool constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param DataHelper $helper
     * @param GiftCardFactory $giftCardFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        DataHelper $helper,
        GiftCardFactory $giftCardFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_helper = $helper;
        $this->giftCardFactory = $giftCardFactory;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Bdcrops\GiftCard\Model\ResourceModel\Pool');
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave()
    {
        parent::beforeSave();

        if ($this->getId() && $this->getCanInherit()) {
            $dataUpdate = [
                'can_redeem'      => $this->getCanRedeem(),
                'store_id'        => $this->getStoreId(),
                'template_id'     => $this->getTemplateId(),
                'image'           => $this->getImage(),
                'expired_at'      => $this->getExpiredAt()
                    ? date('Y-m-d', strtotime($this->getExpiredAt()))
                    : null,
                'template_fields' => $this->getTemplateFields()
            ];
            $balanceChange = $this->getBalance() - $this->getOrigData('balance');
            if ($balanceChange != 0) {
                $sqlVar = '(`balance` + ' . $balanceChange . ')';
                $dataUpdate['balance'] = new Zend_Db_Expr("(CASE WHEN ({$sqlVar} < 0) THEN 0 ELSE {$sqlVar} END)");
            }

            $this->giftCardFactory->create()->updateMultiple($dataUpdate, ['pool_id = ?' => $this->getId()]);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->getStatus() == Status::STATUS_ACTIVE;
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
}
