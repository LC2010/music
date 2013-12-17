<?php
/**
 *  @Copyright (c) 2013  初见 
 *  @func      基础控制器 
 *  @time      2013/08/24 12:41
 *  @author    Xu Yong < xuyong616@gmail.com >
 *  @version   Id:1.0
 */
class FrontController extends DefaultController{
    
    public function item(){
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $size = isset($_GET['size']) ? intval($_GET['size']) : 12;
        $ItemDB = new ItemModelDB();
        $data   = $ItemDB->getItemList($page, $size);
        Message::showSucc('ok', $data);
    }
}
