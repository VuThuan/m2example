/**
 * Bdcrops
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Bdcrops.com license sliderConfig is
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

define([
    'Magento_Ui/js/grid/columns/thumbnail',
    'jquery',
    'mage/template',
    'text!Bdcrops_GiftCard/template/grid/cells/thumbnail/preview.html',
    'Magento_Ui/js/modal/modal'
], function (Column, $, mageTemplate, thumbnailPreviewTemplate) {
    'use strict';

    return Column.extend({
        modal: {},
        preview: function (row) {
            if (typeof row[this.index + '_design'] === 'undefined') {
                console.log('There is no preview for this template.');
                return;
            }

            var templateId = row.template_id;
            if (typeof this.modal[templateId] === 'undefined') {
                var modalHtml = mageTemplate(
                    thumbnailPreviewTemplate,
                    {
                        card: row[this.index + '_card'],
                        design: row[this.index + '_design'],
                        images: row[this.index + '_images'],
                        alt: this.getAlt(row),
                        templateId: templateId
                    }
                );
                this.modal[templateId] = $('<div/>')
                    .html(modalHtml)
                    .modal({
                        title: this.getAlt(row),
                        innerScroll: true,
                        modalClass: '_image-box',
                        buttons: []
                    });
            }
            this.modal[templateId].trigger('openModal');
        }
    });
});

