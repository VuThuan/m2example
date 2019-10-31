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

namespace Bdcrops\GiftCard\Model\ResourceModel;

use Exception;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Model\StoreManagerInterface;
use Bdcrops\GiftCard\Helper\Data;

/**
 * Class GiftCard
 * @package Bdcrops\GiftCard\Model\ResourceModel
 */
class GiftCard extends AbstractDb
{
    /**
     * @type StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Constructor
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        $connectionName = null
    ) {
        $this->_storeManager = $storeManager;

        parent::__construct($context, $connectionName);
    }

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('bdcrops_giftcard', 'giftcard_id');
    }

    /**
     * @param AbstractModel $object
     *
     * @return $this
     */
    protected function _beforeSave(AbstractModel $object)
    {
        parent::_beforeSave($object);

        $templateFields = $object->getTemplateFields();
        $extraContent = $object->getExtraContent();

        if (is_array($templateFields)) {
            $object->setTemplateFields(Data::jsonEncode($templateFields));
        }

        if (is_array($extraContent)) {
            $object->setExtraContent(Data::jsonEncode($extraContent));
        }

        return $this;
    }

    /**
     * Check for unique values existence
     *
     * @param $object
     * @param $code
     *
     * @return bool
     * @throws LocalizedException
     */
    public function checkCodeAvailable($object, $code)
    {
        $select = $this->getConnection()->select()->from($this->getMainTable());
        $select->where('code=?', trim($code));
        if ($object->getId() || (string) $object->getId() === '0') {
            $select->where($this->getIdFieldName() . '!=?', $object->getId());
        }
        $test = $this->getConnection()->fetchRow($select);

        return (boolean) $test;
    }

    /**
     * @param $ids
     * @param $status
     *
     * @return $this
     * @throws LocalizedException
     */
    public function updateStatuses($ids, $status)
    {
        $this->getConnection()->update(
            $this->getMainTable(),
            ['status' => $status],
            [$this->getIdFieldName() . ' IN (?)' => $ids]
        );

        return $this;
    }

    /**
     * Perform actions after object load
     *
     * @param AbstractModel|DataObject $object
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _afterLoad(AbstractModel $object)
    {
        parent::_afterLoad($object);

        $templateFields = $object->getTemplateFields();
        $extraContent = $object->getExtraContent();

        if ($templateFields) {
            $object->addData(Data::jsonDecode($templateFields));
        }

        if ($extraContent) {
            $object->setExtraContent(Data::jsonDecode($extraContent));
        }

        return $this;
    }

    /**
     * @param $giftCard
     * @param $qty
     *
     * @return array
     * @throws LocalizedException
     */
    public function createMultiple($giftCard, $qty)
    {
        $resultData = [];
        $data = [
            'init_balance'    => $giftCard->getBalance(),
            'balance'         => $giftCard->getBalance(),
            'status'          => $giftCard->getStatus(),
            'can_redeem'      => $giftCard->getCanRedeem(),
            'store_id'        => $giftCard->getStoreId(),
            'pool_id'         => $giftCard->getPoolId(),
            'template_id'     => $giftCard->getTemplateId(),
            'image'           => $giftCard->getImage(),
            'template_fields' => $giftCard->getTemplateFields(),
            'expired_at'      => $giftCard->getExpiredAt()
        ];

        while ($qty--) {
            $data['code'] = $giftCard->generateCode($giftCard->getPattern());
            $resultData[] = $data;
        }

        $this->getConnection()->insertMultiple($this->getMainTable(), $resultData);

        return array_column($resultData, 'code');
    }

    /**
     * @param $data
     * @param $where
     *
     * @return $this
     * @throws LocalizedException
     */
    public function updateMultiple($data, $where)
    {
        $this->getConnection()->update($this->getMainTable(), $data, $where);

        return $this;
    }

    /**
     * Generate multi Gift Card transaction
     *
     * @param array $objects
     *
     * @return $this
     * @throws Exception
     */
    function generateMultiGiftCard($objects)
    {
        $this->beginTransaction();

        try {
            /** @type \Bdcrops\GiftCard\Model\GiftCard $object */
            foreach ($objects as $object) {
                $object->save();
            }

            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }

        return $this;
    }
}
