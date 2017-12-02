<?php

namespace Core\Swoole;

use Conf\Config AS GlobalConf;

class Config
{
    const SERVER_TYPE_SERVER = 'SERVER_TYPE_SERVER';

    const SERVER_TYPE_WEB = 'SERVER_TYPE_WEB';

    const SERVER_TYPE_WEB_SOCKET = 'SERVER_TYPE_WEB_SOCKET';


    private $listenIp; //监听IP

    private $listenPort; //监听端口

    private $workerSetting; //线程设置

    private $workerNum; //线程数

    private $taskWorkerNum; //

    private $serverName;

    private $runMode;

    private $serverType;

    private $socketType;

    protected static $instance;

    static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->listenIp = GlobalConf::getInstance()->getConf("SERVER.LISTEN");
        $this->listenPort = GlobalConf::getInstance()->getConf("SERVER.PORT");
        $this->workerSetting = GlobalConf::getInstance()->getConf("SERVER.CONFIG");
        $this->workerNum = GlobalConf::getInstance()->getConf("SERVER.CONFIG.worker_num");
        $this->taskWorkerNum = GlobalConf::getInstance()->getConf("SERVER.CONFIG.task_worker_num");
        $this->serverName = GlobalConf::getInstance()->getConf("SERVER.SERVER_NAME");
        $this->runMode = GlobalConf::getInstance()->getConf("SERVER.RUN_MODE");
        $this->serverType = GlobalConf::getInstance()->getConf("SERVER.SERVER_TYPE");
        $this->socketType = GlobalConf::getInstance()->getConf("SERVER.SOCKET_TYPE");
    }


    /**
     * getListenIp  [description]
     * @copyright Copyright (c)
     * @author Wongzx <842687571@qq.com>
     */
    public function getListenIp()
    {
        return $this->listenIp;
    }

    /**
     * getlistenPort  [description]
     * @copyright Copyright (c)
     * @author Wongzx <842687571@qq.com>
     */
    public function getListenPort()
    {
        return $this->listenPort;
    }

    /**
     * getWorkerSetting  [description]
     * @copyright Copyright (c)
     * @author Wongzx <842687571@qq.com>
     */
    public function getWorkerSetting()
    {
        return $this->workerSetting;
    }

    /**
     * getWorkerNum  [description]
     * @copyright Copyright (c)
     * @author Wongzx <842687571@qq.com>
     */
    public function getWorkerNum()
    {
        return $this->workerNum;
    }

    /**
     * getTaskWorkerNum  [description]
     * @copyright Copyright (c)
     * @author Wongzx <842687571@qq.com>
     */
    public function getTaskWorkerNum()
    {
        return $this->taskWorkerNum;
    }

    /**
     * getServerName  [description]
     * @copyright Copyright (c)
     * @author Wongzx <842687571@qq.com>
     */
    public function getServerName()
    {
        return $this->serverName;
    }

    /**
     * getRunMode  [description]
     * @copyright Copyright (c)
     * @author Wongzx <842687571@qq.com>
     */
    public function getRunMode()
    {
        return $this->runMode;
    }

    /**
     * getServerType  [description]
     * @copyright Copyright (c)
     * @author Wongzx <842687571@qq.com>
     */
    public function getServerType()
    {
        return $this->serverType;
    }

    /**
     * getSocketType  [description]
     * @copyright Copyright (c)
     * @author Wongzx <842687571@qq.com>
     */
    public function getSocketType()
    {
        return $this->socketType;
    }


}