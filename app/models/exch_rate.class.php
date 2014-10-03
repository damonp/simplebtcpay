<?php

class ExchRate extends API
{

    protected $use_avg = true;
    protected $vendor = SBTCP_API_VENDOR_EXCH_RATE;

    public function __construct($vendor=null)
    {

        $vendor = $vendor ? $vendor:$this->vendor;
        try {
            switch($vendor) {
                default:
                case('bitcoinaverage'):
                    $ticker = $this->curl('https://api.bitcoinaverage.com/ticker/global/USD/');
                    if($this->use_avg)    {
                        $this->ticker = $ticker->{'24h_avg'};
                    }   else    {
                        $this->ticker = $ticker->last;
                    }
                    //$ticker = $this->curl('http://drk.cryptoapi.net/index.php');
                    //$this->ticker = $ticker->drk_usd;
                break;
                case('blockchain'):
                    $ticker = $this->curl('https://blockchain.info/ticker');
                    if($this->use_avg)    {
                        $this->ticker = $ticker->USD->{'15m'};
                    }   else    {
                        $this->ticker = $ticker->USD->last;
                    }
                break;
                case('coinbase'):
                    $ticker = $this->curl('https://coinbase.com/api/v1/prices/sell');
                    $this->ticker = $ticker->amount;
                break;
                case('coindesk'):
                    $ticker = $this->curl('http://api.coindesk.com/v1/bpi/currentprice.json');
                    $this->ticker = $ticker->bpi->USD->rate_float;
                break;
            }

            return $this->ticker;

        } catch (Exception $e) {
            error_log('error: '. print_r($e->getMessage(),true));
            error_log('['.__LINE__.'] : '.__FILE__);
        }
    }

    public function __toString()
    {
        return (string) $this->ticker;
    }
}
