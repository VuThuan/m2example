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

namespace Bdcrops\GiftCard\Block\Adminhtml\Template\Edit\Tab;

use Exception;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Gallery\Content;
use Bdcrops\GiftCard\Helper\Data;
use Bdcrops\GiftCard\Model\Template;

/**
 * Class Images
 * @package Bdcrops\GiftCard\Block\Adminhtml\Template\Edit\Tab
 */
class Images extends Generic implements TabInterface
{
    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        $this->addChild('content', 'Bdcrops\GiftCard\Block\Adminhtml\Template\Edit\Tab\Renderer\Images');

        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        /* @var $content Content */
        $content = $this->getChildBlock('content');
        $content->setId('media_gallery_content')->setElement($this);
        $content->setFormName('edit_form');

        return $content->toHtml();
    }

    /**
     * Retrieve data object related with form
     *
     * @return Template
     */
    public function getDataObject()
    {
        return $this->_coreRegistry->registry('current_template');
    }

    /**
     * Get product images
     *
     * @return array|null
     */
    public function getImages()
    {
        $images = $this->getDataObject()->getImages();
        if ($images) {
            try {
                $images = Data::jsonDecode($images);
            } catch (Exception $e) {
                $images = [];
            }
        }

        return $images;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'images';
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Images');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Images');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
