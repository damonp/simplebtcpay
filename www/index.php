<?php

    include_once('../app/lib/config.inc.php');
    include_once('app/lib/main.inc.php');

    include_once('app/lib/header.inc.php');

?>

<div id="bodycontent" style="">

    <h3>Overview</h3>
    <p>simpleBTCpay : DRK provides an easy to setup Darkcoin donation / micro-payment interface.</p>

    <h4>Features</h4>
    <ul>
    <li>Simple Secure API driven functionality.</li>
    <li>No secure keys or passwords stored or required.</li>
    <li>QR Code support for easy of payment.</li>
    <li>Open Source [ <a href="/download.php">download</a> ].</li>
    <li>Live DRK/USD quotes from multiple exchange APIs.</li>
    <li>On-the-fly DRK forwarding addresses generated from local darkcoind.</li>
    <li>Only one incoming DRK address needed.</li>
    <li>Configurable minimum payment amount. [Default: 0.001DRK]</li>
    </ul>

    <h4>Examples</h4>
    <ul>
    <li>Link to form to allow customer to complete (all fields):<br />
        <a href="http://drkmkt.com/form.php?oemail=user@domain.com&tot_usd=0.50&odesc=Donation</li>">http://drkmkt.com/form.php?oemail=user@domain.com&tot_usd=0.50&odesc=Donation</a></li>
    <li>Direct link to payment page (all fields):
        <a href="http://drkmkt.com/pay.php?oemail=user@domain.com&tot_usd=0.50&odesc=Donation">http://drkmkt.com/pay.php?oemail=user@domain.com&tot_usd=0.50&odesc=Donation</a></li>
    <li>Link to minimal form:<br />
        <a href="http://drkmkt.com/form.php?tot_usd=0.50">http://drkmkt.com/form.php?tot_usd=0.50</a></li>
    <li>Form with optional fields disabled:<br />
        <a href="http://drkmkt.com/form.php?oid=false&oemail=false&tot_usd=0.50&odesc=false">http://drkmkt.com/form.php?oid=false&oemail=false&tot_usd=0.50&odesc=false</a></li>
    </ul>

<h2 id="exchrate"><?php echo '1 '.SBTCP_COIN.' = $'.number_format($exch_rate, 2); ?></h2>
<div class="logo"></div>

<?php   include_once('app/lib/form.inc.php');   ?>

<div class="">
<h3 class="">Uses</h3>
<ul style="">
    <li>Donations</li>
    <li>Download Forms</li>
    <li>Invoice Payments</li>
    <li>Micro-pay Support Form</li>
</ul>
</div>


</div>


<?php   include_once('app/lib/footer.inc.php'); ?>

<script>
<!--



-->
</script>
</body>
</html>
