<?php
require_once('tools.php');

// [{"equipmentID":"equip000001","group":0,"row":1,"column":1},{"equipmentID":"equip000002","group":0,"row":2,"column":1}]

class EquipmentMapAction extends Action
{
	//根据班级ID返回相应的教室配置。（暂时认为每个教室，无论什么课，其教室配置情况相同）

	// http://localhost:9011/index.php/EquipmentMap/getEquipmentMap/user/demo1
	// http://localhost:9011/index.php/EquipmentMap/insertEquipmentMap/user/demo1
	// Content-Type: application/x-www-form-urlencoded
	public function getEquipmentMap()
	{
		$class_id = Tools::request('class');
		$sql_select = "select IGROUP, IROW, ICOLUMN, EQUIPEMNTID from T_EQUIPMENT_LOCATION_MAP where class_id = '$class_id';";
		$list = Tools::get_query_result($sql_select);
		$count = count($list);
		for ($i=0; $i < $count; $i++) { 
			$row = $list[$i];
			$result[] = array('group' => $row['IGROUP'], 'row' => $row['IROW'], 
				'column' => $row['ICOLUMN'], 'equipmentID' => $row['EQUIPEMNTID']);			
		}
        echo json_encode($result);
	}
	public function insertEquipmentMap()
	{
		$class_id = Tools::request('class');
		$data = Tools::request_json('data');
		// echo $data; return;
		$count = count($data);
		for ($i=0; $i < $count; $i++) { 
			$item = $data[$i];
			$group = $item['group'];
			$row = $item['row'];
			$column = $item['column'];
			$equipmentID = $item['equipmentID'];
			$sql_insert .= "insert into T_EQUIPMENT_LOCATION_MAP(EQUIPEMNTID,class_id,IGROUP,IROW,ICOLUMN) 
				 values('$equipmentID', '$class_id', $group, $row, $column);";
		}
        $flag = Tools::trans_sql($sql_insert);
        echo $flag ? 'ok':'failed';

	}
}

?>
