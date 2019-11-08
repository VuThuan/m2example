<?php

namespace BDC\OrderNotes\Setup;
use \Magento\Framework\Setup\InstallSchemaInterface;

class InstallSchema implements InstallSchemaInterface{
    public function install(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $connection = $setup->getConnection();
        $connection->addColumn(
            $setup->getTable('quote'),
            'order_notes', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Order Notes'
            ]
        );

        $connection->addColumn(
            $setup->getTable('sales_order'),
            'order_notes', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Order Notes'
            ]
        );
    }
}
