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
        self::init($url, $headers, $return_transfer);

        self::setOpt(CURLOPT_POST, 1);
        self::setOpt(CURLOPT_POSTFIELDS,$data);
        
        $server_output = self::execute();

        self::close();
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
        self::init($url, $headers, $return_transfer);
    
        $server_output =  self::execute();
        self::close();
        return $server_output;
    }

    public static function init($url = "", $headers = [], $return_transfer = true){
        self::$curl_object = curl_init();
        if ($url != "") {
            self::setUrl($url);
        }
        
        self::setHeaders($headers);
        
        if($return_transfer){
            self::setRT();
        }
    }

    public static function execute(){
        return curl_exec(self::$curl_object);
    }

    public static function close(){
        curl_close (self::$curl_object);
        self::$curl_object = null;
    }

    public static function setUrl($url){
        self::setOpt(CURLOPT_URL, self::$base_url.$url);
    }

    public static function setRT(){
        self::setOpt( CURLOPT_RETURNTRANSFER, true);
    }

    public static function setHeaders($headers){
        self::setOpt( CURLOPT_HTTPHEADER, self::prepareHeaders($headers));        
    }

    public static function prepareHeaders($headers){
        $all_headers = $headers + self::$base_headers;
        $prepared_headers = [];

        foreach ($all_headers as $key => $header) {
            $prepared_headers[] = "$key: $header";
        }
        
        return $prepared_headers;
    }
    
    public static function setOpt($opt, $value){
        curl_setopt(self::$curl_object, $opt, $value);
    }

    public static function setOptions($options){
        if(self::$curl_object == null){
            self::init();
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
                    self::setOpt($key, $option);
            }
        }
    }

}