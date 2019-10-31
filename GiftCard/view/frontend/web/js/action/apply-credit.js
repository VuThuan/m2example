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

define(
    [
        'ko',
        'jquery',
        'mage/storage',
        'Bdcrops_GiftCard/js/model/checkout',
        'Bdcrops_GiftCard/js/model/resource-url-manager',
        'Bdcrops_GiftCard/js/model/messageList',
        'Magento_Checkout/js/model/totals',
        'Magento_Checkout/js/action/get-totals',
        'Magento_Checkout/js/action/get-payment-information',
        'mage/translate'
    ],
    function (ko, $, storage, giftCard, urlManager, messageContainer, totals, getTotalsAction, getPaymentInformationAction, $t) {
        'use strict';

        return function (amount) {
            var url = urlManager.getApplyCreditUrl(amount),
                message = $t('Your credit was successfully applied.');

            giftCard.isLoading(true);

            return storage.put(url, {}, false)
                .done(
                    function (response) {
                        if (response) {
                            var deferred = $.Deferred();

                            if ($('body').hasClass('checkout-cart-index')) {
                                getTotalsAction([], deferred);
                                $.when(deferred).done(function () {
                                    giftCard.isLoading(false);
                                });
                            } else {
                                totals.isLoading(true);
                                getPaymentInformationAction(deferred);
                                $.when(deferred).done(function () {
                                    giftCard.isLoading(false);
                                    totals.isLoading(false);
                                });
                            }

                            messageContainer.addSuccessMessage({
                                'message': message
                            });
                        }
                    }
                ).fail(
                    function (response) {
                        giftCard.isLoading(false);
                        totals.isLoading(false);
                        messageContainer.addErrorMessage(JSON.parse(response.responseText));
                    }
                ).always(
                    function (response) {
                        giftCard.isLoading(false);
                        totals.isLoading(false);
                    }
                );
        };
    }
);

