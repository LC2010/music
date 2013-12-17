<?php

class UserinfoModelDB extends MyDB {
    
    //field_arr start
    protected $field_arr = array (
      'id' => 
      array (
        'name' => 'ID',
        'type' => 'int',
        'max_length' => '10',
        'validate' => 0,
      ),
      'uid' => 
      array (
        'name' => '微博UID',
        'type' => 'bigint',
        'max_length' => '10',
        'validate' => 0,
      ),
      'screen_name' => 
      array (
        'name' => '昵称',
        'type' => 'varchar',
        'max_length' => '50',
        'validate' => 0,
      ),
      'profile_image_url' => 
      array (
        'name' => '头像',
        'type' => 'varchar',
        'max_length' => '255',
        'validate' => 0,
      ),
      'access_token' => 
      array (
        'name' => 'token',
        'type' => 'varchar',
        'max_length' => '100',
        'validate' => 0,
      ),
      'create_time' => 
      array (
        'name' => '注册时间',
        'type' => 'int',
        'max_length' => '10',
        'validate' => 1,
      ),
    );
        //field_arr end 75e337e1ffdf8317eff51a30b98839d61
    private  $mc = null;
    public function __construct($dbname = null, array $db_config = array()) {
        parent::__construct($dbname, $db_config);
        parent::setTableName("userinfo");
        $this->mc = new MyMemcache();
    }

    /**
     *  是否已经记录
     *
     */
    public function isOpened($uid){
        $mc_key = __CLASS__.__METHOD__.$uid;
        $data   = $this->mc->get($mc_key);
        if(empty($data)){
            $sql  = "select id from ".$this->getTableName()." where uid=?";
            $ret  = $this->getRow($sql, array($uid));
            $data = true;
            if(empty($ret)){
                $data = false;
            }
            $this->mc->set($mc_key, $data, 300);
        }
        return $data;
    }

    /**
     * 单个用户的信息
     *
     */ 
    public function getUserinfoByUid($uid){
        $mc_key = __CLASS__.__METHOD__.$uid;
        $data   = $this->mc->get($mc_key);
        if(empty($data)){
            $sql  = "select uid,screen_name,profile_image_url from ".$this->getTableName()." where uid=?";
            $data  = $this->getRow($sql, array($uid));
            if($data){
                $this->mc->set($mc_key, $data, 300);
            }
        }
        return $data;
    }
    
    /**
     *
     * 批量获取用户信息
     *
     */
    public function getUserinfoByUids($uid_arr=array(), $fields=array('*')){
        $fields = implode(',', $fields);
        $uids   = implode(',', $uid_arr);
        $mc_key = __CLASS__.__METHOD__.'1'.$uids.$fields;
        $data   = $this->mc->get($mc_key);
        if(empty($data)){
            $sql  = "select {$fields} from ".$this->getTableName()." where uid in({$uids})";
            $data = $this->getData($sql);
            if($data){
                $this->mc->set($mc_key, $data, 300);
            }
        } 
        return $data;
    }
}
?>
