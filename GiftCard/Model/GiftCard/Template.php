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

namespace Bdcrops\GiftCard\Model\GiftCard;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\Data\OptionSourceInterface;
use Bdcrops\GiftCard\Model\TemplateFactory;

/**
 * Class Template
 * @package Bdcrops\GiftCard\Model\GiftCard
 */
class Template extends AbstractSource implements OptionSourceInterface
{
    /**
     * @type TemplateFactory
     */
    protected $_templateFactory;

    /**
     * @var array
     */
    protected $_templates;

    /**
     * Constructor
     *
     * @param TemplateFactory $templateFactory
     */
    function __construct(TemplateFactory $templateFactory)
    {
        $this->_templateFactory = $templateFactory;
    }

    /**
     * Get all option
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (is_null($this->_templates)) {
            $this->_templates = [['value' => 'no_template', 'label' => __('-- No Template --')]];

            $collection = $this->_templateFactory->create()->getCollection();
            foreach ($collection as $template) {
                $this->_templates[] = ['value' => $template->getId(), 'label' => $template->getName()];
            }
        }

        return $this->_templates;
    }
}
