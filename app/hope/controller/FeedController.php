<?php
/**
 *  @Copyright (c) 2013  初见 
 *  @func      首页控制器 
 *  @time      2013/08/24 12:41
 *  @author    Xu Yong < xuyong616@gmail.com >
 *  @version   Id:1.0
 */
class FeedController extends DefaultController{
	
	public function view(){
        $uid = $this->isLogin();
        $UserinfoDB = new UserinfoModelDB();
        if($uid){
            $userinfo = $UserinfoDB->getUserinfoByUid($uid);
            $this->setView('userinfo', $userinfo);
        } 
        $ItemDB = new ItemModelDB();
        $list   = $ItemDB->getItemList(1, 12);
        $uid_arr = array();
        foreach($list as $key=>$value){
            if(!in_array($value['uid'], $uid_arr))
                $uid_arr[] = $value['uid'];
        }
        $userinfo = $UserinfoDB->getUserinfoByUids($uid_arr);
        foreach($userinfo as $key=>$value){
            $value['profile_image_url'] = str_replace('/50/', '/180/', $value['profile_image_url']);
            $userinfo_arr[$value['uid']] = $value;
        }
        foreach($list as $key=>$value){
            $list[$key]['userinfo'] = $userinfo_arr[$value['uid']]; 
            $list[$key]['timePass'] = $this->timePass($value['create_time']);
        }
        Common::debug($userinfo, 'userinfo');
        Common::debug($list, 'list');
        $this->setView('url', 'http://mayhope.com');
        $this->setView('list', $list);
        $this->setView('uid', $uid);
        $this->display('feed.html');
	}	
}
