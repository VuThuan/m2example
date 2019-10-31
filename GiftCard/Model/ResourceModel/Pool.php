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

use Magento\Framework\DataObject;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Bdcrops\GiftCard\Helper\Data;

/**
 * Class Pool
 * @package Bdcrops\GiftCard\Model\ResourceModel
 */
class Pool extends AbstractDb
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('bdcrops_giftcard_pool', 'pool_id');
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

        if ($object->getTemplateFields()) {
            $object->addData(Data::jsonDecode($object->getTemplateFields()));
        }

        return $this;
    }
}
