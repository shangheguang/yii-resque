# Yii Resque

Yii resque is a component for Yii to queue your background jobs, this component based on [php-resque](https://github.com/chrisboulton/php-resque) and [php-resque-scheduler](https://github.com/chrisboulton/php-resque-scheduler) with some enhancement for support phpredis, I'm also added log handler using [Monolog](https://github.com/Seldaek/monolog), already tested with [ResqueBoard](https://github.com/kamisama/ResqueBoard).

## Requirement

- php pcntl extension.
- [Redis.io](http://redis.io)
- [phpredis](https://github.com/nicolasff/phpredis) extension for better performance, otherwise it'll automatically use [Credis](https://github.com/colinmollenhour/credis) as fallback.
- Yii Framework >1.1.x

## Configuration

- Copy files to each folder
- Add to your ```config/main.php``` and your ```config/console.php```

```php
...
'components'=>array(
    ...
    'resque'=>array(
        'class' => 'application.components.yii-resque.RResque',
        'server' => 'localhost',     // Redis server address
        'port' => '6379',            // Redis server port
        'database' => 0,             // Redis database number
        'password' => '',            // Redis password auth, set to '' or null when no auth needed
    ),
    ...
)
...
```

- You may want to add these additional lines to your ```console.php``` for fixing auto loading and enabling the usage of models and helpers inside your workers :

```php
...
'import' => array(
    'application.models.*',
),
```

For performance reason you can just load required class by adding ```Yii::import()``` on your ```setUp``` function inside worker class, e.g :

```php
public function setUp()
{
    Yii::import('application.models.*');
}
```

## How to

### Create job and Workers

You can put this line where ever you want to add jobs to queue

```php
Yii::app()->resque->createJob('queue_name', 'Worker_ClassWorker', $args = array(), $track = true);
```

Put your workers inside Worker folder and name the class with ```Worker_``` as prefix, e.g you want to create worker with name SendEmail then you can create file inside Worker folder and name it SendEmail.php, class inside this file must be ```Worker_SendEmail```

### Delete Job

This method could delete or remove job based on queue, worker class, and/or job id :

```php
// This will remove job with key 'b6487da4b6d162f958bb06b405df6963' inside 'queue_name' queue and worker 'Worker_ClassWorker'
Yii::app()->resque->deleteJob('queue_name', 'Worker_ClassWorker', 'b6487da4b6d162f958bb06b405df6963');

// This will remove all jobs inside worker 'Worker_ClassWorker' and 'queue_name' queue
Yii::app()->resque->deleteJob('queue_name', 'Worker_ClassWorker');

// This will remove all jobs inside 'queue_name' queue
Yii::app()->resque->deleteJob('queue_name');
```

This method will return ```boolean```.

### Create Delayed Job

You can run job at specific time

```php
$time = 1332067214;
Yii::app()->resque->enqueueJobAt($time, 'queue_name', 'Worker_ClassWorker', $args = array(), $track = true);
```

or run job after n second 

```php
$in = 3600;
$args = array('id' => $user->id);
Yii::app()->resque->enqueueIn($in, 'email', 'Worker_ClassWorker', $args, $track = true);
```

### Check Job Status

You can get job status by this code :

```php
// This will return int : 1, 2, 3, 4, or 63
$int = Yii::app()->resque->status($queue_token);

// This will return string : waiting, running, failed, completed, or scheduled
$string = Yii::app()->resque->statusToString($int);
```

### Create Recurring Job

This is some trick that sometime useful if you want to do some recurring job like sending weekly newsletter, I just made some modification in my worker ```tearDown``` event

```php
public function tearDown()
{
    $interval = 3600; # This job will repeat every hour

    # Add next job queue based on interval
    Yii::app()->resque->enqueueJobIn($interval, 'queue_name', 'Worker_Newsletter', $args = array());
}
```

So everytime job has done completely the worker will send queue for same job.

### Get Total Scheduled Jobs

This will return total of scheduled jobs in queue (EXCLUDE all active job)

```php
Yii::app()->resque->getDelayedJobsCount();
```

### Get Current Queues

This will return all job in queue (EXCLUDE all active job)

```php
Yii::app()->resque->getQueues();
```

### Start and Stop workers

Run this command from your console/terminal :

Start queue

```bash
yiic rresque start
```

or 

```bash
yiic rresque start --queue=queue_name --interval=5 --verbose=0
```

Start delayed or scheduled queue

```bash
yiic rresque startrecurring
```

Stop queue

```bash
yiic rresque stop
```

Stop queue with QUIT signal

```bash
yiic rresque stop --quit=true
```

## Start Worker Options

This is available options for starting worker using `yiic` command :

* Set queue name

```bash
--queue=[queue_name]
```
This option default to `*` means all queue.

* Set interval time

```bash
--interval=[time in second]
```
Set your interval time for checking new job.

* Run in verbose mode

```bash
--verbose=[1 or 0]
```
Set to `1` if you want to see more information in log file.

* Number of worker

```bash
--count=[integer]
```

########################################################################################################################################################################

phpResque开启队列任务文件（核心文件）
yii_resque/commands/PhpResqueCommand.php

phpResque的worker任务文件夹
yii_resque/commands/phpResqueWorker

开启任务：
/**
 * 运行 resque 进程
 * classworker 				    一定大小写保持一直，否则Worker执行错误(返回:3)
 * @param string $queue		    队列名称
 * @param string $classworker: 	worker任务类名
 * @param number $interval		Sleeping多久，默认(Sleeping for 5)
 * @param number $verbose       resque中worker的运行日志开关 0,1
 * @param number $count			开启进程数，默认(1)
 * @linux_run_link:    			/usr/local/php/bin/php -f /www/site/dev.website.cn/protected/commands/index.php phpresque start --queue=default --classworker=UserWorker
 */
 
结束进程：
 /**
 * kill   resque进程
 * @param string $quit
 * @linux_run_link:  			/usr/local/php/bin/php -f /www/site/dev.website.cn/protected/commands/index.php phpresque stop
 */

有问题可以发送到此邮箱：shangheguang#yeah.net
把#替换为@

########################################################################################################################################################################
