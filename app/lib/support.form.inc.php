<?php

    $tot_btc = $tot_usd = $oid = $odesc = null;
    if(count($_GET) > 0)   {
       $filters = array(
                         'tot_btc'  => FILTER_SANITIZE_STRING,
                         'tot_usd'  => FILTER_SANITIZE_STRING,
                         'oid'      => FILTER_SANITIZE_STRING,
                         'odesc'    => FILTER_SANITIZE_STRING,
                         'oemail'   => FILTER_SANITIZE_STRING, //- filter email
                         'act'      => FILTER_SANITIZE_STRING,
                        );
       extract(filter_input_array(INPUT_GET, $filters));
    }

    //- adjust defaults as needed
    $oid = $oid ? $oid:Helper::rand_id();
    $tot_btc = $tot_btc != '' ? $tot_btc:0.0;
    $tot_usd = $tot_usd != '' ? $tot_usd:0.50;
    $odesc = $odesc == '' ? 'Donation':$odesc;

    $error = false;
    switch($act)    {
        case('error.minimum'):
            $error = "Minimum Payment is: 0.001 BTC.";
        break;
    }

    if($error):
?>
<div class="error">
<?php echo $error; ?>
</div>

<?php endif; ?>

<div id="orderform" class="contact">
<form action="./pay.php" method="post" id="orderform" name="orderform" onclick="" class="form-horizontal">
<input type="hidden" name="exch_rate" value="<?php $exch_rate; ?>" />
<input type="hidden" name="act" value="pay" />

<fieldset class="">
<legend>Contact Us</legend>

<?php if($oid != 'false'): ?>
<label for="oid">Request ID:</label>
<input type="text" name="oid" value="<?php echo $oid;?>" size="8" style="text-align:right;" />
<br /><br />
<?php endif; ?>

<?php if($oemail != 'false'): ?>
<label for="oemail">Email:</label>
<input type="text" name="oemail" value="<?php echo $oemail;?>" size="20" style="text-align:left;" />
<br /><br />
<?php endif; ?>

<?php if($odesc != 'false'): ?>
<label for="odesc">Question:</label>
<textarea name="odesc" rows="3" class="desc"><?php echo $odesc;?></textarea>
<br /><br />
<?php endif; ?>

<label for="tot_btc">Total BTC:</label>
<input type="text" name="tot_btc" value="<?php echo $tot_btc;?>" size="8" style="text-align:right;" /> <b>BTC</b>
<br />
<center><em style="font-weight:bold;color:#999;">or</em></center>

<label for="tot_usd">Total USD:</label>
<input type="text" name="tot_usd" value="<?php echo number_format($tot_usd, 2);?>" size="8" style="text-align:right;" /> <b>USD</b>
<br /><br />

<div class="wide">
<button type="submit" name="submit" id="submit" class="btn btn-primary">Submit</button>
</div>

<br />
<div class="formfooter">

</div>
</fieldset>

</form>


