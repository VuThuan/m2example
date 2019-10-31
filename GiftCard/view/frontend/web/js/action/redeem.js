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
        'mage/storage',
        'Bdcrops_GiftCard/js/model/dashboard',
        'Magento_Ui/js/model/messageList',
        'Bdcrops_GiftCard/js/model/messageList'
    ],
    function (storage, giftCard, messageList, giftCardMessageList) {
        'use strict';

        return function (deferred, code) {

            return storage.post(
                'mpgiftcard/index/redeem',
                JSON.stringify({
                    code: code
                })
            ).done(
                function (response) {
                    if (!response.errors) {
                        giftCard.balance(response.balance);
                        giftCard.transactions(response.transactions);

                        if (typeof response.giftCardLists !== 'undefined') {
                            giftCard.giftCardLists(response.giftCardLists);
                        }

                        messageList.addSuccessMessage(response);
                        deferred.resolve();
                    } else {
                        messageList.addErrorMessage(response);
                        deferred.reject();
                    }

                    giftCardMessageList.clear();
                }
            ).fail(
                function () {
                    deferred.reject();
                }
            );
        };
    }
);

