<?php
/**
 * 测试TestResque
 * @author shangheguang
 *
 */
class TestResqueCommand extends CConsoleCommand {
	
	public function run($args) {
	
		//  /usr/local/php/bin/php -f /www/site/dev.website.cn/protected/commands/index.php testResque
		
		$return 	= null;
		$yiiPath 	= Yii::getPathOfAlias('system');
		$appPath 	= Yii::getPathOfAlias('application');
		$resquePath = Yii::getPathOfAlias('application.extensions.phpresque');
		
		$server 		= '192.168.1.102';
		$port 			= 6379;
		$db 			= 2;
		$auth 			= 'password';
		$host 			= 'redis://user:'.$auth.'@'.$server.':'.$port;
		
		$prefix 		= '';
		
		//$includeFiles 	= array('/www/site/dev.website.cn/protected/commands/phpResqueWorker/ClassWorker.php');
		$includeFiles 	= array($appPath.'/commands/phpResqueWorker/ClassWorker.php');
		
		$interval 		= 2;
		$queue			= 'default';
		$count			= 3;
		$verbose		= 1;
		$script			= 'resque';
		
		if (is_array($includeFiles)) {
			$includeFiles = implode(',', $includeFiles);
		}
		
		$options = '';
		$command = 'nohup sh -c " PREFIX='.$prefix.' QUEUE='.$queue.' COUNT='.$count.' REDIS_BACKEND='.$host.' REDIS_BACKEND_DB='.$db.' REDIS_AUTH='.$auth.' INTERVAL='.$interval.' VERBOSE='.$verbose.' APP_INCLUDE='.$includeFiles.' YII_PATH='.$yiiPath.' APP_PATH='.$appPath.' ' . $options . ' php '.$resquePath.'/bin/'.$script.'" >> '.$appPath.'/runtime/yii_resque_log.log 2>&1 &';
		
		exec($command, $return);
		
	}
	
}
