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

namespace Bdcrops\GiftCard\Controller\Adminhtml\Pool;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;
use Bdcrops\GiftCard\Controller\Adminhtml\Pool;
use Bdcrops\GiftCard\Helper\Data;
use Bdcrops\GiftCard\Helper\Data as DataHelper;
use Bdcrops\GiftCard\Model\GiftCardFactory;
use Bdcrops\GiftCard\Model\PoolFactory;
use Bdcrops\GiftCard\Model\TransactionFactory;

/**
 * Class Save
 * @package Bdcrops\GiftCard\Controller\Adminhtml\Pool
 */
class Save extends Pool
{
    /**
     * @type GiftCardFactory
     */
    protected $_giftCardFactory;

    /**
     * @type TransactionFactory
     */
    protected $_transactionFactory;

    /**
     * @type DataHelper
     */
    protected $_dataHelper;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param PoolFactory $poolFactory
     * @param GiftCardFactory $giftCardFactory
     * @param TransactionFactory $transactionFactory
     * @param DataHelper $dataHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        PoolFactory $poolFactory,
        GiftCardFactory $giftCardFactory,
        TransactionFactory $transactionFactory,
        DataHelper $dataHelper
    ) {
        $this->_giftCardFactory = $giftCardFactory;
        $this->_transactionFactory = $transactionFactory;
        $this->_dataHelper = $dataHelper;

        parent::__construct($context, $resultPageFactory, $poolFactory);
    }

    /**
     * @return void
     * @var PageFactory
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            // init model and set data
            $pool = $this->_initObject();

            $this->prepareData($data);
            if (!empty($data)) {
                $pool->addData($data);
            }

            // try to save it
            try {
                $pool->save();

                $this->messageManager->addSuccessMessage(__('The Gift Code Pool has been saved successfully.'));

                // clear previously saved data from session
                $this->_getSession()->setData('pool_form_data', false);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $pool->getId()]);

                    return;
                }
                $this->_redirect('*/*/');

                return;
            } catch (LocalizedException $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving gift code pool.' . $e->getMessage())
                );
            }

            $this->_getSession()->setData('pool_form_data', $data);

            // redirect to edit form
            $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);

            return;
        }
        $this->_redirect('*/*/');
    }

    /**
     * @param $data
     *
     * @return $this
     */
    protected function prepareData(&$data)
    {
        if (isset($data['template_fields'])) {
            $data['template_fields'] = DataHelper::jsonEncode($data['template_fields']);
        }
        $data['action_vars'] = ['auth' => $this->_auth->getUser()->getName()];

        $store = $this->_objectManager->get('Magento\Store\Model\StoreManager');
        if ($store->isSingleStoreMode()) {
            $data['store_id'] = $store->getStore(true)->getId();
        }

        return $this;
    }
}
