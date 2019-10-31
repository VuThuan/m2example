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
    'jquery',
    'productGallery'
], function ($, productGallery) {
    'use strict';

    $.widget('mage.productGallery', productGallery, {
        options: {
            types: {}
        },

        _create: function () {
            this._super();
        },

        setBase: function (imageData) {
            return this;
        },

        _updateImagesRoles: function () {
            return this;
        }
    });

    return $.mage.productGallery;
});

