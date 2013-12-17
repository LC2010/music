<?php
/**
 *  @Copyright (c) 2013  初见 
 *  @func      基础控制器 
 *  @time      2013/08/24 12:41
 *  @author    Xu Yong < xuyong616@gmail.com >
 *  @version   Id:1.0
 */
class DefaultController extends BaseController{
	
	public function __construct($state, $action){
		parent::__construct($state, $action);
        //微博授权地址
        $o = new SaeTOAuthV2( WB_AKEY , WB_SKEY );
        $display = NULL;
        $mobile = 0;
        if($this->isMobile()){
            $display = 'mobile';
            $mobile = 1;
        }
        $code_url = $o->getAuthorizeURL( WB_CALLBACK_URL, 'code', NULL, $display);
        $this->setView('code_url', $code_url );
        $this->setView('mobile', $mobile);
	}		  

    /**
     *  判断登录状态
     *
     */
    public function isLogin(){
        $uid = Session::get('uid');
        if($uid){
            return $uid;
        }else{
            return false;
        }
    }
    
    public function timePass($time){
       
        $oneMinute = 60;
        $oneHour   = 3600;
        $oneDay    = 86400;
        $oneMonth  = 2592000;
        $oneYear   = 31104000;
        $tmp = '';
        $timeCha = time()-$time;
        if($timeCha < $oneMinute){
            $second = $timeCha;
            $tmp = $second."秒前";
        }else if($timeCha >= $oneMinute && $timeCha < $oneHour){
            $minute = floor($timeCha/$oneMinute);
            $tmp    = $minute."分钟之前";
        }else if($timeCha >= $oneHour && $timeCha < $oneDay){
            $hour = floor($timeCha/$oneHour);
            $tmp  = $hour."小时之前";
        }else if($timeCha >= $oneDay && $timeCha < $oneMonth){
            $day  = floor($timeCha/$oneDay);
            $tmp  = $day."天之前";
        }else if($timeCha >= $oneMonth && $timeCha < $oneYear){
            $month = floor($timeCha/$oneMonth);
            $tmp   = $month."月之前";
        }else if($timeCha >=$oneYear){
            $year  = floor($timeCha/$oneYear);
            $tmp   = $year."年之前";
        }
        return $tmp; 
    } 

    public	function isMobile()
	{
	    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
	    if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
	    {
	        return true;
	    }
	    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
	    if (isset ($_SERVER['HTTP_VIA']))
	    {
	        // 找不到为flase,否则为true
	        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
	    }
	    // 脑残法，判断手机发送的客户端标志,兼容性有待提高
	    if (isset ($_SERVER['HTTP_USER_AGENT']))
	    {
	        $clientkeywords = array ('nokia',
	            'sony',
	            'ericsson',
	            'mot',
	            'samsung',
	            'htc',
	            'sgh',
	            'lg',
	            'sharp',
	            'sie-',
	            'philips',
	            'panasonic',
	            'alcatel',
	            'lenovo',
	            'iphone',
	            'ipod',
	            'blackberry',
	            'meizu',
	            'android',
	            'netfront',
	            'symbian',
	            'ucweb',
	            'windowsce',
	            'palm',
	            'operamini',
	            'operamobi',
	            'openwave',
	            'nexusone',
	            'cldc',
	            'midp',
	            'wap',
	            'mobile'
	        );
	        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
	        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
	        {
	            return true;
	        }
	    }
	    return false;
	}	
}
