define(
[
     'uiComponent',
     'Magento_Checkout/js/model/payment/renderer-list'
],
function (
     Component,
     rendererList
) {
     'use strict';
     rendererList.push(
         {
             type: 'newpayment',
             component: 'Bdcrops_Newpayment/js/view/payment/method-renderer/newpayment-method'
         }
     );
     /** Add view logic here if needed */
     return Component.extend({});
}
);
