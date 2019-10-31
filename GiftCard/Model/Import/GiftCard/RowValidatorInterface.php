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

namespace Bdcrops\GiftCard\Model\Import\GiftCard;

use Magento\Framework\Validator\ValidatorInterface;
use Bdcrops\GiftCard\Model\Import\GiftCard;

/**
 * Interface RowValidatorInterface
 * @package Bdcrops\GiftCard\Model\Import\GiftCard
 */
interface RowValidatorInterface extends ValidatorInterface
{
    const ERROR_CODE_IS_EMPTY    = 'codeIsEmpty';
    const ERROR_DUPLICATE_CODE   = 'duplicatedUrlKey';
    const ERROR_INVALID_TEMPLATE = 'invalidTemplate';
    const ERROR_INVALID_STATUS   = 'invalidStatus';
    const ERROR_INVALID_POOL     = 'invalidPool';
    const ERROR_INVALID_WEBSITE  = 'invalidWebsite';
    const ERROR_INVALID_BALANCE  = 'invalidBalance';
    const ERROR_INVALID_REDEEM   = 'invalidRedeem';
    const VALUE_ALL              = 'all'; #Value that means all entities (e.g. websites, groups etc.)

    /**
     * Initialize validator
     *
     * @param GiftCard $context
     *
     * @return $this
     */
    public function init($context);
}
