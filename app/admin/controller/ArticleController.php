<?php
/**
 * All rights reserved.
 * 文章
 * @author            **
 * @package            /
 * @version            $Id: $2011-2-3
 */

class ArticleController extends DefaultController {

    /* 
     * 普通列表展示
     * 对应参数生成SQL，如url参数为：name=test，生成对应SQL为：`name` = 'test'（name必须为表字段）
     */
    public function view() {
        $articleDB = new ArticleModelDB();
        //普通条件，检查GET参数
        $fieldArr = $articleDB->getFields();
        $where = $whereArr = array();
        foreach ($fieldArr as $v) {
            if (array_key_exists($v, $_GET) && $_GET[$v] !== null) {
                $where[] = "`{$v}` = ?";
                $whereArr[] = $_GET[$v];
            }
        }
        $whereStr = count($where) > 0 ? implode(" AND ", $where) : '1=1';
        if (array_key_exists('_search_field', $_GET) && $_GET['_search_field'] != '' && in_array($_GET['_search_field'], $fieldArr, true) && array_key_exists('_search_keyword', $_GET) && $_GET['_search_keyword'] != '') {
            $whereStr = $whereStr == '1=1' ? $_GET['_search_field'] . " LIKE ?" : $whereStr . " AND " . $_GET['_search_field'] . " LIKE ?";
            $whereArr[] = "%" . $_GET['_search_keyword'] . "%";
        }
        //排序条件
        $orderStr = '`id` DESC';
        if (!empty($_POST['order']))
        {
            foreach($_POST['order'] as $k=>$v){
                $orderArr[] = "`{$k}` {$v}";
            }
            $orderStr = implode(',', $orderArr);
        }
        //查询操作
        $sql = "SELECT * FROM `".$articleDB->getTableName()."` WHERE {$whereStr} ORDER BY {$orderStr}";
        $data = $articleDB->getData($sql, $whereArr, 20);
        
        $pageStr = $articleDB->getPageStr();
        //读取外键数据
        $kindDictFkArr = DictConfig::$kind;
        $this->setView('kindDictFkArr',$kindDictFkArr);
        
        //模版显示
        $this->setView('pageStr', $pageStr);
        $this->setView('data', $data);
        $this->display('Article.html');
    }
    
    /* 
     * 指定条件，获取一条数据
     * 主要用于数据编辑时form表单中的数据获取
     */
    public function get() {
        $articleDB = new ArticleModelDB();
        $fieldArr = $articleDB->getFields();
        $where = $whereArr = array();
        foreach ($fieldArr as $v) {
            if (array_key_exists($v, $_GET) && $_GET[$v] !== null) {
                $where[] = "`{$v}` = ?";
                $whereArr[] = $_GET[$v];
            }
        }
        if (count($where) == 0) {
            Message::showError('没有条件');
        }
        $whereStr = implode(" AND ", $where);
        $sql = "SELECT * FROM `".$articleDB->getTableName()."` WHERE {$whereStr}";
        $row = $articleDB->getRow($sql, $whereArr);
        $row['publish_time'] = date('Y-m-d', $row['publish_time']);
        
        $row ? Message::showSucc('获取单条文章数据', $row) : Message::showError('没有获取到指定数据，可能已被删除', $row);
    }
    
    /* 
     * 数据插入
     * 根据form表单的name自动匹配数据表字段进行插入
     */
    public function create() {
        $articleDB = new ArticleModelDB();
        $fieldArr = $articleDB->getFields();
        $insertArr = array();
        foreach ($fieldArr as $v) {
            if (array_key_exists($v, $_POST) && $_POST[$v] !== null) {
                $insertArr[$v] = $_POST[$v];
            }
        }
        $insertArr['publish_time'] = strtotime($_POST['publish_time']);
        
        $rs = $articleDB->insert($insertArr);
        Log::write($this->adminUserName, $this->ip, $articleDB->insertId(), self::$controller . "__" . self::$action, $rs);
        $rs ? Message::showSucc('添加成功') : Message::showError('添加失败：' . implode(" ", $articleDB->getErrorInfo()));
    }

    /* 
     * 数据更新
     * 根据form表单的name自动匹配数据表字段进行更新，以主键为条件
     */
    public function update() {
        $articleDB = new ArticleModelDB();
        $updateArr = $whereArr = array();
        $whereArr['id'] = $_GET['id'];
        
        $sql = "SELECT * FROM `".$articleDB->getTableName()."` WHERE `id` = ?";
        $row = $articleDB->getRow($sql, array_values($whereArr));
        
        $fieldArr = $articleDB->getFields();
        foreach ($fieldArr as $v) {
            if (in_array($v, array('id'))) {continue;}//跳过主键
            if (array_key_exists($v, $_POST) && $_POST[$v] !== null) {
                $updateArr[$v] = $_POST[$v];
            }
        }
        $updateArr['publish_time'] = strtotime($_POST['publish_time']);
        
        $changeNewRow = array_diff_assoc($updateArr, $row);//获取被修改字段数据
        if (empty($changeNewRow)) {
            Message::showSucc('提交成功，数据无修改');
        }
        $rs = $articleDB->update($changeNewRow, $whereArr);
        //获取被修改数据写入日志
        $changeOldRow = array_diff_assoc(array_diff_assoc($row, $updateArr),array_diff_key($row, $updateArr));
        Log::write($this->adminUserName, $this->ip, $_GET['id'], self::$controller . "__" . self::$action, $rs, Log::arrayToLog($changeOldRow));
        //列表无刷新显示外键数据获取
        if(isset($changeNewRow['kind'])) {
            $changeNewRow['kind'] = DictConfig::$kind[$changeNewRow['kind']];
        }
        if(isset($changeNewRow['publish_time'])) {
            $changeNewRow['publish_time'] = date('Y-m-d', $changeNewRow['publish_time']);
        }
        
        //更新提示
        $rs ? Message::showSucc('更新成功', $changeNewRow) : Message::showError('更新失败：' . implode(" ", $articleDB->getErrorInfo()));
    }

    /* 
     * 数据删除
     * 以主键为条件
     */
    public function delete() {
        $articleDB = new ArticleModelDB();
        $whereArr = array();
        $whereArr['id'] = $_GET['id'];
        
        $sql = "SELECT * FROM `".$articleDB->getTableName()."` WHERE `id` = ?";
        $row = $articleDB->getRow($sql, array_values($whereArr));
        
        $rs = $articleDB->delete($whereArr);
        Log::write($this->adminUserName, $this->ip, $_GET['id'], self::$controller . "__" . self::$action, $rs, Log::arrayToLog($row));
        $rs ? Message::showSucc('删除成功') : Message::showError('删除失败：' . implode(" ", $articleDB->getErrorInfo()));
    }

    /* 
     * 数据逻辑删除
     * 以主键为条件，字段内必须有逻辑删除标识字段“delete”，0为正常，1为删除。
     */
    public function logicDelete() {
        $articleDB = new ArticleModelDB();
        $updateArr = array('' => 1);
        $whereArr = array();
        $whereArr['id'] = $_GET['id'];
        
        $rs = $articleDB->update($updateArr, $whereArr);
        Log::write($this->adminUserName, $this->ip, $_GET['id'], self::$controller . "__" . self::$action, $rs);
        $rs ? Message::showSucc('删除成功') : Message::showError('删除失败：' . implode(" ", $articleDB->getErrorInfo()));
    }

    public function logicResume() {
        $articleDB = new ArticleModelDB();
        $updateArr = array('' => 0);
        $whereArr = array();
        $whereArr['id'] = $_GET['id'];
        
        $rs = $articleDB->update($updateArr, $whereArr);
        Log::write($this->adminUserName, $this->ip, $_GET['id'], self::$controller . "__" . self::$action, $rs);
        $rs ? Message::showSucc('恢复成功') : Message::showError('恢复失败：' . implode(" ", $articleDB->getErrorInfo()));
    }
    
    public function edit(){
        $id = $_GET['id'];
        $ArticleDB = new ArticleModelDB();
        $article   = $ArticleDB->getArticleById($id);
        $this->setView('article', $article);
        $this->display('Edit.html');
    }

    public function doEdit(){
        $id      = $_POST['id'];
        $content = $_POST['content'];
        $update['content'] = $content;
        $where['id']       = $id;
        $ArticleDB = new ArticleModelDB();
        $ArticleDB->update($update, $where);
        $url = "http://admin.mayhope.com/?p=admin&s=article&a=view&id=".$id;
        header("Location:$url");
    }
}
?>
