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

namespace Bdcrops\GiftCard\Controller\Adminhtml\Code;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;
use Bdcrops\GiftCard\Controller\Adminhtml\Code;
use Bdcrops\GiftCard\Helper\Template;
use Bdcrops\GiftCard\Model\GiftCardFactory;
use Bdcrops\GiftCard\Model\Product\DeliveryMethods;
use Spipu\Html2Pdf\Exception\Html2PdfException;

/**
 * Class Save
 * @package Bdcrops\GiftCard\Controller\Adminhtml\Code
 */
class Save extends Code
{
    /**
     * @var Template
     */
    protected $templateHelper;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param GiftCardFactory $giftCardFactory
     * @param Template $templateHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        GiftCardFactory $giftCardFactory,
        Template $templateHelper
    ) {
        $this->templateHelper = $templateHelper;

        parent::__construct($context, $resultPageFactory, $giftCardFactory);
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     * @throws Html2PdfException
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            // init model and set data
            $giftCard = $this->_initObject();

            $action = $this->getRequest()->getParam('action');
            if (($action == 'print') && $giftCard->getId()) {
                $this->templateHelper->outputGiftCardPdf($giftCard, 'D');

                return;
            }

            $this->prepareData($data);
            if (!empty($data)) {
                $giftCard->addData($data);
            }

            if ($action == 'send') {
                if ($giftCard->getDeliveryMethod() && $giftCard->getDeliveryAddress()) {
                    $giftCard->setData('send_to_recipient', true);
                } else {
                    $this->messageManager->addNoticeMessage(__('Gift card is not sent. Please correct the delivery information.'));
                }
            }

            // try to save it
            try {
                $giftCard->save();

                $this->messageManager->addSuccessMessage(__('The Gift Card Code has been saved successfully.'));

                // clear previously saved data from session
                $this->_getSession()->unsData('giftcard_code_form');

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back') == 'edit') {
                    $this->_redirect('*/*/edit', ['id' => $giftCard->getId()]);

                    return;
                }
                $this->_redirect('*/*/');

                return;
            } catch (LocalizedException $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                // display error message
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving gift code.'));
            }
            $this->_getSession()->setData('giftcard_code_form', $data);

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
            $data['template_fields'] = Template::jsonEncode($data['template_fields']);
        }
        if (isset($data['delivery_method'])) {
            $fieldName = DeliveryMethods::getFormFieldName($data['delivery_method']);
            $data['delivery_address'] = isset($data[$fieldName]) ? $data[$fieldName] : '';
        }
        $data['action_vars'] = ['auth' => $this->_auth->getUser()->getName()];

        $store = $this->_objectManager->get('Magento\Store\Model\StoreManager');
        if ($store->isSingleStoreMode()) {
            $data['store_id'] = $store->getStore(true)->getId();
        }

        return $this;
    }
}
