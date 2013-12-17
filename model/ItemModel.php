<?php

class ItemModel {


    public function getGoodById($id){
        $redis = MyRedis::getInstance();
        $UserinfoDB = new UserinfoModelDB();
        $count_key = 'count_'.$id;
        $list_key  = 'list_'.$id;
        $count     = intval($redis->get($count_key));
        $uid_arr   = $redis->lRange($list_key, 0, -1);
        $data      = array('count'=>$count, 'list'=>array());
        if(!empty($uid_arr)){
            $userinfo = $UserinfoDB->getUserinfoByUids($uid_arr);
            foreach($userinfo as $key=>$value){
                $user_arr[$value['uid']] = $value;
            }
            foreach($uid_arr as $key=>$uid){
                $data['list'][] = $user_arr[$uid];
            }
        }
        return $data;
    }

    public function isGooded($id, $uid){
        $redis = MyRedis::getInstance();
        $list_key = 'list_'.$id;
        $uid_arr   = $redis->lRange($list_key, 0, -1);
        if(in_array($uid, $uid_arr)){
            return true;
        }else{
            return false;
        }
    }
}   
?>
