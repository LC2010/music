<?php
/*
 * Copyright (c) 2010,  新浪网运营部-网络应用开发部
 * All rights reserved.
 * @description: 图片表DB类
 * @author: **
 * @date: 2010/07/15
 * @version: 1.0
 */
class ItemModelDB extends MyDB {
    
    //field_arr start
    protected $field_arr = array (
      'id' => 
      array (
        'name' => 'ID',
        'type' => 'bigint',
        'max_length' => '10',
        'validate' => 0,
      ),
      'uid' => 
      array (
        'name' => 'uid',
        'type' => 'bigint',
        'max_length' => '10',
        'validate' => 0,
      ),
      'image' => 
      array (
        'name' => '原图',
        'type' => 'varchar',
        'max_length' => '255',
        'validate' => 0,
      ),
      'face' => 
      array (
        'name' => '封面',
        'type' => 'varchar',
        'max_length' => '255',
        'validate' => 0,
      ),
      'desc' => 
      array (
        'name' => '描述',
        'type' => 'varchar',
        'max_length' => '255',
        'validate' => 0,
      ),
      'like' => 
      array (
        'name' => '喜欢',
        'type' => 'int',
        'max_length' => '10',
        'validate' => 0,
      ),
      'comment' => 
      array (
        'name' => '评论',
        'type' => 'int',
        'max_length' => '10',
        'validate' => 0,
      ),
      'click' => 
      array (
        'name' => '浏览',
        'type' => 'int',
        'max_length' => '10',
        'validate' => 0,
      ),
      'create_time' => 
      array (
        'name' => '创建时间',
        'type' => 'int',
        'max_length' => '10',
        'validate' => 0,
      ),
    );
        //field_arr end 3590a6bc6f918a4ac7c62feada24b45c1
    private $mc = null;
    public function __construct($dbname = null, array $db_config = array()) {
        parent::__construct($dbname, $db_config);
        parent::setTableName("item");
        $this->mc = new MyMemcache();
    }
    
    public function getItemList( $page=1, $size = 12 ){
        $mc_key = __CLASS__.__METHOD__.$page.$size;
        $data   = $this->mc->get($mc_key);
        if(empty($data)){
            $start = ($page-1)*$size;
            $sql   = "select * from ".$this->getTableName()." order by create_time desc limit {$start}, {$size}";
            $data  = $this->getData($sql);
            $this->mc->set($mc_key, $data, 5);
        }
        return $data;
    }

    public function getItemById( $id ){
        $mc_key = __CLASS__.__METHOD__.$id;
        $data   = $this->mc->get($mc_key);
        if(empty($data)){
            $sql   = "select * from ".$this->getTableName()." where id=?";
            $data  = $this->getRow($sql, array($id));
            $this->mc->set($mc_key, $data, 300);
        }
        return $data;
    }

    public function updateClick($id){
        $sql  = "update ".$this->getTableName()." set click=click+1 where id={$id}";
        $this->exec($sql);
    }

    public function getNextItemId($id){
        $mc_key = __CLASS__.__METHOD__.$id;
        $data   = $this->mc->get($mc_key);
        if(empty($data)){
            $sql = "select * from ".$this->getTableName()." where id<? order by id desc limit 1";
            $data = $this->getRow($sql, array($id));
            if(empty($data)){
                $sql  = "select * from ".$this->getTableName()." where id>? order by id limit 1";
                $data = $this->getRow($sql, array($id)); 
            } 
            if($data){
                $this->mc->set($mc_key, $data, 300);
            }    
            return $data;
        }
    }
}
?>
