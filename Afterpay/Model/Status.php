<?php
/**
 * Magento 2 extensions for Afterpay Payment
 *
 * @author Afterpay
 * @copyright 2016-2019 Afterpay https://www.afterpay.com
 */
namespace Bdcrops\Afterpay\Model;

/**
 * Class Status
 * @package Bdcrops\Afterpay\Model
 */
class Status
{
    /**
     * Constant variable to manage status responds
     */
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_PENDING = 'PENDING';
    const STATUS_DECLINED = 'DECLINED';
    const STATUS_FAILED = 'FAILED';
}
