<?php

   $tot_drk = $tot_usd = $oid = $odesc = null;
   if(count($_GET) > 0)   {
      $filters = array(
                        'tot_drk'  => FILTER_SANITIZE_STRING,
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
   $tot_drk = $tot_drk != '' ? $tot_drk:0.0;
   $tot_usd = $tot_usd != '' ? $tot_usd:0.50;
   if($odesc == '') {
      if(file_exists(SBTCP_PATH.'/app/data/tips.php'))   {
         include(SBTCP_PATH.'/app/data/tips.php');
         srand((double)microtime()*1000000);
         $odesc = array_rand($tips);
         $tot_usd = $tips[$odesc];
         if($tot_usd == EXCH_RATE)  {
            $tot_usd = $tot_usd <= 0 ? 1.0:$tot_usd;
            $tot_drk = $tot_usd;
            $tot_usd = $tot_usd * $exch_rate;
         }
      }  else  {
         $odesc = 'Donation';
      }
   }

   $error = false;
   switch($act)    {
      case('error.minimum'):
        $error = "Minimum Payment is: 0.001 DRK.";
      break;
   }

   if($error):
?>
<div class="error">
<?php echo $error; ?>
</div>

<?php endif; ?>

<div id="orderform">
<form action="./pay.php" method="post" id="orderform" name="orderform" onclick="" class="form-horizontal">
<input type="hidden" name="exch_rate" value="<?php $exch_rate; ?>" />
<input type="hidden" name="act" value="pay" />

<fieldset class="">
<legend><?php echo SBTCP_COIN; ?>Pay</legend>

<?php if($oid != 'false'): ?>
<label for="oid">Invoice ID:</label>
<input type="text" name="oid" value="<?php echo $oid;?>" size="8" style="text-align:right;" />
<br /><br />
<?php endif; ?>

<label for="tot_drk">Total <?php echo SBTCP_COIN; ?>:</label>
<input type="text" name="tot_drk" value="<?php echo $tot_drk;?>" size="8" style="text-align:right;" /> <b><?php echo SBTCP_COIN; ?></b>
<br />
<center><em style="font-weight:bold;color:#999;">or</em></center>

<label for="tot_usd">Total USD:</label>
<input type="text" name="tot_usd" value="<?php echo number_format($tot_usd, 2);?>" size="8" style="text-align:right;" /> <b>USD</b>
<br /><br />

<?php if($oemail != 'false'): ?>
<label for="oemail">Email:</label>
<input type="text" name="oemail" value="<?php echo $oemail;?>" size="20" style="text-align:left;" />
<br /><br />
<?php endif; ?>

<?php if($odesc != 'false'): ?>
<label for="odesc">Description:</label>
<textarea name="odesc" rows="3" class="desc"><?php echo $odesc;?></textarea>
<br /><br />
<?php endif; ?>

<div class="wide">
<button type="submit" name="submit" id="submit" class="btn btn-primary">Submit</button>
</div>

<br />
<div class="formfooter">

</div>
</fieldset>

</form>


