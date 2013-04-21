<?php
require_once('tools.php');


class ClassManageAction extends Action
{

    public function class_index()
    {
        $this->display();
    }
    public function class_list()
    {
        $sql = "select class_id, class_name, create_time, note from T_CLASSES order by create_time asc;";
        $result = Tools::get_query_result($sql);
        echo json_encode($result);
    }

    public function remove_class()
    {
        $json = Tools::request("data");
        $flag = $this->remove_class_from_database($json);
        echo $flag ? 'ok':'failed';
    }

    public function add_class()
    {
        $json = Tools::request("data");
        $flag = $this->add_class_to_database($json);
        echo $flag ? 'ok':'failed';
    }

    /**

    */
    public function remove_class_from_database($classes_json)
    {
        $codes = "''";
        if(Tools::json_is_array($classes_json)){
            $classes_array = json_decode($classes_json, true);
            foreach ($classes_array as $key => $item) {
                $class_id = $item['class_id'];
                $codes .= ",'".$class_id."'";
            }
        }
        else{
            $class = json_decode($classes_json, true);
            $class_id = $class['class_id'];
            $codes .= ",'".$class_id."'";
        }
        $sql_delete = "delete from T_CLASSES where class_id in($codes);";
        //获取属于该班级的用户
        $sql_select_users = "select user_id from T_CLASS_LINK_USER where class_id in($codes);";
        $user_list = Tools::get_query_result($sql_select_users);
        $user_array = array();
        foreach ($user_list as $key => $value) {
            $user_array[] = $value['user_id'];
        }
        $UserManageAction = A('UserManagement');
        $sql_delete .= $UserManageAction->get_sql_remove_user($user_array);
        return Tools::trans_sql($sql_delete);
    }
    public function add_class_to_database($classes_json)
    {
        date_default_timezone_set("Asia/Shanghai");
        $create_time = date("Y-m-d H:i:s");
        $sql_replace ='';

        if(Tools::json_is_array($classes_json)){
            $classes_array = json_decode($classes_json, true);
            foreach ($classes_array as $key => $item) {
                $class_id = $item['class_id'];
                $class_name = $item['class_name'];
                $note = $item['note'];               
                $sql_replace .= "replace into T_CLASSES(class_id, class_name, create_time, note) values('$class_id','$class_name','$create_time','$note');";              
            }
        }
        else{
                $class = json_decode($classes_json, true);
                $class_id = $class['class_id'];
                $class_name = $class['class_name'];
                $note = $class['note'];               
                $sql_replace .= "replace into T_CLASSES(class_id, class_name, create_time, note) values('$class_id','$class_name','$create_time','$note');";              
        } 
        // return $sql_replace;       
        return Tools::trans_sql($sql_replace);
    }



}
?>