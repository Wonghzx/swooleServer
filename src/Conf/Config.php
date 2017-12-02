<?php

namespace Conf;

use Core\Component\Sql\SplArray;

class Config
{
    private static $instance;

    protected $conf;


    public function __construct()
    {
        $conf = $this->sysConf() + $this->userConf();
        $this->conf = new SplArray($conf);
    }

    static public function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }


    /**
     * getConf  [description]
     * @param $keyPath
     * @copyright Copyright (c)
     * @author Wongzx <842687571@qq.com>
     * @return null
     */
    function getConf($keyPath)
    {
        return $this->conf->get($keyPath);
    }

    /**
     * setConf  [在server启动以后，无法动态的去添加，修改配置信息（进程数据独立）]
     * @param $keyPath
     * @param $data
     * @copyright Copyright (c)
     * @author Wongzx <842687571@qq.com>
     */
    function setConf($keyPath, $data)
    {
        $this->conf->set($keyPath, $data);
    }


    /**
     * sysConf  [description]
     * @copyright Copyright (c)
     * @author Wongzx <842687571@qq.com>
     * @return array
     */
    public function sysConf()
    {
        return [
            "SERVER" => [
                "LISTEN" => "0.0.0.0",
                "SERVER_NAME" => "",
                "PORT" => 9501,
                "RUN_MODE" => 3,//不建议更改此项
                "SERVER_TYPE" => 'SERVER_TYPE_WEB',//
                'SOCKET_TYPE' => 1,//当SERVER_TYPE为SERVER_TYPE_SERVER模式时有效
                "CONFIG" => [
                    'task_worker_num' => 8, //异步任务进程
                    "task_max_request" => 10,
                    'max_request' => 5000,//强烈建议设置此配置项
                    'worker_num' => 8,
                ],
            ],
            "DEBUG" => [
                "LOG" => true,
                "DISPLAY_ERROR" => true,
                "ENABLE" => true,
            ],
            "CONTROLLER_POOL" => true//web或web socket模式有效
        ];
    }

    public function userConf()
    {
        return [];
    }

}