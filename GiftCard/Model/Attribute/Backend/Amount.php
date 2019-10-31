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

use Exception;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Bdcrops\GiftCard\Helper\Data;

/**
 * Class Amount
 * @package Bdcrops\GiftCard\Model\Attribute\Backend
 */
class Amount extends AbstractClass
{
    /**
     * @inheritdoc
     */
    public function beforeSave($object)
    {
        $attributeCode = $this->getAttribute()->getName();
        $priceRows = $object->getData($attributeCode);
        if (!is_null($priceRows)) {
            $amounts = [];
            $priceRows = array_filter((array) $priceRows);
            foreach ($priceRows as $key => $data) {
                if (!isset($data['delete']) || (isset($data['delete']) && !$data['delete'])) {
                    $amounts[] = $data;
                }
            }

            $object->setData($attributeCode, Data::jsonEncode($amounts));

            if ($object->getData('allow_amount_range')) {
                $minAmount = $object->getData('min_amount') ?: 0;
                $object->setData('min_amount', max(0, (float) $minAmount));

                if ($maxAmount = $object->getData('max_amount')) {
                    $object->setData('max_amount', max($object->getData('min_amount'), (float) $maxAmount));
                }

                $priceRate = $object->getData('price_rate') ?: 100;
                $object->setData('price_rate', max(0, min(100, (float) $priceRate)));
            }
        }

        return parent::beforeSave($object);
    }

    /**
     * Assign gift card amounts to product data
     *
     * @param Product $object
     *
     * @return $this
     */
    public function afterLoad($object)
    {
        $attributeCode = $this->getAttribute()->getName();
        $data = $object->getData($attributeCode);

        if (is_string($data)) {
            try {
                $data = Data::jsonDecode($data);
                $object->setData($attributeCode, $data);
            } catch (Exception $e) {
                $object->setData($attributeCode, []);
            }
        }

        return $this;
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
        $priceRows = $object->getData($attributeCode);
        if (!is_array($priceRows) || empty($priceRows)) {
            if (!$object->getData('allow_amount_range')) {
                throw new LocalizedException(__('Please setup Gift Card amount.'));
            }

            return true;
        }

        foreach ($priceRows as $priceRow) {
            if (!empty($priceRow['delete'])) {
                continue;
            }

            if (!$this->isPositiveOrZero($priceRow['price']) || !$this->isPositiveOrZero($priceRow['amount'])) {
                throw new LocalizedException(__('Group amount must be number greater than 0'));
            }
        }

        return parent::validate($object);
    }
}
