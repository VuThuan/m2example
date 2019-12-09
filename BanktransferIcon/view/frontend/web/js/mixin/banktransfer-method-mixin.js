define([
    'jquery',
    'ko'
], function ($, ko) {
    'use strict';

    return function (banktransferMethod) {
        return banktransferMethod.extend({
            defaults: {
                template: 'Bdcrops_BanktransferIcon/payment/banktransfer'
            },
            getLogoUrl: function () {
                return window.checkoutConfig.payment.banktransfer.banktransferLogoUrl;
            }
        });
    };
});
