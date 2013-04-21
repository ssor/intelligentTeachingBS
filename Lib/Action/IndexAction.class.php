<?php
require_once('tools.php');
class IndexAction extends Action
{
    /**
    +----------------------------------------------------------
    * 输出页面
    +----------------------------------------------------------
    */
    public function index()
    {
        $this->assign('user_account', $_SESSION['acount']);
        $this->assign('nick_name', $_SESSION['nick_name']);
        $this->assign('company', C('COMPANY_SIGN'));
        $this->assign('version', C('VERSION'));
        $this->assign('list', $this->left_menu());
        $this->display();        
    }
    public function admin_index()
    {
        $this->assign('user_account', $_SESSION['acount']);
        $this->assign('nick_name', $_SESSION['nick_name']);
        $this->assign('company', C('COMPANY_SIGN'));
        $this->assign('version', C('VERSION'));
        $this->assign('list', $this->tree_admin());
        $this->display('index_viewer');                
    }
    public function overview()
    {
        $this->display();
    }
    public function basic_principle()
    {
        $this->display();
    }
    public function ABCcase()
    {
        $this->display();
    }
    public function tree_document()
    {
        $a_tree = array();
        $items_1 = array(
                array('item_name' => '基本原理', turl => '/Index/basic_principle')
                , array('item_name' => '案例分析', turl => '/Index/ABCcase')
            );
        $a_tree[] = array(id => 'subbase_class_manage', name => '文档目录', pId => 0
                        , open => 'true', items => $items_1);

        return $a_tree;
    }
    public function tree_teacher()
    {
        $a_tree = array();
        $items_1 = array(
                array('item_name' => '班级管理', turl => '/ClassManage/class_index')
                , array('item_name' => '注册审核', turl => '/UserManagement/UserIndex_not_checked')
                , array('item_name' => '学生管理', turl => '/UserManagement/UserIndex')
            );
        $a_tree[] = array(id => 'subbase_class_manage', name => '用户管理', pId => 0
                        , open => 'true', items => $items_1);
                              
        return $a_tree;
    }  
    public function tree_admin()
    {
        $a_tree = array();
        $items_1 = array(
                array('item_name' => '班级管理', turl => '/ClassManage/class_index')
                , array('item_name' => '注册审核', turl => '/UserManagement/UserIndex_not_checked')
                , array('item_name' => '学生管理', turl => '/UserManagement/UserIndex')
            );
        $a_tree[] = array(id => 'subbase_class_manage', name => '用户管理', pId => 0
                        , open => 'true', items => $items_1);
                              
        return $a_tree;
    }
   public function left_menu()
    {
        $a_tree = array();
        $items_1 = array(
                array('item_name' => '个人信息', turl => '/UserManagement/user_info_index')
            );
        $a_tree[] = array(id => 'subbase_class_manage', name => '用户信息', pId => 0
                        , open => 'true', items => $items_1);
                              
        return $a_tree;
    }
 
    //查看作业成本
    //因为要统计每类投入要素的成本，这里把值传递给页面
    public function list_action_final_cost_index()
    {
        $a_items = array();

        $list_all_inventory_resource_groups = $this->get_all_system_inventory_resource_groups();
        for($i = 0; $i < count($list_all_inventory_resource_groups); $i++)
        {
            $group = $list_all_inventory_resource_groups[$i];

            $a_items[$i]['field'] = $group['resource_group_id'].'_fee';
            $a_items[$i]['value'] = $group['resource_group_name'];
        }

        $this->assign('list', $a_items);
        $this->display();
    }
    public function export_result_excel(){

        // $array_result = $this->caculate_final_cost();
        $list_all_inventory_resource_groups = $this->get_all_system_inventory_resource_groups();
        // var_dump($list_all_inventory_resource_groups); return;
        Vendor("PHPExcel.PHPExcel"); 
        $objPHPExcel = new PHPExcel();
        //设置列标题
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', '作业编码')
                                            ->setCellValue('B1','作业名称')
                                            ->setCellValue('c1', '总作业成本')
                                            ->setCellValue('d1', '月处理量')
                                            ->setCellValue('e1','单位处理成本');
        $a_new_column_num = Tools::number_to_ABC_map('f', count($list_all_inventory_resource_groups));
        $a_column_resource_group_id_map = array();
        for($i = 0; $i < count($a_new_column_num); $i++)
        {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($a_new_column_num[$i].'1', $list_all_inventory_resource_groups[$i]['resource_group_name']);
            $a_column_resource_group_id_map[$a_new_column_num[$i]] = $list_all_inventory_resource_groups[$i]['resource_group_id'];
        }
        $a_all_column_num = Tools::number_to_ABC_map('a', 5 + count($list_all_inventory_resource_groups));

        //输入数据
        $array_result = $this->action_final_cost();
        // var_dump($array_result);return;
        // for($i = 0; $i < count($array_result); $i++)
        $i = -1;
        foreach ($array_result as $key => $value) {
            $i ++;
            for($j = 0; $j < count($a_all_column_num); $j++)
            {
                $column_index = $a_all_column_num[$j];
                $row_index = $i + 2;
                switch ($column_index) {
                    case 'A':
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($column_index.$row_index, $value['action_code']);
                        break;
                    case 'B':
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($column_index.$row_index, $value['action_name']);
                        break;
                    case 'C':
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($column_index.$row_index, $value['action_total_fee']);
                        break;
                    case 'D':
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($column_index.$row_index, $value['action_work_capability']);
                        break;
                    case 'E':
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($column_index.$row_index, $value['each_work_fee']);
                        break;
                    default:
                        $group_id = $a_column_resource_group_id_map[$column_index];
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($column_index.$row_index, $value[$group_id.'_fee']);
                        break;
                }
            }
        }

        date_default_timezone_set("Asia/Shanghai");
        $time= date("Y-m-d");
        $xls_name = 'report_'.$time;
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=$xls_name.xls");
        //header('Content-Disposition: attachment;filename="ex.xls"');
        header('Cache-Control: max-age=0');
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }    
    public function get_chart_data()
    {
        $result = $this->action_final_cost();
        // var_dump($result); return;
        $all_action_total_fee = 0;
        foreach ($result as $key => $item) {
            $all_action_total_fee += $item['action_total_fee'];
        }
        $pies = array();
        foreach ($result as $key => $item) {
            $action_total_fee = $item['action_total_fee'];
            $percent = 0;
            if($action_total_fee > 0)
            {
                $percent = number_format(100 * $action_total_fee / $all_action_total_fee, 2);
            }
            $pies[] = array('label' => $item['action_name'].'('.$percent.'%)', 
                            'value' => ($item['action_total_fee'] + 0));
        }
        // var_dump($pies); return;
        $pies_json = json_encode($pies);

        $colors = Tools::randomColors(count($result));
        echo '{
          "elements":[
            {
              "type":      "pie",
              "colours":   '
              .json_encode($colors).',
              "alpha":     0.6,
              "border":    2,
              "start-angle": 35,
              "values" :   '
              .$pies_json.'
            }
          ]
        }';
    }

    public function save_data()
    {
        // $result['status'] = 'ok';
        $json = Tools::request_json("data");
        // $user_id = Tools::get_user_data_set();
        // $flag = true;
        if(count($json) > 0) {
            $flag = $this->save_history_data($json);
            if ($flag == false) {
                $result = Tools::set_result_json('failed', '插入数据时出现异常');
            }
            else
            {
                $result = Tools::set_result_json('ok', '数据保存为 '.$data_id.', 请在作业成本历史页面查看。');
            }
        }
        else
        {
            $result = Tools::set_result_json('failed', '没有数据被保存！');
        }
        echo $result;  
    }   
    public function list_action_final_cost_list()
    {
        $result = $this->action_final_cost();
        // var_dump($result);
        echo json_encode(array_values($result));
        return;
    } 
    /**
    +----------------------------------------------------------
    * 数据计算输出
    +----------------------------------------------------------
    */
    public function save_history_data($json)
    {
        if(!empty($json)){
            $user_id = Tools::get_user_data_set();
            // $flag = true;
            if(count($json) > 0){
                //以日期作为键值，检查现有保存数据，则替换已有的
                date_default_timezone_set("Asia/Shanghai");
                // $time = date("Y-m-d G:i:s");
                $time = date("Y-m-d H:i:s");
                $data_id= 'report_'.date("YmdHis");
                $sql_replace = "replace into T_ACTION_COST_HISTORY(history_id, user_id, time_stamp) values('$data_id', '$user_id', '$time');";
                foreach ($json as $key => $item) {
                    $action_code = $item['action_code'];
                    $action_total_fee = str_replace(',', '', $item['action_total_fee']);
                    $sql_replace .= "replace into T_ACTION_LINK_HISTORY(history_id, action_code, user_id, action_fee) values('$data_id', '$action_code', '$user_id', $action_total_fee);";
                }
                // array_walk($json, function($item, $i) use(&$sql_replace){
                //     $action_code = $item['action_code'];
                //     $action_total_fee = str_replace(',', '', $item['action_total_fee']);
                //     $sql_replace .= "replace into T_ACTION_LINK_HISTORY(history_id, action_code, user_id, action_fee) values('$data_id', '$action_code', '$user_id', $action_total_fee);";
                // });
                return Tools::trans_sql($sql_replace);            
            }
            else{
                return false;
            }
        }
    }
    public function action_final_cost()
    {
        $system_id = Tools::get_system_id();
        $user_data_set = Tools::get_user_data_set();
        $result = CostCaculateAction::action_final_cost($system_id, $user_data_set);
        return $result;        
    }

    public function get_all_system_inventory_resource_groups()
    {
        $system_id = Tools::get_system_id();
        $sql_select_all_inventory_resource_groups = 
            "select resource_group_id, resource_group_name, system_id from T_INVENTORY_RESOURCE_GROUP where system_id = '$system_id';";
        $list_all_inventory_resource_groups = Tools::get_query_result($sql_select_all_inventory_resource_groups);
        return $list_all_inventory_resource_groups;
    }


}
?>