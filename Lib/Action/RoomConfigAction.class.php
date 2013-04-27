<?php
require_once('tools.php');
// [{"group":0,"row":4,"column":1},{"group":1,"row":2,"column":2},{"group":2,"row":4,"column":1}]
class RoomConfigAction extends Action
{

	//根据班级ID返回相应的教室配置。（暂时认为每个教室，无论什么课，其教室配置情况相同）

	// http://localhost:9011/index.php/RoomConfig/getRoomConfig/user/demo1
	// http://localhost:9011/index.php/RoomConfig/insertRoomConfig/user/demo1
	// Content-Type: application/x-www-form-urlencoded
	public function getRoomConfig()
	{
		$class_id = Tools::request('class');
		$sql_select = "select IGROUP ,IROW , ICOLUMN  from T_ROOM_CONFIG where class_id = '$class_id';";
		$list = Tools::get_query_result($sql_select);
		$count = count($list);
		for ($i=0; $i < $count; $i++) { 
			$row = $list[$i];
			$result[] = array('group' => $row['IGROUP'], 'row' => $row['IROW'], 'column' => $row['ICOLUMN']);			
		}
        echo json_encode($result);
	}
	public function insertRoomConfig()
	{
		$flag = true;
		$class_id = Tools::request('class');
		// echo $class_id; return;
		$data = Tools::request_json('data');
		// echo $data; return;
		$count = count($data);
		$sql_insert = "delete from T_ROOM_CONFIG where class_id = '$class_id';";
		for ($i=0; $i < $count; $i++) { 
			$item = $data[$i];
			$group = $item['group'];
			$row = $item['row'];
			$column = $item['column'];
			$sql_insert .= "insert into T_ROOM_CONFIG(class_id,IGROUP,IROW,ICOLUMN) 
				 values('$class_id', $group, $row, $column);";
		}
        $flag = Tools::trans_sql($sql_insert);
        echo $flag ? 'ok':'failed';

	}
}

?>
