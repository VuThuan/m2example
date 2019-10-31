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
        'jquery',
        'Magento_Checkout/js/model/resource-url-manager',
        'Magento_Checkout/js/model/quote'
    ],
    function ($, resourceUrlManager, quote) {
        "use strict";
        return $.extend(resourceUrlManager, {
            getApplyGiftCardUrl: function (code) {
                var params = (this.getCheckoutMethod() == 'guest') ? {quoteId: quote.getQuoteId()} : {},
                    urls = {
                        'guest': '/guest-carts/:quoteId/mpgiftcard/' + code,
                        'customer': '/carts/mine/mpgiftcard/' + code
                    };

                return this.getUrl(urls, params);
            },

            getApplyCreditUrl: function (amount) {
                var params = {},
                    urls = {
                        'customer': '/carts/mine/mpgiftcredit/' + amount
                    };

                return this.getUrl(urls, params);
            }
        });
    }
);

