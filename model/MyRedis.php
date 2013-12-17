<?php

class MyRedis{

    private static $rs = null;
    
    public static function getInstance(){
        $host = '127.0.0.1';
        $port = '6379';
        $link = md5($host.$port);
        if(!empty(self::$rs[$link])){
          return self::$rs[$link];
        }
        self::$rs[$link] = new Redis();
        self::$rs[$link]->connect($host, $port);
        return self::$rs[$link];
    } 
}
