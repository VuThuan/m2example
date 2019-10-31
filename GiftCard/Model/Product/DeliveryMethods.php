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

namespace Bdcrops\GiftCard\Model\Product;

use Magento\Config\Model\Config\Backend\Admin\Custom;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Validator\EmailAddress;
use Magento\Framework\Validator\Timezone;
use Bdcrops\GiftCard\Helper\Data;
use Bdcrops\GiftCard\Model\Source\FieldRenderer;
use Zend\Validator\Date;

/**
 * Class DeliveryMethods
 * @package Bdcrops\GiftCard\Model\Product
 */
class DeliveryMethods extends AbstractSource
{
    const TYPE_EGIFT   = 1;
    const TYPE_PRINT   = 2;
    const TYPE_MAIL    = 3;
    const METHOD_EMAIL = 1;
    const METHOD_SMS   = 2;
    const METHOD_PRINT = 3;
    const METHOD_POST  = 4;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Http
     */
    protected $request;

    /**
     * DeliveryMethods constructor.
     *
     * @param Data $dataHelper
     * @param Http $request
     */
    public function __construct(Data $dataHelper, Http $request)
    {
        $this->request = $request;
        $this->helper = $dataHelper;
    }

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            self::TYPE_EGIFT => __('eGift'),
            self::TYPE_PRINT => __('Print-at-home'),
            self::TYPE_MAIL  => __('Mail')
        ];
    }

    /**
     * Get all option
     *
     * @return array
     */
    public function getAllOptions()
    {
        $result = [];

        foreach (static::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    /**
     * @param       $method
     * @param array $params
     *
     * @return array
     */
    public function getDeliveryMethod($method, $params = [])
    {
        if (!array_key_exists($method, self::getOptionArray())) {
            return [];
        }

        $methodLabels = static::getMethodOptionArray();
        switch ($method) {
            case self::TYPE_EGIFT:
                $methods = [
                    [
                        'key'    => self::METHOD_EMAIL,
                        'label'  => $methodLabels[self::METHOD_EMAIL],
                        'fields' => [
                            'email' => [
                                'label'       => __('Email'),
                                'type'        => 'input',
                                'name'        => 'email',
                                'value'       => isset($params['email']) ? $params['email'] : '',
                                'placeHolder' => __('Recipient email'),
                                'class'       => 'validate-email required-entry',
                                'required'    => true
                            ]
                        ]
                    ]
                ];

                if ($this->helper->isSmsEnable()) {
                    $validator = ObjectManager::getInstance()->get(Validator\PhoneNumber::class);
                    $country = $this->helper->getConfigValue(Custom::XML_PATH_GENERAL_COUNTRY_DEFAULT);
                    $phonePattern = $validator->getPhonePattern($country);

                    $methods[] = [
                        'key'    => self::METHOD_SMS,
                        'label'  => $methodLabels[self::METHOD_SMS],
                        'fields' => [
                            'phone_number' => [
                                'label'       => __('Phone Num.'),
                                'type'        => 'input',
                                'name'        => 'phone_number',
                                'value'       => isset($params['phone_number']) ? $params['phone_number'] : '',
                                'placeHolder' => __('Recipient phone number'),
                                'class'       => 'delivery-phone-number validate-phoneLax required-entry',
                                'required'    => true,
                                'note'        => '+' . $phonePattern['code']
                            ]
                        ]
                    ];
                }
                break;
            case self::TYPE_PRINT:
                $methods = [
                    [
                        'key'    => self::METHOD_PRINT,
                        'label'  => $methodLabels[self::METHOD_PRINT],
                        'fields' => [
                            'label' => [
                                'label' => '',
                                'type'  => 'label',
                                'name'  => 'print_label',
                                'value' => __('You can print gift card on the confirmation email or the Gift Card in your account.'),
                            ]
                        ]
                    ]
                ];
                break;
            default:
                $methods = [
                    [
                        'key'    => self::METHOD_POST,
                        'label'  => $methodLabels[self::METHOD_POST],
                        'fields' => [
                            'label' => [
                                'label' => '',
                                'type'  => 'label',
                                'name'  => 'post_label',
                                'value' => __('Please input shipping address when checking out.'),
                            ]
                        ]
                    ]
                ];
                break;
        }

        return $methods;
    }

    /**
     * @param $method
     *
     * @return string
     */
    public static function getFormFieldName($method)
    {
        $fieldName = '';
        switch ($method) {
            case self::METHOD_EMAIL:
                $fieldName = 'recipient_email';
                break;
            case self::METHOD_SMS:
                $fieldName = 'recipient_phone';
                break;
            case self::METHOD_PRINT:
                $fieldName = 'customer_email';
                break;
            case self::METHOD_POST:
                $fieldName = 'recipient_address';
                break;
        }

        return $fieldName;
    }

    /**
     * @return array
     */
    public static function getMethodOptionArray()
    {
        return [
            self::METHOD_EMAIL => __('Email'),
            self::METHOD_SMS   => __('Text Message'),
            self::METHOD_PRINT => __('Print At Home'),
            self::METHOD_POST  => __('Post Office'),
        ];
    }

    /**
     * @return array
     */
    public static function getMethodOptionArrayForForm()
    {
        return ['' => __('-- Please Select --')] + self::getMethodOptionArray();
    }

    /**
     * @param $method
     * @param $fields
     *
     * @return array
     * @throws LocalizedException
     */
    public function validateMethodFields($method, $fields)
    {
        $currentAction = $this->request->getFullActionName();
        if ($currentAction != 'wishlist_index_add' && !array_key_exists($method, static::getMethodOptionArray())) {
            throw new LocalizedException(__('Delivery method is invalid.'));
        }

        $options = [FieldRenderer::METHOD => $method];
        if ($method == self::METHOD_EMAIL) {
            $email = $fields->getEmail();
            $validator = ObjectManager::getInstance()->get(EmailAddress::class);
            if (!$validator->isValid($email)) {
                throw new LocalizedException(__('Recipient email is required.'));
            }
            $options[FieldRenderer::ADDRESS] = $email;
        } elseif ($method == self::METHOD_SMS) {
            $validator = ObjectManager::getInstance()->get(Validator\PhoneNumber::class);
            $country = $this->helper->getConfigValue(Custom::XML_PATH_GENERAL_COUNTRY_DEFAULT);

            $phonePattern = $validator->getPhonePattern($country);
            $phoneNumber = '+' . $phonePattern['code'] . $fields->getPhoneNumber();

            $validator->setCountry($country);
            if (!$validator->isValid($phoneNumber)) {
                throw new LocalizedException(__('Recipient phone number is required.'));
            }
            $options[FieldRenderer::ADDRESS] = $phoneNumber;
        }

        if ($deliveryDate = $fields->getDeliveryDate()) {
            $validator = new Date();
            if (!$validator->isValid($deliveryDate)) {
                throw new LocalizedException(__('Please correct the delivery date.'));
            }
            $options[FieldRenderer::DATE] = $deliveryDate;
        }
        if ($timezone = $fields->getTimezone()) {
            $validator = ObjectManager::getInstance()->get(Timezone::class);
            if (!$validator->isValid($timezone)) {
                throw new LocalizedException(__('Please correct the timezone.'));
            }
            $options[FieldRenderer::TIMEZONE] = $timezone;
        }

        return $options;
    }

    protected function getPhoneNumberValidator()
    {
    }
}
