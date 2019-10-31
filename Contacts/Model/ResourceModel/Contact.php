
<?php
namespace Bdcrops\Contacts\Model\ResourceModel;

class Contact extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('bdcrops_contacts_contact','bdcrops_contacts_contact_id');
    }
}
