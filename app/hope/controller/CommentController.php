<?php
/**
 *  @Copyright (c) 2013  初见 
 *  @func      首页控制器 
 *  @time      2013/08/24 12:41
 *  @author    Xu Yong < xuyong616@gmail.com >
 *  @version   Id:1.0
 */
class CommentController extends DefaultController{
	
    public function submit(){
        $CommentDB = new CommentModelDB();
        $pid      = intval($_POST['pid']);
        $art_id   = intval($_POST['art_id']);
        $comment  = isset($_POST['comment']) ? htmlspecialchars(trim($_POST['comment'])) : '';
        $to_uid   = isset($_POST['to_uid']) ? htmlspecialchars(trim($_POST['to_uid'])) : '';
        $from_uid = $this->isLogin();
        if(!$from_uid){
            Message::showError('你先登录能死么?');
        }else if(empty($comment)){
            Message::showError('说空话？搞毛！');
        }if($CommentDB->isExist($from_uid, $comment, $art_id)){
            Message::showError('我擦，你为毛重复提交? 就知道你想灌水');
        }else{
            $val = array();
            $val['from_uid'] = $from_uid;
            $val['to_uid'] = $to_uid;
            $val['pid']      = $pid;
            $val['art_id']   = $art_id;
            $val['comment']  = $comment; 
            $CommentDB->insert($val);          
        } 
        Message::showSucc('ok');        
    }
}    
