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

namespace Bdcrops\GiftCard\Block\Adminhtml\Pool\Edit\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\View\Element\Template;

/**
 * Class Generate
 * @package Bdcrops\GiftCard\Block\Adminhtml\Pool\Edit\Tab
 */
class Generate extends Template implements TabInterface
{
    /**
     * @var string
     */
    protected $_template = 'Bdcrops_GiftCard::pool/generate.phtml';

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Generate Gift Cards');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Generate Gift Cards');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
