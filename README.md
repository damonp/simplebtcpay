simpleBTCpay
============

## Overview
simpleBTCpay provides an easy to setup bitcoin donation / micro-payment interface.  

### Features
- Simple API driven functionality
- Live BTC/USD quotes from BitcoinAverage.com
- On-the-fly BTC forwarding addresses generated from [blockchain.io](https://blockchain.info/api/api_receive)
- Configurable minimum payment amount. [Default: 0.001B (~$0.40)]
- Only one BTC address needed


### Examples
1. Link to form to allow customer to complete (all fields):  
<http://simplebtcpay.com/index.php?oid=7f0665&oemail=user@domain.com&tot_usd=0.50&odesc=Donation>
1. Direct link to payment page (all fields):  
<http://simplebtcpay.com/index.php?oid=1ALK5l&oemail=user@domain.com&tot_usd=0.50&odesc=Donation>
1. Link to minimal form:  
<http://simplebtcpay.com/index.php?tot_usd=0.50>
1. Form with optional fields disabled:  
<http://simplebtcpay.com/index.php?oid=false&oemail=false&tot_usd=0.50&odesc=false>

### Install

Edit lib/config.inc.php as needed.

```
php -f bin/setup.php
```

