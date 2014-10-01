simpleBTCpay
============

## Overview
simpleBTCpay provides an easy to setup bitcoin donation / micro-payment interface.  

### Features
- Simple API driven functionality
- Live BTC/USD quotes from multiple APIs; [BitcoinAverage](http://bitcoinaverage.com), [Coindesk BPI](http://coindesk.com), [Coinbase](http://coinbase.com) and others.
- On-the-fly BTC forwarding addresses generated from [Blockchain](https://blockchain.info/api/api_receive) or [BlockCypher](http://dev.blockcypher.com/reference.html#payments)
- Provides automated callback to complete order when funds are received.
- Generates templated admin and customer emails upon successful payment with order detals.
- Configurable minimum payment amount. [Default: 0.001B]
- Only one incoming BTC address needed.


### Examples
1. Link to form to allow customer to complete (all fields):  
<http://simplebtcpay.com/form.php?oemail=user@domain.com&tot_usd=0.50&odesc=Donation>
1. Direct link to payment page (all fields):  
<http://simplebtcpay.com/form?oemail=user@domain.com&tot_usd=0.50&odesc=Donation>
1. Link to minimal form:  
<http://simplebtcpay.com/form.php?tot_usd=0.50>
1. Form with optional fields disabled:  
<http://simplebtcpay.com/form.php?oid=false&oemail=false&tot_usd=0.50&odesc=false>

### Install
1. Clone repo outside of public web space.
2. Add an [Apache alias](http://httpd.apache.org/docs/2.2/mod/mod_alias.html) in your vhost container to simplebtcpay/www similar to:

	```
	Alias /srv/simplebtcpay/www /btc
	```	
	This will expose the app at the url:
	<http://domain.com/btc>

3. Copy lib/config.sample.inc.php
4. Edit lib/config.inc.php as needed.
5. From app root directory:
	
	```
	php -f bin/setup.php
	```
6. Restart webserver.
7. Go: <http://domain.com/btc>

Setup creates a new SQLite database in the data/ directory. 