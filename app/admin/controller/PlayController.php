<?php
/**
 * All rights reserved.
 * 音乐
 * @author            **
 * @package            /
 * @version            $Id: $2011-2-3
 */

class PlayController extends DefaultController {

    /* 
     * 普通列表展示
     * 对应参数生成SQL，如url参数为：name=test，生成对应SQL为：`name` = 'test'（name必须为表字段）
     */
    public function view() {
        $playDB = new PlayModelDB();
        //普通条件，检查GET参数
        $fieldArr = $playDB->getFields();
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
        $sql = "SELECT * FROM `".$playDB->getTableName()."` WHERE {$whereStr} ORDER BY {$orderStr}";
        $data = $playDB->getData($sql, $whereArr, 20);
        
        $pageStr = $playDB->getPageStr();
        //读取外键数据
        
        //模版显示
        $this->setView('pageStr', $pageStr);
        $this->setView('data', $data);
        $this->display('Play.html');
    }
    
    /* 
     * 指定条件，获取一条数据
     * 主要用于数据编辑时form表单中的数据获取
     */
    public function get() {
        $playDB = new PlayModelDB();
        $fieldArr = $playDB->getFields();
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
        $sql = "SELECT * FROM `".$playDB->getTableName()."` WHERE {$whereStr}";
        $row = $playDB->getRow($sql, $whereArr);
        $row['publish_time'] = date('Y-m-d', $row['publish_time']);
        
        $row ? Message::showSucc('获取单条音乐数据', $row) : Message::showError('没有获取到指定数据，可能已被删除', $row);
    }
    
    /* 
     * 数据插入
     * 根据form表单的name自动匹配数据表字段进行插入
     */
    public function create() {
        $playDB = new PlayModelDB();
        $fieldArr = $playDB->getFields();
        $insertArr = array();
        foreach ($fieldArr as $v) {
            if (array_key_exists($v, $_POST) && $_POST[$v] !== null) {
                $insertArr[$v] = $_POST[$v];
            }
        }
        $insertArr['publish_time'] = strtotime($_POST['publish_time']);
        
        $rs = $playDB->insert($insertArr);
        Log::write($this->adminUserName, $this->ip, $_GET['id'], self::$controller . "__" . self::$action, $rs);
        $rs ? Message::showSucc('添加成功') : Message::showError('添加失败：' . implode(" ", $playDB->getErrorInfo()));
    }

    /* 
     * 数据更新
     * 根据form表单的name自动匹配数据表字段进行更新，以主键为条件
     */
    public function update() {
        $playDB = new PlayModelDB();
        $updateArr = $whereArr = array();
        $whereArr['id'] = $_GET['id'];
        
        $sql = "SELECT * FROM `".$playDB->getTableName()."` WHERE `id` = ?";
        $row = $playDB->getRow($sql, array_values($whereArr));
        
        $fieldArr = $playDB->getFields();
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
        $rs = $playDB->update($changeNewRow, $whereArr);
        //获取被修改数据写入日志
        $changeOldRow = array_diff_assoc(array_diff_assoc($row, $updateArr),array_diff_key($row, $updateArr));
        Log::write($this->adminUserName, $this->ip, $_GET['id'], self::$controller . "__" . self::$action, $rs, Log::arrayToLog($changeOldRow));
        //列表无刷新显示外键数据获取
        if(isset($changeNewRow['publish_time'])) {
            $changeNewRow['publish_time'] = date('Y-m-d', $changeNewRow['publish_time']);
        }
        
        //更新提示
        $rs ? Message::showSucc('更新成功', $changeNewRow) : Message::showError('更新失败：' . implode(" ", $playDB->getErrorInfo()));
    }

    /* 
     * 数据删除
     * 以主键为条件
     */
    public function delete() {
        $playDB = new PlayModelDB();
        $whereArr = array();
        $whereArr['id'] = $_GET['id'];
        
        $sql = "SELECT * FROM `".$playDB->getTableName()."` WHERE `id` = ?";
        $row = $playDB->getRow($sql, array_values($whereArr));
        
        $rs = $playDB->delete($whereArr);
        Log::write($this->adminUserName, $this->ip, $_GET['id'], self::$controller . "__" . self::$action, $rs, Log::arrayToLog($row));
        $rs ? Message::showSucc('删除成功') : Message::showError('删除失败：' . implode(" ", $playDB->getErrorInfo()));
    }

    /* 
     * 数据逻辑删除
     * 以主键为条件，字段内必须有逻辑删除标识字段“delete”，0为正常，1为删除。
     */
    public function logicDelete() {
        $playDB = new PlayModelDB();
        $updateArr = array('' => 1);
        $whereArr = array();
        $whereArr['id'] = $_GET['id'];
        
        $rs = $playDB->update($updateArr, $whereArr);
        Log::write($this->adminUserName, $this->ip, $_GET['id'], self::$controller . "__" . self::$action, $rs);
        $rs ? Message::showSucc('删除成功') : Message::showError('删除失败：' . implode(" ", $playDB->getErrorInfo()));
    }

    public function logicResume() {
        $playDB = new PlayModelDB();
        $updateArr = array('' => 0);
        $whereArr = array();
        $whereArr['id'] = $_GET['id'];
        
        $rs = $playDB->update($updateArr, $whereArr);
        Log::write($this->adminUserName, $this->ip, $_GET['id'], self::$controller . "__" . self::$action, $rs);
        $rs ? Message::showSucc('恢复成功') : Message::showError('恢复失败：' . implode(" ", $playDB->getErrorInfo()));
    }
}
?>
