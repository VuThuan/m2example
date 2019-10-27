<?php
declare(strict_types=1);
namespace Bdcrops\TicketingSystem\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
/**
 * Class NonRevertable
 * @package Bdcrops\TicketingSystem\Setup\Patch\Data
 */
class NonRevertable implements DataPatchInterface{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;
    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(ModuleDataSetupInterface $moduleDataSetup){
        $this->moduleDataSetup = $moduleDataSetup;
    }
    /**
     * Do Upgrade
     * @return void
     */
    public function apply(){
        $data = ['ticket_id' => 'T20191027001', 'custmer_id' => '1',
        'category' => '1','subject' => 'Order Issue',
        'content' => 'Order Not delivered',
        'status' => '1', 'priority' => '1'
      ];
        $this->moduleDataSetup->getConnection()->insert('bdcrops_ticketing_system', $data);
    }
    /**
     * {@inheritdoc}
     */
    public function getAliases(){
        return [];
    }
    /**
     * {@inheritdoc}
     */
    public static function getDependencies(){
        return [];
    }
}
