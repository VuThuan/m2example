<?php
namespace Bdcrops\TicketingSystem\Model;
class Tickets extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'bdcrops_ticketingsystem_ticket';
    protected $_cacheTag = 'bdcrops_ticketingsystem_ticket';
    protected $_eventPrefix = 'bdcrops_ticketingsystem_ticket';
    protected function _construct()
    {
        $this->_init('Bdcrops\TicketingSystem\Model\ResourceModel\Tickets');
    }
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
    public function getDefaultValues()
    {
        $values = [];
        return $values;
    }
}
