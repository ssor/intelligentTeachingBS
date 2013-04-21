<?php
return array(
	//'配置项'=>'配置值'
    // 'URL_MODEL'=>3, // 如果你的环境不支持PATHINFO 请设置为3
	//'DB_TYPE'=>'mysql',

	/***********************************************************
	// ms sqlserver config
	'DB_TYPE'=>'Sqlsrv',//OK
	// 连接本地
	//'DB_HOST'=>'192.168.1.102',//OK
	'DB_HOST'=>'(local)',//OK
	'DB_NAME'=>'IMS',
	'DB_USER'=>'sa',
	'DB_PWD'=>'078515',	
	//************************************************************/
	
	// 'DB_TYPE'               => 'mysql',     // 数据库类型
	// 'DB_HOST'				=> '127.0.0.1',//OK
	// 'DB_NAME'               => 'webgisdb',          // 数据库名
	// 'DB_USER'               => 'root',      // 用户名
	// 'DB_PWD'                => '078515',          // 密码
	// 'DB_PORT'               => '3306',          // 端口
	// 'DB_CHARSET'            => 'utf8',      // 数据库编码默认采用utf8
	
	// 'DB_DSN' => 'mysql://root:078515@localhost:3306/webgisdb',
	//***********************************************************
	// Sqlite config
	'DB_TYPE'=>'pdo',	
	'DB_DSN' => 'sqlite:'.dirname(__FILE__).'./test.db3', //相对于config目录的路径
	'DB_NAME'=>'test.db3',
	//************************************************************
	// 'TMPL_ACTION_SUCCESS'=>'Tpl/Public/success.html', // 定义错误跳转页面URL地址
	// 'DB_PREFIX'=>'think_',
    // 'APP_DEBUG' => 0,
    // 'HTML_FILE_SUFFIX' => '.html',
    //'APP_GROUP_LIST' => 'Admin,Home',
    //'DEFAULT_GROUP' => 'Home',
    
    'DEFAULT_MODULE' => 'Welcome',
    'DEFAULT_ACTION' => 'welcome',
	
	// 'INTERFACES_FILE_PATH' => './Public/Interfaces/',
	// 'TEMPLATE_FILE_PATH' => './Public/Template/',
	// 'SEND_ORDER_MAIL' => '1',
	
	// 'LOCAL_IP' => '192.168.1.101',
	// 'LOCAL_IP' => 'localhost',
	// 'LOCAL_IP' => '118.202.124.38',
	// 'LOCAL_PORT' => '9001',
	//'__PUBLIC__' => './Public/',
	//'__IMAGES__' => './Public/Images/',
	'PUBLIC_NETWORK_ADDRESS' => 'http://111.67.197.251:10000/index.php',
	'PUBLIC_NETWORK_IP' => '111.67.197.251',
	'PUBLIC_NETWORK_PORT' => '10000',
	'COMPANY_SIGN' => '北京动思科技发展有限公司',
);
?>
