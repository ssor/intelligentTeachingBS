<?php


// || C('DB_TYPE')== 'mysql')
class Tools extends Action
{
	static public function executeSql($sql) {
		$state=true;
		if (!empty($sql)) {
			switch (C('DB_TYPE')) {
				case 'oracle':
					$sql="begin ".$sql." end;";	
					$M = new Model(); 
					if (!$M->execute($sql))
					{
						$state = false;
					}
					break;
				case 'pdo':
					$M = new Model();
					$M->startTrans();
					//$M->execute($sql);
					$sqlArray = explode(';',$sql);
					for($i=0;$i<count($sqlArray)-1;$i++)
					{
						//$M = new Model();
						
						if (!$M->execute($sqlArray[$i])) {
							$state=false;
							break;
						}
					}
					if(!$M->commit())
					{
						$state=false;
						$M->rollback();
					}
					break;
				case 'mysql':
					$M = new Model();
					$M->startTrans();
					//$M->execute($sql);
					$sqlArray = explode(';',$sql);
					for($i=0;$i<count($sqlArray)-1;$i++)
					{
						//$M = new Model();
						
						if (!$M->execute($sqlArray[$i])) {
							$state=false;
							break;
						}
					}
					if(!$M->commit())
					{
						$state=false;
						$M->rollback();
					}
					break;					
				default:
					# code...
					break;
			}	
		}
		
		return $state;
	}	
	
    static function json_is_array($json)
    {
        $is_array = json_decode($json);
        if(is_array($is_array)){
            return true;
        }   
        return false;
    }    	
	public function checkUTF8($str) {
		$cod = mb_check_encoding($str,"UTF-8");
		if("UTF-8" != $cod ||  empty($cod))
		{
			$str = mb_convert_encoding( $str,'utf-8','gb2312'); 
		}
		return $str;
	}
	static function request($name)
	{
		$value = $_GET[$name];
		if($value == null){
			$value = $_POST[$name];
		}
		return $value;
	}
    static function request_json($name){
        return json_decode(self::request($name), true);
    }	
    static function get_query_result($sql)
    {
        if(empty($sql))
        {
            return NULL;
        }
        else
        {
            $MSelect = new Model();
            return $MSelect->query($sql);
        }
    }
    static public function trans_sql($sql = '')
    {
        $state = true;
        $M = new Model();
        $sqlArray = explode(';',$sql);
        if(count($sqlArray) > 1)
        {
            $M->startTrans();
            for($i = 0; $i < count($sqlArray) - 1; $i++)
            {
                if (false === $M->execute($sqlArray[$i])) {
                    $state = false;
                    break;
                }
            }
            if($state)
            {
                $M->commit();
                return true;
            }
            else
            {
                $M->rollback();
                return false;
            }            
        }
        else
        {
            return $M->execute($sql);
        }
    }    
    static public function get_raw_post_data()
    {
		 $Input = file_get_contents("php://input"); 
		 return $Input;
    }
    static public function set_result_json($status = 'ok', $text = '')
    {
        return json_encode(array('status' => $status, 'text' => $text));
    }    
}
?>