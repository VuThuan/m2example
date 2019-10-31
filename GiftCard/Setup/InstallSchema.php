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

namespace Bdcrops\GiftCard\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class InstallSchema
 * @package Bdcrops\GiftCard\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * install tables
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $connection = $installer->getConnection();

        /** Table bdcrops_giftcard_pool save all code pools information */
        if (!$installer->tableExists('bdcrops_giftcard_pool')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('bdcrops_giftcard_pool'))
                ->addColumn('pool_id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true
                ], 'Code Pool Id')
                ->addColumn('name', Table::TYPE_TEXT, 255, ['nullable' => false], 'Name')
                ->addColumn('status', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => '0'], 'Status')
                ->addColumn(
                    'can_inherit',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => '1'],
                    'Can Inherit'
                )
                ->addColumn('pattern', Table::TYPE_TEXT, 255, ['nullable' => false], 'Code Pattern')
                ->addColumn(
                    'balance',
                    Table::TYPE_DECIMAL,
                    [12, 4],
                    ['nullable' => false, 'default' => 0.0000],
                    'Balance'
                )
                ->addColumn(
                    'can_redeem',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => '1'],
                    'Can Redeem'
                )
                ->addColumn(
                    'store_id',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => '0', 'unsigned' => true],
                    'Website Id'
                )
                ->addColumn(
                    'template_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true, 'unsigned' => true],
                    'Template Id'
                )
                ->addColumn('image', Table::TYPE_TEXT, 255, [], 'Template Image')
                ->addColumn('template_fields', Table::TYPE_TEXT, '2M', [], 'Template Fields')
                ->addColumn('expired_at', Table::TYPE_DATETIME, null, [], 'Expired At')
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    'Created At'
                )
                ->addForeignKey(
                    $installer->getFkName('bdcrops_giftcard_pool', 'store_id', 'store', 'store_id'),
                    'store_id',
                    $installer->getTable('store'),
                    'store_id',
                    Table::ACTION_SET_DEFAULT
                )->setComment('Gift Card Pool Table');
            $connection->createTable($table);
        }

        /** Table bdcrops_giftcard_template save template information */
        if (!$installer->tableExists('bdcrops_giftcard_template')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('bdcrops_giftcard_template'))
                ->addColumn('template_id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true
                ], 'Gift Card Template Id')
                ->addColumn('name', Table::TYPE_TEXT, 255, ['nullable' => false], 'Name')
                ->addColumn('status', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => '1'], 'Status')
                ->addColumn(
                    'can_upload',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => '1'],
                    'Can Upload Image'
                )
                ->addColumn('title', Table::TYPE_TEXT, 255, ['nullable' => false, 'default' => 'Gift Card'], 'Title')
                ->addColumn(
                    'font_family',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false, 'default' => 'Arial'],
                    'Font Familly'
                )
                ->addColumn('background_image', Table::TYPE_TEXT, 255, [], 'Background Image')
                ->addColumn('design', Table::TYPE_TEXT, '2M', [], 'Design For Template (Json)')
                ->addColumn('note', Table::TYPE_TEXT, '2M', [], 'Note')
                ->addColumn('images', Table::TYPE_TEXT, '2M', [], 'Images (Json)')
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    'Created At'
                )
                ->setComment('Gift Card Template Table');
            $connection->createTable($table);
        }

        /** Table bdcrops_giftcard save all codes information */
        if (!$installer->tableExists('bdcrops_giftcard')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('bdcrops_giftcard'))
                ->addColumn('giftcard_id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true
                ], 'Gift Card Id')
                ->addColumn('code', Table::TYPE_TEXT, 255, ['nullable' => false], 'Code')
                ->addColumn(
                    'init_balance',
                    Table::TYPE_DECIMAL,
                    [12, 4],
                    ['nullable' => false, 'default' => 0.0000],
                    'Initial Balance'
                )
                ->addColumn(
                    'balance',
                    Table::TYPE_DECIMAL,
                    [12, 4],
                    ['nullable' => false, 'default' => 0.0000],
                    'Balance'
                )
                ->addColumn('status', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => '0'], 'Status')
                ->addColumn(
                    'can_redeem',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => '1'],
                    'Can Redeem'
                )
                ->addColumn(
                    'store_id',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => '0', 'unsigned' => true],
                    'Store Id'
                )
                ->addColumn('pool_id', Table::TYPE_INTEGER, null, ['nullable' => true, 'unsigned' => true], 'Pool Id')
                ->addColumn(
                    'template_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true, 'unsigned' => true],
                    'Template Id'
                )
                ->addColumn('image', Table::TYPE_TEXT, 255, [], 'Template Image')
                ->addColumn('template_fields', Table::TYPE_TEXT, '2M', [], 'Template Fields')
                ->addColumn('customer_ids', Table::TYPE_TEXT, 255, [], 'Customer Save Gift Card')
                ->addColumn('order_item_id', Table::TYPE_INTEGER, null, ['unsigned' => true], 'Order Item Id')
                ->addColumn('order_increment_id', Table::TYPE_TEXT, 32, [], 'Order Increment Id')
                ->addColumn('delivery_method', Table::TYPE_SMALLINT, null, [], 'Delivery Method')
                ->addColumn('delivery_address', Table::TYPE_TEXT, '2M', [], 'Delivery Address')
                ->addColumn('is_sent', Table::TYPE_SMALLINT, 5, ['default' => 0], 'Is Sent Gift Card')
                ->addColumn('delivery_date', Table::TYPE_DATE, null, [], 'Delivery Date')
                ->addColumn('timezone', Table::TYPE_TEXT, 31, [], 'Timezone')
                ->addColumn('extra_content', Table::TYPE_TEXT, '2M', [], 'Extra Content')
                ->addColumn('expired_at', Table::TYPE_DATETIME, null, [], 'Expired At')
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    'Created At'
                )
                ->addIndex(
                    $installer->getIdxName('bdcrops_giftcard', ['code'], AdapterInterface::INDEX_TYPE_UNIQUE),
                    ['code'],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                )->addForeignKey(
                    $installer->getFkName('bdcrops_giftcard', 'store_id', 'store', 'store_id'),
                    'store_id',
                    $installer->getTable('store'),
                    'store_id',
                    Table::ACTION_SET_DEFAULT
                )->setComment('Gift Card Code Table');
            $connection->createTable($table);
        }

        /** Table bdcrops_giftcard_history save gift code history */
        if (!$installer->tableExists('bdcrops_giftcard_history')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('bdcrops_giftcard_history'))
                ->addColumn('history_id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true
                ], 'History Id')
                ->addColumn(
                    'giftcard_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Code Id'
                )
                ->addColumn('code', Table::TYPE_TEXT, 255, ['nullable' => false], 'Code')
                ->addColumn('action', Table::TYPE_TEXT, '64', [], 'Action')
                ->addColumn(
                    'balance',
                    Table::TYPE_DECIMAL,
                    [12, 4],
                    ['nullable' => false, 'default' => 0.0000],
                    'Initial Balance'
                )
                ->addColumn(
                    'amount',
                    Table::TYPE_DECIMAL,
                    [12, 4],
                    ['nullable' => false, 'default' => 0.0000],
                    'Amount change'
                )
                ->addColumn('status', Table::TYPE_SMALLINT, null, [], 'Status')
                ->addColumn(
                    'store_id',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => '0', 'unsigned' => true],
                    'Store Id'
                )
                ->addColumn('extra_content', Table::TYPE_TEXT, '2M', [], 'Extra Content')
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    'Created At'
                )
                ->addForeignKey(
                    $installer->getFkName(
                        'bdcrops_giftcard_history',
                        'giftcard_id',
                        'bdcrops_giftcard',
                        'giftcard_id'
                    ),
                    'giftcard_id',
                    $installer->getTable('bdcrops_giftcard'),
                    'giftcard_id',
                    Table::ACTION_CASCADE
                )
                ->setComment('Gift Card History Table');
            $connection->createTable($table);
        }

        /** Table bdcrops_giftcard_credit save customer credit */
        if (!$installer->tableExists('bdcrops_giftcard_credit')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('bdcrops_giftcard_credit'))
                ->addColumn('credit_id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true
                ], 'Credit Id')
                ->addColumn(
                    'customer_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Customer Id'
                )
                ->addColumn(
                    'balance',
                    Table::TYPE_DECIMAL,
                    [12, 4],
                    ['nullable' => false, 'default' => 0.0000],
                    'Balance'
                )
                ->addColumn('credit_notification', Table::TYPE_SMALLINT, null, [], 'Email Update Balance Notification')
                ->addColumn(
                    'giftcard_notification',
                    Table::TYPE_SMALLINT,
                    null,
                    [],
                    'Email Gift Card Update Notification'
                )
                ->addForeignKey(
                    $installer->getFkName('bdcrops_giftcard_credit', 'customer_id', 'customer_entity', 'entity_id'),
                    'customer_id',
                    $installer->getTable('customer_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE
                )
                ->setComment('Gift Card Customer Credit');
            $connection->createTable($table);
        }

        /** Table bdcrops_giftcard_transaction save customer credit transaction */
        if (!$installer->tableExists('bdcrops_giftcard_transaction')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('bdcrops_giftcard_transaction'))
                ->addColumn('transaction_id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true
                ], 'Transaction Id')
                ->addColumn(
                    'credit_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Customer Id'
                )
                ->addColumn('action', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => '0'], 'Action')
                ->addColumn(
                    'balance',
                    Table::TYPE_DECIMAL,
                    [12, 4],
                    ['nullable' => false, 'default' => 0.0000],
                    'Balance'
                )
                ->addColumn(
                    'amount',
                    Table::TYPE_DECIMAL,
                    [12, 4],
                    ['nullable' => false, 'default' => 0.0000],
                    'Amount'
                )
                ->addColumn(
                    'website_id',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Website Id'
                )
                ->addColumn(
                    'extra_content',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false, 'default' => ''],
                    'Extra Content'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    'Created At'
                )
                ->addForeignKey(
                    $installer->getFkName(
                        'bdcrops_giftcard_transaction',
                        'credit_id',
                        'bdcrops_giftcard_credit',
                        'credit_id'
                    ),
                    'credit_id',
                    $installer->getTable('bdcrops_giftcard_credit'),
                    'credit_id',
                    Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $installer->getFkName('bdcrops_giftcard_credit', 'website_id', 'store_website', 'website_id'),
                    'website_id',
                    $installer->getTable('store_website'),
                    'website_id',
                    Table::ACTION_CASCADE
                )
                ->setComment('Gift Card Customer Credit Transaction');
            $connection->createTable($table);
        }

        $installer->endSetup();
    }
}
