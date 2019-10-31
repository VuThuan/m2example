<?phpnamespace Bdcrops\Contacts\Model;


use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Contact extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'bdcrops_contacts_contact';

    protected function _construct()
    {
        $this->_init('Bdcrops\Contacts\Model\ResourceModel\Contact');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
