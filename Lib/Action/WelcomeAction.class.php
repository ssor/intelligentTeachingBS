<?php
require_once('tools.php');

class WelcomeAction extends Action {
	
	// 首页
	///*
	public function welcome()
	{
        $this->assign('version', C('VERSION'));
        $this->assign('company', C('COMPANY_SIGN'));
        //清空用户信息
        $_SESSION['system_id'] = '';//每次重新登录，重新设置系统
        $_SESSION['acount']= '';
        $_SESSION['nick_name'] = '';

		$this->display();
	}
	public function test_search()
	{
		$M = new Model();
        $sql_select_general_settings = 
        	"select setting_id, setting_value from T_GENERAL_SETTINGS;";
        $list_general_settings = $M->query($sql_select_general_settings);
        $settings = array();

        foreach ($list_general_settings as $index => $value) {
        	$settings[$value['setting_id']] = $value['setting_value'];
        }

        // php versino >= 5.5.0
        // foreach ($list_general_settings as list($key, $value)) {
        // 	$settings[$key] = $value;
        // }

        // var_dump($list_general_settings);
        var_dump($settings);
        // $founded = in_array("system_set", $list_general_settings);

	}

	// 检查标题是否可用
	public function checkLogin()
	{
        // $_SESSION['acount'] = 'teacher';
        // $_SESSION['nick_name'] = '教师';
        // $result['status'] = 'ok';
        // $result['url'] = '/Index/index';
        // echo json_encode($result);
        // return;

        $json_str = Tools::request("data");
        $json = json_decode($json_str,true);
        $username = $json['user_name'];
        $password = $json['password'];
        
		$_SESSION['acount']= $username;

        // || $username == 'admin'
        //教师或者管理员单独对待
        if($username == 'admin')
        {
            $pwdMd5 = md5($password);
            $sql = "SELECT ACCOUNT, REMARK FROM THINK_USER  where ACCOUNT = '$username'
                     AND PASSWORD = '$pwdMd5'";
            $list = Tools::get_query_result($sql);
            if (count($list) > 0) {
                $result['status'] = 'ok';
                $result['url'] = '/Index/admin_index';
                $foo_json = json_encode($result);                
                $_SESSION['nick_name'] = $list[0]['REMARK'];
            }
            else{
                $foo_json = Tools::set_result_json('failed', '用户名或者密码错误！');
            }
            echo $foo_json;return;
        }
		if (!empty($username) && !empty($password))
		{
			$pwdMd5 = md5($password);
			$sql = "SELECT STUDENTID, NAME FROM T_STUDENTINFO  where
					STUDENTID = '$username'
					 AND PASSWORD= '$pwdMd5'";
			$list = Tools::get_query_result($sql);
			if (count($list) <= 0){
                $foo_json = Tools::set_result_json('failed', '用户名或者密码错误！');
			}
            else{
                $check_status = $list[0]['status'];
                if($check_status == 'no'){
                    $foo_json = Tools::set_result_json('failed', '您尚未审核通过！');
                }
                else{
                    $result['status'] = 'ok';
                    $result['url'] = '/Index/index';
                    $foo_json = json_encode($result);
                    $_SESSION['nick_name'] = $list[0]['NAME'];
                }
            }
        }
        else
        {
            $foo_json = Tools::set_result_json('failed', '用户名或者密码错误！');
        }
        echo $foo_json; 
		return;	
	}
	
}
?>
