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

namespace Bdcrops\GiftCard\Block\Adminhtml\Customer\Edit\Tab\Transaction;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
use Bdcrops\GiftCard\Helper\Data;
use Bdcrops\GiftCard\Model\Transaction\Action;

/**
 * Class DetailRenderer
 * @package Bdcrops\GiftCard\Block\Adminhtml\Customer\Edit\Tab\Transaction
 */
class DetailRenderer extends AbstractRenderer
{
    /**
     * @param DataObject $row
     *
     * @return string
     */
    public function render(DataObject $row)
    {
        $params = is_array($row->getExtraContent()) ? $row->getExtraContent() : Data::jsonDecode($row->getExtraContent());

        return Action::getActionLabel($row->getAction(), $params);
    }
}
