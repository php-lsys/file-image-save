<?php
/**
 * lsys database
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
return array(
	"mysqli"=>array(
		//PMYSQLi 配置
		"type"=>\LSYS\Database\MYSQLi::class,
		"charset"=>"UTF8",
		"table_prefix"=>"yaf_",
		"connection"=>array(
			//单数据库使用此配置
			'database' => 'ssss',
			'hostname' => '127.0.0.1',
			'username' => 'root',
			'password' => '110',
			'persistent' => FALSE,
			"variables"=>array(
			),
		),
	)
);