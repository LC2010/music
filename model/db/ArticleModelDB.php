<?php
/*
 * Copyright (c) 2010,  新浪网运营部-网络应用开发部
 * All rights reserved.
 * @description: 文章表DB类
 * @author: **
 * @date: 2010/07/15
 * @version: 1.0
 */
class ArticleModelDB extends MyDB {
    
    //field_arr start
    protected $field_arr = array (
      'id' => 
      array (
        'name' => 'ID',
        'type' => 'int',
        'max_length' => '11',
        'validate' => 0,
      ),
      'uid' => 
      array (
        'name' => '用户',
        'type' => 'int',
        'max_length' => '11',
        'validate' => 0,
      ),
      'kind' => 
      array (
        'name' => '分类',
        'type' => 'varchar',
        'max_length' => '20',
        'validate' => 0,
      ),
      'title' => 
      array (
        'name' => '标题',
        'type' => 'varchar',
        'max_length' => '50',
        'validate' => 0,
      ),
      'content' => 
      array (
        'name' => '正文',
        'type' => 'varchar',
        'max_length' => '3000',
        'validate' => 0,
      ),
      'click_count' => 
      array (
        'name' => '点击',
        'type' => 'int',
        'max_length' => '11',
        'validate' => 0,
      ),
      'comment_count' => 
      array (
        'name' => '评论',
        'type' => 'int',
        'max_length' => '11',
        'validate' => 0,
      ),
      'publish_time' => 
      array (
        'name' => '发布时间',
        'type' => 'int',
        'max_length' => '11',
        'validate' => 0,
      ),
    );
        //field_arr end a97933cb399876283b8a06bcf4fcd2991
    private $mc = null;
    public function __construct($dbname = null, array $db_config = array()) {
        parent::__construct($dbname, $db_config);
        parent::setTableName("article");
        $this->mc = new MyMemcache();
    }

    public function getArticleById($id, $fileds = array(' * ')){
        $fields = implode(',', $fileds);
        $sql  = "select {$fields} from ".$this->getTableName()." where id=?";
        $data = $this->getRow($sql, array($id));
        return $data;
    }

    public function getArticleList( $page=1, $size=5, $kind = array()){
        $kind  = empty( $kind ) ? implode(',', array_keys(DictConfig::$kind))  : implode(',', $kind); 
        $sql     = "select * from ".$this->getTableName()." where kind in({$kind}) order by publish_time desc";
        $data    = $this->getData($sql);
        return $data;
    }
}
?>
