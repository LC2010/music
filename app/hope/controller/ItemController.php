<?php
/**
 *  @Copyright (c) 2013  初见  
 *  @func      图片展示 
 *  @time      2013/08/24 12:41
 *  @author    Xu Yong < xuyong616@gmail.com >
 *  @version   Id:1.0
 */
class ItemController extends DefaultController{
    
    public function view()
    {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if(empty($id)){
             Message::showError('地址有误', array(), 'http://mayhope.com', 3); 
        }else{
            $ItemDB = new ItemModelDB();
            $ItemModel = new ItemModel(); 
            $CommentDB = new CommentModelDB();  
            $ItemDB->updateClick($id);
            $uid = $this->isLogin();
            $UserinfoDB = new UserinfoModelDB();
            if($uid){
                $userinfo = $UserinfoDB->getUserinfoByUid($uid);
                $this->setView('userinfo', $userinfo);
            }else{
                $uid = 0;
            }
            $data = $ItemDB->getItemById($id);
            $data['userinfo'] = $UserinfoDB->getUserinfoByUid($data['uid']); 
            if(empty($data)){
                Message::showError('图片不存在', array(), 'http://mayhope.com');
            }
            $good = $ItemModel->getGoodById($id);
            $is_good = $ItemModel->isGooded($id, $uid);
            if($is_good){
                $is_good = 1;
            }else{
                $is_good = 0;
            }
            $comment = $CommentDB->getCommentList($id);
            foreach($comment as $key=>$value){
                $comment[$key]['create_time'] = date('M d Y, g:i A', strtotime($value['create_time']));
                $comment[$key]['userinfo'] = $UserinfoDB->getUserinfoByUid($value['from_uid']);
            }
            $this->setView('comment', $comment);
            $this->setView('is_good', $is_good);
            $this->setView('good', $good);
            Common::debug($data);
            $url = 'http://mayhope.com/item/'.$id.'.html';
            $this->setView('url', $url);
            $this->setView('data', $data); 
            $this->setView('uid', $uid);
            $this->setView('id', $id);
            $this->display('item.html');        
        } 
    }

    public function upload()
    {
            $uid = $this->isLogin();
            $UserinfoDB = new UserinfoModelDB();
            if($uid){
                $userinfo = $UserinfoDB->getUserinfoByUid($uid);
                $this->setView('userinfo', $userinfo);
            }
            $this->setView('url', 'http://mayhope.com/item/upload');
            $this->setView('uid', $uid);
            $this->display('upload.html');        
    }

    public function doUpload(){
       
        Common::debug($_FILES); 
        if(isset($_FILES["Filedata"]))
        { 
                $Filedata = $_FILES["Filedata"];
                if(is_uploaded_file($Filedata["tmp_name"]))
                { 
                        $info = pathinfo($Filedata['name']);
                        $ext  = $info['extension'];
                        $key       = md5(Ip::getClientIp().uniqid().$Filedata['name'].time());
                        $filename  = $key.'.'.$ext;
                        //暂存缓存目录，Imagick处理完转到images目录
                        $tmp_file  = PATH_CACHE.'images/'.$filename; 
                        if(move_uploaded_file($Filedata['tmp_name'], $tmp_file)){
                                $img = new Imagick($tmp_file);
                                $width  = $img->getImageWidth();
                                $height = $img->getImageHeight();
                                //如果是竖图
                                if($height>=$width){
                                    
                                     $dest_show  = './images/show/'.$key.'.'.$ext;
                                     if($width>=600){
                                         $img->thumbnailImage(600, 0); 
                                     }  
                                     $img->writeImage($dest_show);
                                     $dest_face = './images/face/'.$key.'.'.$ext;
                                     if($width<400){
                                        Message::showError('error');
                                     }
                                     $img->thumbnailImage(400, 0);
                                     $img->cropImage(400 , 400, 0, 0);
                                     $img->writeImage($dest_face);         
                                 }
                                 //横图
                                 else{
                                     
                                     $dest_show  = './images/show/'.$key.'.'.$ext;
                                     if($width>=600){
                                         $img->thumbnailImage(600, 0);
                                     }  
                                     $img->writeImage($dest_show);
                                     $dest_face = './images/face/'.$key.'.'.$ext;
                                     if($height<400){
                                        Message::showError('error');
                                     }
                                     $img->thumbnailImage(0, 400);
                                     $w     = $img->getImageWidth();
                                     $x     = ceil(($w-400)/2); 
                                     $img->cropImage(400 , 400, $x, 0);
                                     $img->writeImage($dest_face);                               
                                 }                  
                                 $dest_face = ltrim($dest_face, '.');                                  
                                 $dest_show = ltrim($dest_show, '.');
                                 $data =array(
                                     'img_face'=>'http://mayhope.com'.$dest_face,
                                     'img_show'=>'http://mayhope.com'.$dest_show, 
                                     'height'=>$height, 
                                     'width'=>$width
                                 );
                                 Message::showSucc('ok', $data); 
                        }
                }
        }  
    }

    /**
     *  提交表单
     *
     */ 
    public function submit(){
         $image_face = htmlspecialchars($_POST['image_face']);
         $image_show = htmlspecialchars($_POST['image_show']); 
         $desc       = htmlspecialchars($_POST['desc']);
         $uid        = htmlspecialchars($_POST['uid']);
         $url        = "http://mayhope.com";
         if($image_face!=''&&$image_show!=''){
             $ItemDB     = new ItemModelDB();
             $value['face']    = $image_face;
             $value['image']   = $image_show;
             $value['uid']     = $uid; 
             $value['desc']    = $desc;
             $value['like']    = 0;
             $value['comment'] = 0;
             $value['click']   = 0;
             $value['create_time'] = time();
             $ItemDB->insert($value);
             $id = $ItemDB->insertId();
             $url = "http://mayhope.com/item/".$id.".html";
         }
         header("Location:{$url}");
    }
    
    public function lists(){
        $page = $_POST['page'];
        $ItemDB = new ItemModelDB();
        $UserinfoDB = new UserinfoModelDB();
        $list   = $ItemDB->getItemList($page);
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

        Message::showSucc('ok', $list);
    }
    
    /**
     *  赞
     * 
     */  
     public function good(){
         $uid = isset($_POST['uid']) ? trim($_POST['uid']) : 0;
         $id  = isset($_POST['id']) ? trim($_POST['id']) : 0;
         $count_key = 'count_'.$id;
         $list_key  = 'list_'.$id;
         $redis     = MyRedis::getInstance();
         $arr       = $redis->lRange($list_key, 0, -1);
         if(!in_array($uid, $arr)){
             if(!$redis->exists($count_key)){   
                $redis->set($count_key, 0);
             }  
             $redis->incrBy($count_key, 1);
             $size = $redis->lSize($list_key);
             if($size>20){
                $redis->rPop($ist_key);
             } 
             $redis->lPush($list_key, $uid);
         }else{
            Message::showError('你已经赞过了');
         }
         $count = $redis->get($count_key);
         $ItemDB     = new ItemModelDB();
         $UserinfoDB = new UserinfoModelDB();
         $ItemDB->update(array('like'=>$count), array('id'=>$id));
         $userinfo = $UserinfoDB->getUserinfoByUid($uid);         
         Message::showSucc('ok', $userinfo);
     } 
    
    /**
     *
     * 取消赞
     *
     */
    public function cancel(){
         $uid = isset($_POST['uid']) ? trim($_POST['uid']) : 0;
         $id  = isset($_POST['id']) ? trim($_POST['id']) : 0;
         $count_key = 'count_'.$id;
         $list_key  = 'list_'.$id;
         $redis     = MyRedis::getInstance();
         $arr       = $redis->lRange($list_key, 0, -1);
         if(in_array($uid, $arr)){
             $redis->decrBy($count_key, 1);
             $redis->lRem($list_key, $uid, 1);
         }
         Message::showSucc('ok');
    }

    /**
     *   注销登录
     *
     */
    public function logout(){
        $r = isset($_GET['r'])?$_GET['r'] : 'http://mayhope.com';
        Session::set('uid', 0); 
        header("Location:{$r}");
    }

    public function next($id){
        $id     = intval($_POST['id']);
        $ItemDB = new ItemModelDB();
        $data   = $ItemDB->getNextItemId($id);
        Message::showSucc('ok', $data);
    }
}
