<?php

    include('../lib/config.inc.php');

    $tot_btc = $tot_usd = $oid = $odesc = null;
    if(count($_GET) > 0)   {
       $filters = array(
                         'tot_btc'  => FILTER_SANITIZE_STRING,
                         'tot_usd'  => FILTER_SANITIZE_STRING,
                         'oid'      => FILTER_SANITIZE_STRING,
                         'odesc'    => FILTER_SANITIZE_STRING,
                         'oemail'   => FILTER_SANITIZE_STRING,
                        );
       extract(filter_input_array(INPUT_GET, $filters));
    }

    include('main.inc.php');

    $tot_btc = $tot_btc != '' ? $tot_btc:'0.0';
    $tot_usd = $tot_usd != '' ? $tot_usd:'0.00';
    $oid = $oid ? $oid:rand_id();

    include('header.inc.php');
?>

<div id="orderform">
<form action="./pay.php" method="post" id="orderform" name="orderform" onclick="return submit_form();">
<input type="hidden" name="exch_rate" value="<?php $exch_rate; ?>" />
<input type="hidden" name="act" value="pay" />

<fieldset style="width:94%;margin-right:15px;">
<legend>BTCPay</legend>
<label for="oid">Invoice ID:</label>
<input type="text" name="oid" value="<?php echo $oid;?>" size="8" style="text-align:right;" />
<br /><br />

<label for="tot_btc">Total BTC:</label>
<input type="text" name="tot_btc" value="<?php echo $tot_btc;?>" size="8" style="text-align:right;" /> <b>BTC</b>
<br />
<center><em style="font-weight:bold;color:#999;">or</em></center>

<label for="tot_usd">Total USD:</label>
<input type="text" name="tot_usd" value="<?php echo $tot_usd;?>" size="8" style="text-align:right;" /> <b>USD</b>
<br /><br />

<label for="oemail">Email:</label>
<input type="text" name="oemail" value="<?php echo $oemail;?>" size="20" style="text-align:left;" />
<br /><br />

<label for="odesc">Description:</label>
<textarea name="odesc" rows="3" cols="20"><?php echo $odesc;?></textarea>

<br /><br />
<label for="submit"></label>
<button type="submit" name="submit" id="submit" >Submit</button>

<br />
</fieldset>

</form>

<?php include('footer.inc.php'); ?>

<script>
<!--



-->
</script>
</body>
</html>

