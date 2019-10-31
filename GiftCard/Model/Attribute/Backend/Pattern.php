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

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Bdcrops\GiftCard\Helper\Product;
use Bdcrops\GiftCard\Ui\DataProvider\Product\Modifier\GiftCard;

/**
 * Class Pattern
 * @package Bdcrops\GiftCard\Model\Attribute\Backend
 */
class Pattern extends AbstractClass
{
    /**
     * @inheritdoc
     */
    public function beforeSave($object)
    {
        $attributeCode = $this->getAttribute()->getName();

        if ($object->getData('use_config_' . $attributeCode)) {
            $object->setData($attributeCode, Product::VALUE_USE_CONFIG);
        } elseif ($data = $object->getData($attributeCode)) {
            switch ($attributeCode) {
                case GiftCard::FIELD_GIFT_CODE_PATTERN:
                    $object->setData($attributeCode, strtoupper(str_replace(' ', '', $data)));
                    break;
                case GiftCard::FIELD_EXPIRE_AFTER_DAY:
                    $object->setData($attributeCode, min($data, 36500));
                    break;
                default:
                    break;
            }
        }

        return parent::beforeSave($object);
    }

    /**
     * Validate object
     *
     * @param DataObject $object
     *
     * @return bool
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function validate($object)
    {
        $attributeCode = $this->getAttribute()->getName();

        if ($object->getData('use_config_' . $attributeCode)) {
            return true;
        }

        if ($attributeCode == GiftCard::FIELD_EXPIRE_AFTER_DAY
            && ($value = $object->getData($attributeCode))
            && !$this->isPositiveOrZero($value)
        ) {
            throw new LocalizedException(__(
                'The value of attribute "%1" must be number greater than 0',
                $attributeCode
            ));
        }

        return parent::validate($object);
    }
}
