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
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        'ko',
        'Magento_Ui/js/view/messages',
        'Bdcrops_GiftCard/js/model/messageList'
    ],
    function ($, ko, Component, globalMessages) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Bdcrops_GiftCard/messages',
                selector: '[data-role=giftcard-messages]'
            },

            initialize: function (config, messageContainer) {
                this._super(config, messageContainer);

                this.messageContainer = globalMessages;

                return this;
            },

            onHiddenChange: function (isHidden) {
                return this;
            }
        });
    }
);

