<?php
/**
 *  @Copyright (c) 2013  初见 
 *  @func      首页控制器 
 *  @time      2013/08/24 12:41
 *  @author    Xu Yong < xuyong616@gmail.com >
 *  @version   Id:1.0
 */
class PublicController extends DefaultController{
	
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
        $this->display('index.html');
	}	
		
    public function callback(){
        $UserinfoDB = new UserinfoModelDB(); 
        $o = new SaeTOAuthV2( WB_AKEY , WB_SKEY );
        if (isset($_REQUEST['code'])) {
            $keys = array();
            $keys['code'] = $_REQUEST['code'];
            $keys['redirect_uri'] = WB_CALLBACK_URL;
            try {
                $token = $o->getAccessToken( 'code', $keys ) ;
            } catch (OAuthException $e) {}
        }  
        if ($token) {
            setcookie( 'weibojs_'.$o->client_id, http_build_query($token) );
            $c = new SaeTClientV2( WB_AKEY , WB_SKEY , $token['access_token'] );
            if(is_object($c)){
                  $uid  = $c->get_uid(); 
                  $uid  = $uid['uid'];
                  Session::set('uid', $uid);
                  $wb_user = $c->show_user_by_id($uid);        
                  if($UserinfoDB->isOpened($uid)){
                        $update = array();
                        $update['screen_name'] = $wb_user['screen_name'];
                        $update['profile_image_url'] = $wb_user['profile_image_url'];
                        $update['access_token'] = $token['access_token'];
                        $UserinfoDB->update($update, array('uid'=>$uid));
                  }else{
                        $val= array();
                        $val['screen_name'] = $wb_user['screen_name'];
                        $val['profile_image_url'] = $wb_user['profile_image_url'];
                        $val['uid'] = $wb_user['id'];
                        $val['access_token'] = $token['access_token'];
                        $val['create_time'] = time();
                        $UserinfoDB->insert($val);
                  }
                  $this->setView('wb_user', $wb_user);
            }
        }
        header("Location:http://mayhope.com");
    }
}
