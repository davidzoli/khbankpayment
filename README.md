## PHP class for K&H Bank payment integration
_Unofficial release. Made by an independent developer._

Simple PHP class to generate URLs for the K&H Bank's payment gateway system.  
Contact the K&H Bank for integration details and for necessary setting values before using this.  

Information about the integration, codes and the payment process can be found in the official documentation given by the K&H Bank.  
This readme contains info about generating URLs for redirect the user to the payment system and getting transaction info.

### Configuration
```php
require 'KHbank.php';

$khtest = new \Payment\KHbank(
    1111111111111111,                       // mid - Given by the bank.
    '/folder_to/private_key.pem',           // Private key - Public pair is at the Bank
    'http://url.to.the.payment.system'      // Test or live url - Given by the Bank.
);
```

### Payment URL
```php
// Transaction (order) id - 1
// Amount to pay - 100 HUF
$pu = $khtest->getPayUrl(1, 10000);
```

### Reserve URL
```php
// Transaction (order) id - 1
// Amount to reserve - 10 HUF
$re = $khtest->getReserveUrl(1, 1000);
```

### Result URL
```php
// Transaction (order) id - 1
$te = $khtest->getResultUrl(1);
```

### Get the payment result
```php
// Transaction (order) id - 1
$tr = $khtest->getResult(1);
```
### Result example
##### Successful transaction
`XXXXXX` is an integer (6 digits)
```
ACK
1
ELFOGADVA / ENGEDELYEZVE
XXXXXX B
```
##### Unsuccessful transaction
```
NAK
64
A 2. SAV ADATAI HIBASAK !
00000000
```