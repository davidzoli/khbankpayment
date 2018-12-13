<?php

require 'KHbank.php';

/*
 * Fill your data given by the bank.
 * */
$khtest = new \Payment\KHbank(
    1111111111111111,
    '/folder_to/private_key.pem',
    'http://url.to.the.payment.system'
);

$pu = $khtest->getPayUrl(1, 10000);
$re = $khtest->getReserveUrl(1, 1000);
$te = $khtest->getResultUrl(1);
$tr = $khtest->getResult(1);

?>

<pre><?php echo $pu; ?></pre>
<pre><?php echo $re; ?></pre>
<pre><?php echo $te; ?></pre>
<pre><?php echo $tr; ?></pre>
