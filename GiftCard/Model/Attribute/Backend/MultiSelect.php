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

namespace Bdcrops\GiftCard\Model\Attribute\Backend;

/**
 * Class MultiSelect
 * @package Bdcrops\GiftCard\Model\Attribute\Backend
 */
class MultiSelect extends AbstractClass
{
    /**
     * @inheritdoc
     */
    public function beforeSave($object)
    {
        $attributeCode = $this->getAttribute()->getName();
        $data = $object->getData($attributeCode);
        if (!is_array($data)) {
            $data = [];
        }

        $object->setData($attributeCode, implode(',', $data) ?: null);

        return parent::beforeSave($object);
    }
}
