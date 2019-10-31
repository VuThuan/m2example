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
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;
use Bdcrops\GiftCard\Controller\Adminhtml\Pool;
use Bdcrops\GiftCard\Helper\Data;
use Bdcrops\GiftCard\Model\GiftCardFactory;
use Bdcrops\GiftCard\Model\PoolFactory;

/**
 * Class Generate
 * @package Bdcrops\GiftCard\Controller\Adminhtml\Pool
 */
class Generate extends Pool
{
    /**
     * @type GiftCardFactory
     */
    protected $_giftCardFactory;

    /**
     * @var JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * Generate constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param PoolFactory $poolFactory
     * @param JsonFactory $resultJsonFactory
     * @param GiftCardFactory $cardFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        PoolFactory $poolFactory,
        JsonFactory $resultJsonFactory,
        GiftCardFactory $cardFactory
    ) {
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_giftCardFactory = $cardFactory;

        parent::__construct($context, $resultPageFactory, $poolFactory);
    }

    /**
     * Generate
     */
    public function execute()
    {
        if (!$this->getRequest()->isAjax()) {
            $this->_forward('noroute');

            return;
        }

        $result = [];
        $pool = $this->_initObject();
        if ($pool && $pool->getId()) {
            try {
                $data = $this->getRequest()->getParams();
                if (!isset($data['pattern']) || !isset($data['qty'])) {
                    throw new InputException(__('Invalid date provided'));
                }

                $giftCards = $this->_giftCardFactory->create()
                    ->setData($pool->getData())
                    ->addData([
                        'pattern'       => $data['pattern'],
                        'pool_id'       => $pool->getId(),
                        'extra_content' => Data::jsonEncode(['auth' => $this->_auth->getUser()->getName()]),
                        'action_vars'   => Data::jsonEncode(['pool_id' => $pool->getId()])
                    ])
                    ->createMultiple($data['qty']);

                $generated = count($giftCards);
                $this->messageManager->addSuccess(__('%1 code(s) have been generated.', $generated));
                $this->_view->getLayout()->initMessages();
                $result['messages'] = $this->_view->getLayout()->getMessagesBlock()->getGroupedHtml();
            } catch (InputException $inputException) {
                $result['error'] = __('Invalid data provided');
            } catch (LocalizedException $e) {
                $result['error'] = $e->getMessage();
            } catch (Exception $e) {
                $result['error'] = __('Something went wrong while generating gift cards. Please review the log and try again.');
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            }
        } else {
            $result['error'] = __('Pool is not defined');
        }

        $this->getResponse()->representJson(Data::jsonEncode($result));
    }
}
