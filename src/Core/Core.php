<?php

namespace Core;

use Conf\Event;
use Core\Swoole\Server;

class Core
{
    protected static $instance;

    private $preCall;

    function __construct($preCall)
    {
        $this->preCall = $preCall;
    }


    /**
     * run  [description]
     * @copyright Copyright (c)
     * @author Wongzx <842687571@qq.com>
     */
    public function run()
    {
        Server::getInstance()->startServer();
    }

    /**
     * getInstance  [得到的实例 类 Function]
     * @copyright Copyright (c)
     * @author Wongzx <842687571@qq.com>
     */
    static public function getInstance(callable $preCall = null)
    {
        if (!isset(self::$instance)) {
            self::$instance = new static($preCall);
        }
        return self::$instance;
    }


    /**
     * frameWork  [initialize frameWork 初始化APi模块框架]
     * @copyright Copyright (c)
     * @author Wongzx <842687571@qq.com>
     */
    public function frameWorkInitialize()
    {
        if (phpversion() < 5.6) {
            die("php version must >= 5.6");
        }
        $this->defineSysConst();     #系统建设路径
        $this->registerAutoLoader(); #注册自动加载机制
        Event::getInstance()->frameInitialize(); #框架初始化接口时间区
        Event::getInstance()->frameInitialized(); #框架初始化
        return $this;
    }


    /**
     * defineSysConst  [系统建设路径]
     * @copyright Copyright (c)
     * @author Wongzx <842687571@qq.com>
     */
    private function defineSysConst()
    {
        defined('ROOT') or define("ROOT", realpath(__DIR__ . '/../'));
    }

    /**
     * registerAutoLoader  [注册自动加载机制]
     * @copyright Copyright (c)
     * @author Wongzx <842687571@qq.com>
     */
    private function registerAutoLoader()
    {
        include_once 'AutoLoader.php';
        $loader = AutoLoader::getInstance();
        $loader->addNamespace('Core', 'Core');
        $loader->addNamespace('App', 'App');
        $loader->addNamespace('Conf', 'Conf');

    }
}