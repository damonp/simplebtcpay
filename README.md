simpleBTCpay : DRK
==================

## Overview
simpleBTCpay provides an easy to setup crypto donation / micro-payment interface.  

### Features
- Simple API driven functionality.
- No secure keys or passwords stored or required.
- Live DRK/USD quotes from multiple exchange APIs.
- QR Code support for easy of payment.
- On-the-fly DRK forwarding addresses generated from darkcoind.
- Automated job to forward payments to main receiving address every 5 mins.
- Provides automated callback to complete order when funds are received.
- Generates templated admin and customer emails upon successful payment with order detals.
- Configurable minimum payment amount. [Default: 0.001DRK]


### Examples
1. Link to form to allow customer to complete (all fields):  
<http://drkmkt.com/form.php?oemail=user@domain.com&tot_usd=0.50&odesc=Donation>
1. Direct link to payment page (all fields):  
<http://drkmkt.com/form?oemail=user@domain.com&tot_usd=0.50&odesc=Donation>
1. Link to minimal form:  
<http://drkmkt.com/form.php?tot_usd=0.50>
1. Form with optional fields disabled:  
<http://drkmkt.com/form.php?oid=false&oemail=false&tot_usd=0.50&odesc=false>

### Install
1. Clone repo outside of public web space.
2. Add an [Apache alias](http://httpd.apache.org/docs/2.2/mod/mod_alias.html) in your vhost container to simplebtcpay/www similar to:

	```
	Alias /srv/simplebtcpay/www /btc
	```	
	This will expose the app at the url:
	<http://domain.com/btc>

3. Copy app/lib/config.sample.inc.php to config.inc.php
4. Edit app/lib/config.inc.php as needed.
5. From app root directory:
	
	```
	php -f app/bin/setup.php
	```
6. Restart webserver.
7. Go: <http://domain.com/btc>

Setup creates a new SQLite database in the app/data/ directory. 