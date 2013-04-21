<?php
require_once('tools.php');

class LoginAction extends Action
{
	// http://localhost:9011/index.php/Login/tryLogin/user/demo1/pwd/pwd1
	public function tryLogin()
	{
		$user_id = Tools::request('user');
		$pwd = Tools::request('pwd');
		$sql_select = "select ACCOUNT from THINK_USER where ACCOUNT = '$user_id' and PASSWORD = '$pwd';";
		$list = Tools::get_query_result($sql_select);
        echo (count($list) > 0) ?'ok':'failed';
	}
}

?>
