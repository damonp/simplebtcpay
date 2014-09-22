<?php

    include('../lib/config.inc.php');

    //- prefer _POSTed variables
    if(count($_POST) > 0)   {
        $filters = array(
                         'tot_btc'  => FILTER_SANITIZE_STRING,
                         'tot_usd'  => FILTER_SANITIZE_STRING,
                         'oid'      => FILTER_SANITIZE_STRING,
                         'odesc'    => FILTER_SANITIZE_STRING,
                         'oemail'   => FILTER_SANITIZE_STRING,
                         'act'      => FILTER_SANITIZE_STRING,
                        );
        extract(filter_input_array(INPUT_POST, $filters));
    }   elseif(count($_GET) > 0)   {
    //- but accept in _GET if _POST empty
        $filters = array(
                         'tot_btc'  => FILTER_SANITIZE_STRING,
                         'tot_usd'  => FILTER_SANITIZE_STRING,
                         'oid'      => FILTER_SANITIZE_STRING,
                         'odesc'    => FILTER_SANITIZE_STRING,
                         'oemail'   => FILTER_SANITIZE_STRING,
                         'act'      => FILTER_SANITIZE_STRING,
                        );
        extract(filter_input_array(INPUT_GET, $filters));
    }

    //= if both neither is set we can't do anything
    if($tot_usd <= 0  && $tot_btc <= 0) {
        header('Location: ./index.php');
        return;
    }

    include('main.inc.php');

//    if(!$receive_addr = $api->getReceiveAddress())   {
//        error_log('Invalid Receive Address.');
//    }
    $receive_addr = SBTCP_RECEIVE_ADDR;

    if($tot_usd > 0)    {
        $amt = $tot_usd / $exch_rate;
    }   else    {
        $amt = $tot_btc;
    }

    $amt = round($amt, 8);

    $sql =  "REPLACE INTO invoices (oid, total, email, desc, status, btc_usd, tot_usd, tot_btc) ".
            "VALUES (:oid, :total, :email, :desc, 'PENDING', :btc_usd, :tot_usd, :tot_btc) ";

    $qry = $db->prepare($sql);
    $vars = array(
                  ':oid'    => $oid,
                  ':total'  => $amt,
                  ':email'  => $oemail,
                  ':desc'   => $odesc,
                  ':btc_usd'=> $exch_rate,
                  ':tot_usd'=> $tot_usd,
                  ':tot_btc'=> $tot_btc
                  );

    foreach($vars as $key => $val)  {
      $qry->bindValue($key, $val);
    }

    $qry->execute();
    //error_log('qry: '. print_r($qry->errorInfo(),true));

    include('header.inc.php');

?>

<div id="orderform">
<?php if($receive_addr): ?>
<h3 class="">Send <?php echo round($amt, 8); ?> BTC <?php if($tot_usd > 0) echo '($'.number_format($tot_usd, 2).') '; ?>to:</h3>

<?php echo '<img src="./qr.php?addr='.$receive_addr.'&amount='.$amt.'&orderid='.$oid.'" width="264" height="264" class="qrcode">'."\n"; ?>
<div style="padding:.5em;">
<?php echo '<a href="bitcoin:'.$receive_addr.'?amount='.$amt.'&label='.$oid.'" title="">'.$receive_addr.'</a>'."\n";  ?>
</div>

<?php if($oid != '' || $odesc != ''): ?>
<div id="invoice">
  <?php if($oid != ''): ?>
    <div class="invrow">
        <div class="invhead">ID:</div>
        <div class="invitem"><?php echo $oid; ?></div>
    </div>
  <?php endif; ?>
  <?php if($odesc != ''): ?>
    <div class="invrow">
        <div class="invhead">Item:</div>
        <div class="invitem"><?php echo $odesc; ?></div>
    </div>
  <?php endif; ?>
  <?php if($oemail != ''): ?>
    <div class="invrow">
        <div class="invhead">Email:</div>
        <div class="invitem"><?php echo $oemail; ?></div>
    </div>
  <?php endif; ?>
</div>
<?php endif; ?>

<div id="button">
<button type="submit" name="receipt" id="receipt" >Check Receipt</button>
</div>
<br />
<center>
<div id="results">
</div>
</center>

<?php else: ?>

<h3 class="">Error: Please Try Again Later</h3>

<?php endif; ?>

<?php include('footer.inc.php'); ?>

<script>
<!--

$( "#receipt" ).click(function() {
    $("#results" ).show( "slow", function() {
      $("#results").html('<div id="loading"><img src="images/loader.gif" alt="loading" height="20" width="20" align="center" /></div>');
      $.ajax({
        url: "ajax.php?act=check_receipt&addr=<?php echo $receive_addr;?>",
        cache: false,
        success: function(html){
          var json = $.parseJSON(html);
          if(!json.return)  {
            $("#results").html(json.message);
          } else  {
            $("#results").html('Balance: '+json.balance);
          }
        }
      });
    });
});

-->
</script>
</body>
</html>

