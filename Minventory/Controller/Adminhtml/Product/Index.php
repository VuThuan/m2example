<?php
namespace Bdcrops\Minventory\Controller\Adminhtml\Product;

use \Magento\Framework\Controller\ResultFactory;

class Index extends \Bdcrops\Minventory\Controller\Adminhtml\Product{
    public function execute(){
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend((__('Micro Inventory')));
        return $resultPage;
    }
}


// use Magento\Framework\Controller\ResultFactory;
//
// class Index extends \Magento\Backend\App\Action {
//     public function execute() {
//         return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
//     }
// }
