<?php
// change the following paths if necessary
header("content-type:text/html;charset=utf-8");
$command_yii = dirname(__FILE__) . '/../../../../lib/yii/yii-1.1.13.e9e4a0/framework/yii.php';

defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);
$config = dirname(__FILE__) . '/../config/console.php';

require_once($command_yii);
Yii::createConsoleApplication($config)->run();