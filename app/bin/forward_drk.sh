#!/bin/sh
#  Add your DRK address below and add the following cronjob
#  */2 * * * * /full/path/to/forward_drk.sh >/dev/null 2>&1

fee=0.001
balance=$(darkcoind getbalance)
fwd_to_address='YOUR_DRK_RECEIPT_ADDRESS'
echo balance: $balance

if [ $(echo "$balance > 0" | bc) -eq 1 ];then
   fwd_amount=$( echo "scale=8;$balance - $fee" | bc )
   darkcoind sendtoaddress $fwd_to_address $fwd_amount "fwd"
   echo fwd: $fwd_amount to: $fwd_to_address
fi
