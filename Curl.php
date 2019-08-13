<?php

namespace CodingLiki\Curl;

/**
 * Класс для работы с запросами с помощью curl
 */
class Curl{
    public static $base_url = "";
    public static $base_headers = [];
    public static $curl_object = null;
    /**
     * Реализация POST запроса с параметрами
     *
     * @param [type] $url
     * @param array $data
     * @param array $headers
     * @return void
     */
    public static function post($url, $data=[], $headers = [], $return_transfer = true){
        self::initCurl($url, $headers, $return_transfer);

        self::setCurlOpt(CURLOPT_POST, 1);
        self::setCurlOpt(CURLOPT_POSTFIELDS,$data);
        
        $server_output = curl_exec(self::$curl_object);

        self::closeCurl();
        return $server_output;
    }

    /**
     * Реализация GET запроса
     * параметры указывать самостоятельно в url
     *
     * @param [type] $url
     * @return void
     */
    public static function get($url,$headers = [], $return_transfer = true){
        self::initCurl($url, $headers, $return_transfer);
    
        $server_output = curl_exec(self::$curl_object);
        self::closeCurl();
        return $server_output;
    }

    public static function initCurl($url = "", $headers = [], $return_transfer = true){
        self::$curl_object = curl_init();
        if ($url != "") {
            self::setUrl($url);
        }
        
        self::setHeaders($headers);
        
        if($return_transfer){
            self::setRT();
        }
    }

    public static function closeCurl(){
        curl_close (self::$curl_object);
        self::$curl_object = null;
    }

    public static function setUrl($url){
        self::setCurlOpt(CURLOPT_URL, self::$base_url.$url);
    }

    public static function setRT(){
        self::setCurlOpt( CURLOPT_RETURNTRANSFER, true);
    }

    public static function setHeaders($headers){
        self::setCurlOpt( CURLOPT_HTTPHEADER, self::prepareHeaders($headers));        
    }
    
    public static function prepareHeaders($headers){
        $all_headers = $headers + self::$base_headers;
        $prepared_headers = [];

        foreach ($all_headers as $key => $header) {
            $prepared_headers[] = "$key: $header";
        }
        
        return $prepared_headers;
    }
    
    public static function setCurlOpt($opt, $value){
        curl_setopt(self::$curl_object, $opt, $value);
    }

    public static function setCurlOptions($options){
        if(self::$curl_object == null){
            self::initCurl();
        }
        foreach($options as $key => $option){
            switch($key){
                case CURLOPT_HTTPHEADER:
                    self::setHeaders($option);
                    break;
                case CURLOPT_URL:
                    self::setUrl($option);
                    break;
                default:
                    self::setCurlOpt($key, $option);
            }
        }
    }

}