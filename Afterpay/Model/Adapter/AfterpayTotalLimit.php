<?php
/**
 * Magento 2 extensions for Afterpay Payment
 *
 * @author Afterpay
 * @copyright 2016-2019 Afterpay https://www.afterpay.com
 */
namespace Bdcrops\Afterpay\Model\Adapter;

use \Bdcrops\Afterpay\Model\Adapter\Afterpay\Call;
use \Bdcrops\Afterpay\Model\Config\Payovertime as AfterpayConfig;
use \Magento\Framework\ObjectManagerInterface as ObjectManagerInterface;
use \Magento\Framework\Json\Helper\Data as JsonHelper;

/**
 * Class AfterpayTotalLimit
 * @package Bdcrops\Afterpay\Model\Adapter
 */
class AfterpayTotalLimit
{
    /**
     * @var Call
     */
    protected $afterpayApiCall;
    protected $afterpayConfig;
    protected $objectManagerInterface;
    protected $jsonHelper;

    /**
     * AfterpayTotalLimit constructor.
     * @param Call $afterpayApiCall
     * @param AfterpayConfig $afterpayConfig
     * @param ObjectManagerInterface $objectManagerInterface
     * @param JsonHelper $jsonHelper
     */
    public function __construct(
        Call $afterpayApiCall,
        AfterpayConfig $afterpayConfig,
        ObjectManagerInterface $objectManagerInterface,
        JsonHelper $jsonHelper
    ) {
        $this->afterpayApiCall = $afterpayApiCall;
        $this->afterpayConfig = $afterpayConfig;
        $this->objectManagerInterface = $objectManagerInterface;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * @return mixed|\Zend_Http_Response
     */
    public function getLimit($override = [])
    {
        /** @var \Bdcrops\Afterpay\Model\Config\Payovertime $url */
        $url = $this->afterpayConfig->getApiUrl('v1/configuration'); //V1

        // calling API
        try {
            $response = $this->afterpayApiCall->send($url, null, null, $override);
        } 
        catch (\Exception $e) {

            $state =  $this->objectManagerInterface->get('Magento\Framework\App\State');
            if ($state->getAreaCode() == \Magento\Framework\App\Area::AREA_ADMINHTML) {
                throw new \Exception($e->getMessage());
            }
            else {
                $response = $this->objectManagerInterface->create('Bdcrops\Afterpay\Model\Payovertime');
                $response->setBody($this->jsonHelper->jsonEncode([
                    'error' => 1,
                    'message' => $e->getMessage()
                ]));
            }
        }

        return $response;
    }
}
