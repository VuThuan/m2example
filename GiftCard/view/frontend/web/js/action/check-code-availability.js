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
        'Bdcrops_GiftCard/js/model/messageList'
    ],
    function (storage, messageList) {
        'use strict';

        return function (deferred, code, canRedeem) {
            return storage.post(
                'mpgiftcard/index/check',
                JSON.stringify({
                    code: code
                })
            ).done(
                function (response) {
                    if (!response.errors) {
                        var detail = response.detail;

                        response.message += '<table>';
                        response.message += '<tr><td>Code</td><td>' + detail.code + '</td></tr>';
                        response.message += '<tr><td>Active Balance</td><td>' + detail.balance_formatted + '</td></tr>';
                        response.message += '<tr><td>Status</td><td>' + detail.status_label + '</td></tr>';

                        if (detail.expired_at) {
                            response.message += '<tr><td>Expired At</td><td>' + detail.expired_at_formatted + '</td></tr>';
                        }
                        response.message += '</table>';

                        canRedeem(response.canRedeem);
                        messageList.addSuccessMessage(response);
                        deferred.resolve();
                    } else {
                        messageList.addErrorMessage(response);
                        deferred.reject();
                    }
                }
            ).fail(
                function () {
                    deferred.reject();
                }
            );
        };
    }
);

