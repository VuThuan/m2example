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

namespace Bdcrops\GiftCard\Controller\Adminhtml\Code;

use Bdcrops\GiftCard\Controller\Adminhtml\Code;

/**
 * Class NewAction
 * @package Bdcrops\GiftCard\Controller\Adminhtml\Code
 */
class NewAction extends Code
{
    /**
     * Forward to edit form
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
