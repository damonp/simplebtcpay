<?php

class ExchRate extends API
{

    protected $use_avg = true;
    protected $vendor = SBTCP_API_VENDOR_EXCH_RATE;
    protected $coin = 'DRk';

    public function __construct($vendor=null)
    {

        $vendor = $vendor ? $vendor:$this->vendor;
        try {
            switch($vendor) {
                default:
                case('cryptoapi'):
                    $ticker = $this->curl('http://drk.cryptoapi.net/index.php');
                    $this->ticker = $ticker->drk_usd;
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
