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

/*global define*/
define(
    [
        'jquery',
        'ko'
    ],
    function ($, ko) {
        "use strict";

        var giftCard = window.giftCard;

        return {
            baseUrl: giftCard.baseUrl,
            email: giftCard.customerEmail,
            code: giftCard.code,
            balance: ko.observable(giftCard.balance),
            transactions: ko.observableArray(giftCard.transactions),
            giftCardLists: ko.observableArray(giftCard.giftCardLists),
            notification: giftCard.notification,

            /** Is enable credit balance or not */
            isEnableCredit: function () {
                return giftCard.isEnableCredit;
            },

            /** Is enable setting fieldset */
            isEnableSetting: function () {
                return giftCard.notification.enable;
            }
        };
    }
);

