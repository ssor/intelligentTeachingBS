<?php
require_once('tools.php');

class UserManagementAction extends Action
{
    private $sexes = '[{id:0,text:"男"},{id:1, text:"女"}]';
    /**
    +----------------------------------------------------------
    * 页面输出
    +----------------------------------------------------------
    */
    public function user_info_index()
    {
        $this->assign('sexes', $this->sexes);
        $this->assign('sex_value', '0');
        $user_id = $_SESSION['acount'];
        $this->assign('user_id', $user_id);
        $sql = "select T1.STUDENTID,T1.NAME,T1.SEX,T1.AGE,T1.EMAIL 
                 from T_STUDENTINFO T1 where T1.STUDENTID = '$user_id';";
        $list = Tools::get_query_result($sql);
        if(count($list) > 0){
            $row = $list[0];
            $this->assign('name', $row['NAME']);
            $this->assign('sex_value', $row['SEX']);
            $this->assign('age', $row['AGE']);
            $this->assign('email', $row['EMAIL']);
        }
        $this->display();
    }
	public function register_user_index()
	{
        $this->assign('version', C('VERSION'));
        $this->assign('company', C('COMPANY_SIGN'));
		$this->display();
	}
	public function register_user()
	{
        $json = Tools::request_json("data");
        
		$result['status'] = 'ok';
        $user_name = $json['user_name'];
        $user_id = $json['user_id'];
        $pwd = $json['new_psw1'];
        $class_id = $json['class_id'];

        if($this->check_new_user_if_already_exits($user_id)){
            $foo_json = Tools::set_result_json('failed', '该学号已经注册！');
        }
        else{
            $r = $this->add_user_to_database($user_id, $user_name, $pwd, $class_id);
            if($r){
                $foo_json = Tools::set_result_json('ok', '注册成功');
            }
            else{
                $foo_json = Tools::set_result_json('failed', '注册时出现异常！');
            }
        }
        echo $foo_json;        
	}
    public function sexes()
    {
        // echo $this->sexes;  
        echo '[{id:0,text:"男"},{id:1, text:"女"}]';      
    }
	public function change_pwd_index()
	{
		$this->display();
	}
	public function updatepwd()
	{
		$crtUser = $_SESSION['acount'];
        $json = Tools::request_json("data");
        $oldpwd = $json['old_psw'];
        // echo $oldpwd; return;
        $new_psw = $json['new_psw1'];

	    if($this->check_crt_pwd_right($crtUser, $oldpwd)){
            $r = $this->update_pwd($crtUser, $new_psw);
            if($r){
                $foo_json = Tools::set_result_json('ok', '更改密码成功！');
            }
            else{
                $foo_json = Tools::set_result_json('failed', '更改密码失败！');
            }
        }	
        else{
            $foo_json = Tools::set_result_json('failed', '输入的当前密码错误！');
        }
        echo $foo_json;
	}

	public function UserIndex()
	{
        $sql = "select class_id, class_name from T_CLASSES order by create_time asc;";
        $result = Tools::get_query_result($sql);
        $this->assign('classes', json_encode($result));
        $this->assign('sexes', $this->sexes);
		$this->display();		
	}
    //设置注册是否通过
	public function check_user()
	{
        $json = Tools::request_json("data");
        $flag = $this->set_register_user_checked($json);
        echo $flag ? 'ok':'failed';
	}

	public function update_user_info()
	{
        $json = Tools::request("data");
        $flag = $this->update_user_info_to_database($json);
        // echo $flag;return;
        echo $flag ? 'ok':'failed';
	}
    //学生自行更新个人的部分信息
    public function update_basic_info()
    {
        $user_id = Tools::request('user_id');
        $users_json = Tools::request("data");

        $sql_update = "";
        if(Tools::json_is_array($users_json)){
            $users_array = json_decode($users_json, true);
            foreach ($users_array as $key => $user) {
                $nick_name = $user['user_name'];
                $age = $user['age'];
                $email = $user['email'];
                $sex = $user['sex'];
                $sql_update .= "update T_STUDENTINFO set NAME = '$nick_name',SEX = '$sex', AGE = $age, EMAIL = '$email' 
                     where STUDENTID = '$user_id';";
            }        
        }
        else{
            $user = json_decode($users_json, true);
            $nick_name = $user['user_name'];
            $sex = $user['sex'];
            $age = $user['age'];
            $email = $user['email'];
            $sql_update .= "update T_STUDENTINFO set NAME = '$nick_name',SEX = '$sex', AGE = $age,EMAIL = '$email' 
                 where STUDENTID = '$user_id';";
        }
        // echo $sql_update; return;
        $flag = Tools::trans_sql($sql_update);
        echo $flag ? 'ok':'failed';
    }
	public function reset_pwd()
    {
        $json = Tools::request("data");
        $flag = $this->reset_pwd_default_to_database($json);
        echo $flag ? 'ok':'failed';
	}
    public function update_epc_bind()
    {
        $flag = true;
        $class_id = Tools::request('class');
        $data = Tools::request_json('data');
        if(count($data) > 0){
            for($i=0; $i < count($data); $i++){
                $item = $data[$i];
                $epc = $item['epc'];
                $id = $item['id_num'];
                $sql .= "replace into T_STUDENTINFO_LINK_EPC(DEVICEID, STUDENTID) values('$epc', '$id');";
            }
            // echo $sql;return;
            $flag = Tools::executeSql($sql);
        }
        echo $flag ? 'ok':'failed';
    }
	// public function user_list_unchecked()
	// {
	// 	echo $this->user_list_with_para('no');
	// }
	public function user_list()
	{
		echo $this->user_list_with_para('yes');
	}

    //为客户端提供需要的学生信息
    public function user_list_for_client()
    {
        $class_id = Tools::request('class');
        $sql = "select T1.STUDENTID,T1.NAME,T1.SEX,T1.AGE,T1.EMAIL,T2.DEVICEID,T4.class_name 
                 from T_STUDENTINFO T1,T_CLASS_LINK_USER T3,T_CLASSES T4
                 left join T_STUDENTINFO_LINK_EPC T2 
                on T1.STUDENTID = T2.STUDENTID where T1.STUDENTID = T3.user_id and T3.class_id = T4.class_id and T4.class_id = '$class_id';";
        $list = Tools::get_query_result($sql);
        foreach ($list as $key => $item) {
            $temp = $item['DEVICEID'];
            $result[] = array('id_num' => $item['STUDENTID']
                                , 'epc' => $temp
                                , 'name' => $item['NAME']
                                , 'email' => $item['EMAIL']
                                , 'sex' => (0 == $item['SEX']) ? '男':'女'
                                , 'age' => (empty($item['AGE'])) ? 0:$item['AGE']
                                , 'bj' => $item['class_name']);
        }
        $foo_json = json_encode($result);
        echo $foo_json;           
    }
    public function remove_user()
    {
        $json = Tools::request("data");
        $flag = $this->remove_user_from_database($json);
        echo $flag ? 'ok':'failed';
    }

    /**
    +----------------------------------------------------------
    * 数据计算输出
    +----------------------------------------------------------
    */
    function remove_user_from_database($users_json)
    {
        $user_list[] = array();
        if(Tools::json_is_array($users_json)){
            $users_array = json_decode($users_json,true);
            foreach ($users_array as $key => $user) {
                $user_list[] = $user['account'];
            }
            // $user_list = array_map(function($user){
            //     return $user['account'];
            // }, $users_array);
        }
        else{
            $user = json_decode($users_json,true);
            $user_list[] = $user['account'];
        }
        $sql_delete = $this->get_sql_remove_user($user_list);
        return Tools::trans_sql($sql_delete);
    }
    function reset_pwd_default_to_database($users_json)
    {
        $users = "''";
        if(Tools::json_is_array($users_json)){
            $users_array = json_decode($users_json, true);
            foreach ($users_array as $key => $user) {
                $account = $user['account'];
                $users .= ",'".$account."'";
            }
            // array_walk($users_array, function($user, $i) use(&$users){
            //     $account = $user['account'];
            //     $users .= ",'".$account."'";
            // });
        }
        else{
            $user = json_decode($users_json, true);;
            $account = $user['account'];
            $users .= ",'".$account."'";            
        }

        $default_secret = Tools::get_deault_secret();
        $sql_update = "update THINK_USER set PASSWORD = '$default_secret' where ACCOUNT in($users);";
        return Tools::trans_sql($sql_update);
    }

    //更新学生基本信息和所在班级，不包括学生卡信息
    function update_user_info_to_database($users_json)
    {
        $sql_update = "";
        if(Tools::json_is_array($users_json)){
            $users_array = json_decode($users_json, true);
            foreach ($users_array as $key => $user) {
                $account = $user['studentid'];
                $nick_name = $user['name'];
                $class_id = $user['class_id'];
                $sql_update .= "update T_STUDENTINFO set NAME = '$nick_name',SEX = '$sex', AGE = $age, EMAIL = '$email'  where STUDENTID = '$account';";
                $sql_update .= "update T_CLASS_LINK_USER set class_id = '$class_id' where user_id = '$account';";
            }        
        }
        else{
            $user = json_decode($users_json, true);
            $account = $user['studentid'];
            $nick_name = $user['name'];
            $class_id = $user['class_id'];
            $sex = $user['sex'];
            $age = $user['age'];
            $email = $user['email'];
            $sql_update .= "update T_STUDENTINFO set NAME = '$nick_name',SEX = '$sex', AGE = $age,EMAIL = '$email'  where STUDENTID = '$account';";
            $sql_update .= "update T_CLASS_LINK_USER set class_id = '$class_id' where user_id = '$account';";
        }
        // return $sql_update;
        return Tools::trans_sql($sql_update);
    }

    function set_register_user_checked($users_array)
    {
        $users = "''";
        foreach ($users_array as $key => $user) {
            $account = $user['account'];
            $users .= ",'".$account."'";
        }
        // array_walk($users_array, function($user, $i) use(&$users){
        //     $account = $user['account'];
        //     $users .= ",'".$account."'";
        // });
        $sql_update = "update THINK_USER set status = 'yes' where ACCOUNT in($users);";
        return Tools::trans_sql($sql_update);
    }

    function update_pwd($user_name, $new_psw)
    {
        $newpwdMd5 = md5($new_psw);
        $sqlUpdate = "UPDATE THINK_USER SET PASSWORD = '$newpwdMd5' where ACCOUNT = '$user_name';";
        return Tools::trans_sql($sqlUpdate);
    }
    function check_crt_pwd_right($user_name, $oldpwd)
    {
        $oldpwdMd5 = md5($oldpwd);
        $sql = "SELECT USERS.ACCOUNT FROM THINK_USER USERS WHERE  USERS.ACCOUNT = '$user_name' and PASSWORD = '$oldpwdMd5';";
        $list = Tools::get_query_result($sql);
        return (count($list) <= 0) ? false:true;
    }
    function add_user_to_database($user_id, $user_name, $pwd, $class_id)
    {
        $pwdMd5 = md5($pwd);
        $sql_insert = "insert into T_STUDENTINFO(STUDENTID, PASSWORD, NAME) values('$user_id', '$pwdMd5', '$user_name');";
        $sql_insert .= "insert into T_CLASS_LINK_USER(class_id, user_id) values('$class_id','$user_id');";
        return Tools::trans_sql($sql_insert);
    }
    public function check_new_user_if_already_exits($user_name)
    {
        if (!empty($user_name)) {
            $sql = "SELECT STUDENTID FROM T_STUDENTINFO where STUDENTID = '$user_name';";
            $list = Tools::get_query_result($sql);
            //该用户名尚未注册
            if(count($list) > 0 ){
                return true;
            }
            else{
                return false;
            }
        }
        else{
            return true;
        }
    }
	// $check = yes | no
	public function user_list_with_para($check)
	{
		$sql = "select T1.STUDENTID,T1.NAME,T1.SEX,T1.AGE,T1.EMAIL,T2.DEVICEID,T4.class_id 
                 from T_STUDENTINFO T1,T_CLASS_LINK_USER T3,T_CLASSES T4
                 left join T_STUDENTINFO_LINK_EPC T2 
                on T1.STUDENTID = T2.STUDENTID where T1.STUDENTID = T3.user_id and T3.class_id = T4.class_id;";
		$list = Tools::get_query_result($sql);
        $result = array();
        foreach ($list as $key => $item) {
            $temp = (empty($item['DEVICEID'])) ? '未绑定':'已绑定';
            $result[] = array('studentid' => $item['STUDENTID']
                                , 'deviceid' => $temp
                                , 'name' => $item['NAME']
                                , 'email' => $item['EMAIL']
                                , 'sex' => $item['SEX']
                                , 'age' => (empty($item['AGE'])) ? 0:$item['AGE']
                                , 'class_id' => $item['class_id']);
        }
        $foo_json = json_encode($result);
        return $foo_json;	
	}
	public function get_sql_remove_user($user_list)
	{
        $users = "''";
        foreach ($user_list as $key => $value) {
            $users .= ",'".$value."'";
        }
        $sql_delete = "delete from T_STUDENTINFO
                    where STUDENTID in($users);";
        return $sql_delete;        
	}


}

?>
