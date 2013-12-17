<?php
/*
 * Copyright (c) 2010,  新浪网运营部-网络应用开发部
 * All rights reserved.
 * @description: 音乐表DB类
 * @author: **
 * @date: 2010/07/15
 * @version: 1.0
 */
class PlayModelDB extends MyDB {
    
    //field_arr start
    protected $field_arr = array (
      'id' => 
      array (
        'name' => 'ID',
        'type' => 'int',
        'max_length' => '10',
        'validate' => 0,
      ),
      'songname' => 
      array (
        'name' => '歌名',
        'type' => 'varchar',
        'max_length' => '25',
        'validate' => 0,
      ),
      'artist' => 
      array (
        'name' => '作者',
        'type' => 'varchar',
        'max_length' => '25',
        'validate' => 0,
      ),
      'album' => 
      array (
        'name' => '专辑',
        'type' => 'varchar',
        'max_length' => '25',
        'validate' => 0,
      ),
      'img' => 
      array (
        'name' => '封面',
        'type' => 'varchar',
        'max_length' => '200',
        'validate' => 0,
      ),
      'mp3' => 
      array (
        'name' => '文件',
        'type' => 'varchar',
        'max_length' => '200',
        'validate' => 0,
      ),
      'publish_time' => 
      array (
        'name' => '时间',
        'type' => 'int',
        'max_length' => '10',
        'validate' => 0,
      ),
    );
        //field_arr end 16ddd5a7c6acc853fc7acd98161eee2a1
    
    public function __construct($dbname = null, array $db_config = array()) {
        parent::__construct($dbname, $db_config);
        parent::setTableName("play");
    }

    public function get($id){
        $sql = "select * from ".$this->getTableName()." where id<>?";
        $data = $this->getData($sql, array($id));
        shuffle($data);
        return $data;
    }
    
    public function getMp3ById($id){
        $sql = "select * from ".$this->getTableName()." where id=?";
        $data = $this->getRow($sql, array($id));
        return $data;
    } 

}
?>
