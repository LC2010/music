<?php
/*
 * Copyright (c) 2010,  新浪网运营部-网络应用开发部
 * All rights reserved.
 * @description: 评论表DB类
 * @author: **
 * @date: 2010/07/15
 * @version: 1.0
 */
class CommentModelDB extends MyDB {
    
    //field_arr start
    protected $field_arr = array (
      'id' => 
      array (
        'name' => 'ID',
        'type' => 'int',
        'max_length' => '10',
        'validate' => 0,
      ),
      'art_id' => 
      array (
        'name' => '图片',
        'type' => 'int',
        'max_length' => '10',
        'validate' => 0,
      ),
      'from_uid' => 
      array (
        'name' => 'FUID',
        'type' => 'bigint',
        'max_length' => '10',
        'validate' => 0,
      ),
      'to_uid' => 
      array (
        'name' => 'TUID',
        'type' => 'bigint',
        'max_length' => '10',
        'validate' => 0,
      ),
      'comment' => 
      array (
        'name' => '评论',
        'type' => 'varchar',
        'max_length' => '500',
        'validate' => 0,
      ),
      'pid' => 
      array (
        'name' => 'PID',
        'type' => 'int',
        'max_length' => '10',
        'validate' => 0,
      ),
      'create_time' => 
      array (
        'name' => '时间',
        'type' => NULL,
        'max_length' => NULL,
        'validate' => 0,
      ),
    );
        //field_arr end 710a9dccb4b1a9f9231082b63f333e381
    
    public function __construct($dbname = null, array $db_config = array()) {
        parent::__construct($dbname, $db_config);
        parent::setTableName("comment");
    }

    public function isExist($from_uid, $comment, $art_id){
        $sql  = "select id from ".$this->getTableName()." where from_uid=? and comment=? and art_id=?";
        $data = $this->getRow($sql, array($from_uid, $comment, $art_id));
        if(empty($data)){
            return false;
        }else{
            return true;
        }
    }    
    public function getCommentList($art_id){
        $sql  = "select * from ".$this->getTableName()." where art_id=?";
        $data = $this->getData($sql, array($art_id));
        return $data;
    }
}
?>
