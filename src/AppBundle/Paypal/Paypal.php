<?php

namespace AppBundle\Paypal;

class Paypal
{
    private $email;
    private $password;
    private $signature;
    private $url = "https://api-3t.sandbox.paypal.com/nvp";

    public function __construct($email, $password, $signature)
    {
        $this->email     = $email;
        $this->password  = $password;
        $this->signature = $signature;
    }

    public function request($method, $params = array())
    {
        $params = array_merge($params, array(
            'METHOD'    => $method,
            'VERSION'   => 78,
            'USER'      => $this->email,
            'PWD'       => $this->password,
            'SIGNATURE' => $this->signature
        ));

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => $this->url,
            CURLOPT_POST           => 1,
            CURLOPT_POSTFIELDS     => http_build_query($params),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_VERBOSE        => 1
        ));

        $response = curl_exec($curl);

        $responseArray = array();
        parse_str($response, $responseArray);
        if (curl_errno($curl)) {
            var_dump(curl_error($curl));
            curl_close($curl);
            die('ERROR !!!!!!');
        } else {
            curl_close($curl);
            return $responseArray;
        }
        
    }

    public function getPaypalRedirectUrl($token){
        return "https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=".$token;
    }
}