4
5
6
7
8
9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
<?php

namespace Bdcrops\Newpayment\Model;

/**
 * Pay In Store payment method model
 */
class Newpayment extends \Magento\Payment\Model\Method\AbstractMethod
{

/**
* Payment code
*
* @var string
*/
protected $_code = 'newpayment';

/**
* Availability option
*
* @var bool
*/
protected $_isOffline = true;
}
