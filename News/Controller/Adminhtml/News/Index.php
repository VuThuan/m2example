<?php
namespace Bdcrops\News\Controller\Adminhtml\News;

class Index extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Bdcrops_News::newss';
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/index/index');
        return $resultRedirect;
    }
}
