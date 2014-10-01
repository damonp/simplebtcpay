<?php

class API
{

   protected $use_avg = false;

   public function curl($url, $payload=null)
   {

      $options = array(
                     CURLOPT_URL             => $url,
                     CURLOPT_HEADER          => false,
                     CURLOPT_FORBID_REUSE    => true,
                     CURLOPT_FRESH_CONNECT   => true,
                     CURLOPT_TIMEOUT         => 4,
                     CURLOPT_RETURNTRANSFER  => true,
                     CURLOPT_USERAGENT       => 'simplebtcpay v1',
                     CURLOPT_COOKIESESSION   => true,
                     CURLOPT_SSL_VERIFYPEER  => false,
                     );

      $headers[] = 'Content-Type: application/json; charset=utf-8';
      $headers[] = 'Accept-Language: en-US';
      $headers[] = 'Expect:';

      if($payload) {
         $payload_json = json_encode($payload);
         $options[CURLOPT_CUSTOMREQUEST] = 'POST';
         $options[CURLOPT_POST]  = true;
         $options[CURLOPT_POSTFIELDS]    = $payload_json;
         $headers[]  = 'Content-Length: ' . strlen($payload_json);
      }

      $options[CURLOPT_HTTPHEADER] = $headers;

      $ch = curl_init();
      curl_setopt_array($ch, $options);
      $response = curl_exec($ch);

      if(curl_errno($ch)){
         throw new Exception(curl_error($ch));
      }

      $response = json_decode($response);
      //error_log('API.curl.response: '. print_r($response,true));

      return $response;
   }
}
