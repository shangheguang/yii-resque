<?php
/**
 * PhpResqueCommand
 * @author: shangheguang@yeah.net
 * @date:	2015-12-01
 */
class PhpResqueCommand extends CConsoleCommand
{
	
    public $defaultAction = 'index';

    public function actionIndex()
    {
        echo <<<EOD
This is the command for the php-resque component. Usage:

Available commands are:

    start --queue=[queue_name | *] --classworker=[classworker] --interval=[int] --verbose=[0|1] --count=[int]
    stop --quit=[0|1]
EOD;
    }

    protected function runCommand($queue, $classworker, $interval, $verbose, $count, $script='resque')
    {
        $return 	= null;
        $yiiPath 	= Yii::getPathOfAlias('system');
        $appPath 	= Yii::getPathOfAlias('application');
        $resquePath = Yii::getPathOfAlias('application.extensions.phpresque');
        
        if (!isset(Yii::app()->resque)) {
            die('resque component cannot be found on your console.php configuration!');
        }
        if(empty($classworker)){
        	die('classworker is empty!');
        }
        
        $server 		= Yii::app()->resque->server ? : 'localhost';
        $port 			= Yii::app()->resque->port ? : 6379;
        $db 			= Yii::app()->resque->database ? : 0;
        $auth 			= Yii::app()->resque->password ? : '';
        $host 			= 'redis://user:'.$auth.'@'.$server.':'.$port;   
        $prefix 		= '';
        
        $includeFiles 	= array($appPath.'/commands/phpResqueWorker/'.$classworker.'.php');
        
        if (is_array($includeFiles)) {
        	$includeFiles = implode(',', $includeFiles);
        }
        
        $options = '';
        $command = 'nohup sh -c " PREFIX='.$prefix.' QUEUE='.$queue.' COUNT='.$count.' REDIS_BACKEND='.$host.' REDIS_BACKEND_DB='.$db.' REDIS_AUTH='.$auth.' INTERVAL='.$interval.' VERBOSE='.$verbose.' APP_INCLUDE='.$includeFiles.' YII_PATH='.$yiiPath.' APP_PATH='.$appPath.' ' . $options . ' php '.$resquePath.'/bin/'.$script.'" >> '.$appPath.'/runtime/yii_resque_log.log 2>&1 &';
        
        exec($command, $return);

        return $return;
    }

    /**
     * 运行 resque 进程
     * classworker 				    一定大小写保持一直，否则Worker执行错误(返回:3)
     * @param string $queue		    队列名称
     * @param string $classworker: 	worker任务类名
     * @param number $interval		Sleeping多久，默认(Sleeping for 5)
     * @param number $verbose       resque中worker的运行日志开关 0,1
     * @param number $count			开启进程数，默认(1)
     * @linux_run_link:    /usr/local/php/bin/php -f /www/site/dev.website.cn/protected/commands/index.php phpresque start --queue=default --classworker=UserWorker
     */
    public function actionStart($queue = '*', $classworker = null, $interval = 0, $verbose = 0, $count = 2)
    {
        $this->runCommand($queue, $classworker, $interval, $verbose, $count, 'resque');
    }

    /**
     * kill   resque进程
     * @param string $quit
     * @linux_run_link:  /usr/local/php/bin/php -f /www/site/dev.website.cn/protected/commands/index.php phpresque stop
     */
    public function actionStop($quit = null)
    {
        $quit_string = $quit ? '-s QUIT': '-9';

        exec("ps uxe | grep 'resque' | grep -v grep | awk {'print $2'} | xargs kill $quit_string");
    }
    
}
