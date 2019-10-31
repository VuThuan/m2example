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

define(
    [
        'ko',
        'jquery',
        'Magento_Checkout/js/view/summary/abstract-total',
        'Bdcrops_GiftCard/js/model/checkout',
        'mage/translate'
    ],
    function (ko, $, Component, giftCardModel, $t) {
        "use strict";

        return Component.extend({
            defaults: {
                template: 'Bdcrops_GiftCard/totals/discount'
            },
            giftCardsUsed: giftCardModel.giftCardsUsed,

            /**
             * Is Gift Card Display
             */
            isDisplayed: ko.computed(function () {
                return !!giftCardModel.getSegment('gift_card');
            }),

            /**
             * Is Gift Credit Display
             */
            isCreditDisplayed: ko.computed(function () {
                return !!giftCardModel.getSegment('gift_credit');
            }),

            /**
             * Initial component
             */
            initialize: function () {
                var self = this;

                this._super();

                this.titleDisplay = ko.computed(function () {
                    if (giftCardModel.canShowDetail() && (self.giftCardsUsed().length === 1)) {
                        return self.getTitle() + ' (' + self.giftCardsUsed()[0].code + ')';
                    }

                    return self.getTitle();
                });

                this.ifShowDetails = ko.computed(function () {
                    return giftCardModel.canShowDetail() && (self.giftCardsUsed().length > 1);
                });
            },

            /**
             * Gift Card Title
             * @returns {*}
             */
            getTitle: function () {
                var segment = giftCardModel.getSegment('gift_card');
                if (segment) {
                    return segment.title;
                }

                return $t('Gift Card');
            },

            /**
             * Credit title
             *
             * @returns {*}
             */
            creditTitle: function () {
                var segment = giftCardModel.getSegment('gift_credit');
                if (segment) {
                    return segment.title;
                }

                return $t('Gift Credit');
            },

            /**
             * get Value
             *
             * @returns {*|String}
             */
            getValue: function () {
                return this.getFormattedPrice(giftCardModel.getSegment('gift_card').value);
            },

            /**
             * get Credit Value
             *
             * @returns {*|String}
             */
            getCreditValue: function () {
                return this.getFormattedPrice(giftCardModel.getSegment('gift_credit').value);
            }
        });
    }
);

