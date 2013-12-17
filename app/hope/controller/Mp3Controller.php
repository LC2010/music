<?php
/**
 *  @Copyright (c) 2013  初见 
 *  @func      首页控制器 
 *  @time      2013/08/24 12:41
 *  @author    Xu Yong < xuyong616@gmail.com >
 *  @version   Id:1.0
 */
class Mp3Controller extends DefaultController{
	
	public function view(){
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if($id){
            $PlayDB = new PlayModelDB();
            $data = $PlayDB->getMp3ById($id);
            $this->setView('data', $data);
        }   
        $this->setView('id', $id); 
        $this->display('play.html');
	}	
		
    public function get(){
        $PlayDB = new PlayModelDB();
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $data = $PlayDB->get($id);
        Message::showSucc('ok', $data);
    }
}
