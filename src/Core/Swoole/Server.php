<?php

namespace Core\Swoole;

use Conf\Event;
use Core\Component\SysConst;
use Core\Swoole\Pipe\Dispatcher AS PipeDispatcher;
use Core\AbstractInterface\AbstractAsyncTask;
use Core\Component\SuperClosure;
use Core\Http\Request;
use Core\Http\Response;
use Core\Http\Dispatcher;
use Core\Component\Di;
use Core\AbstractInterface\HttpExceptionHandlerInterface;
use Core\Component\Error\Trigger;

class Server
{
    protected static $instance;

    protected $swooleServer;

    /*
     * 获取一个服务实例
     * @return Server
     */
    static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function __construct()
    {
        /**
         * 判断实例化  swoole服务 HttpServer &  Server &  WebSocket
         */
        $conf = Config::getInstance();
        if ($conf->getServerType() == Config::SERVER_TYPE_SERVER) {
            $this->swooleServer = new \swooleServer($conf->getListenIp(), $conf->getListenPort(), $conf->getRunMode(), $conf->getSocketType());
        } else if ($conf->getServerType() == Config::SERVER_TYPE_WEB) {
            $this->swooleServer = new \swooleHttpServer($conf->getListenIp(), $conf->getListenPort(), $conf->getRunMode());
        } else if ($conf->getServerType() == Config::SERVER_TYPE_WEB_SOCKET) {
            $this->swooleServer = new \swooleWebSocket($conf->getListenIp(), $conf->getListenPort(), $conf->getRunMode());
        } else {
            die('server type error');
        }
    }

    /**
     *[startServer 创建并启动一个swoole http server]
     * @author  Wongzx <[842687571@qq.com]>
     * @copyright Copyright (c)
     * @return    [type]        [description]
     */
    public function startServer()
    {
        $conf = Config::getInstance();
        $this->getSwooleServer()->set($conf->getWorkerSetting());
        $this->beforeWorkerStart();
        $this->pipeMessage();
        $this->serverStartEvent();
        $this->serverShutdownEvent();
        $this->workerErrorEvent();
        $this->onTaskEvent();
        $this->onFinish();
        $this->workerStartEvent();
        $this->workerStopEvent();
        if($conf->getServerType() != Config::SERVER_TYPE_SERVER){
            $this->listenRequest();
        }
        $this->isStart = 1;
        $this->getSwooleServer()->start();
    }

    /*
         * 监听http请求
         */
    private function listenRequest()
    {
        $this->getSwooleServer()->on("request",
            function (\swooleHttpServer $request, \swooleHttpResponse $response) {
                $request2 = Request::getInstance($request);
                $response2 = Response::getInstance($response);
                try {
                    Event::getInstance()->onRequest($request2, $response2);
                    Dispatcher::getInstance()->dispatch();
                    Event::getInstance()->onResponse($request2, $response2);
                } catch (\Exception $exception) {
                    $handler = Di::getInstance()->get(SysConst::HTTP_EXCEPTION_HANDLER);
                    if ($handler instanceof HttpExceptionHandlerInterface) {
                        $handler->handler($exception, $request2, $response2);
                    } else {
                        Trigger::exception($exception);
                    }
                }
                $response2->end(true);
            });
    }

    /**
     *[getSwooleServer void]
     *  用于获取 getSwooleServer 实例
     * server启动后，在每个进程中获得的，均为当前自身worker的server（可以理解为进程克隆后独立运行）
     * @author  Wongzx <[842687571@qq.com]>
     * @copyright Copyright (c)
     * @return    [type]        [description]
     */
    public function getSwooleServer()
    {
        return $this->swooleServer;
    }

    /**
     *[beforeWorkerStart void]
     * @author  Wongzx <[842687571@qq.com]>
     * @copyright Copyright (c)
     * @return    [type]        [description]
     */
    private function beforeWorkerStart()
    {
        Event::getInstance()->beforeWorkerStart($this->getSwooleServer());
    }

    /**
     *[serverStartEvent void]
     * @author  Wongzx <[842687571@qq.com]>
     * @copyright Copyright (c)
     * @return    [type]        [description]
     */
    private function serverStartEvent()
    {
        $this->getSwooleServer()->on('start', function (\swooleServer $server) {
            Event::getInstance()->onStart($server);
        });

    }

    private function serverShutdownEvent()
    {
        $this->getSwooleServer()->on("shutdown", function (\swooleServer $server) {
            Event::getInstance()->onShutdown($server);
        });
    }

    private function pipeMessage()
    {
        $this->getSwooleServer()->on('pipeMessage', function (\swooleServer $server, $fromId, $data) {
            PipeDispatcher::getInstance()->dispatch($server, $fromId, $data);

        });
    }


    /*
     * 当worker/task_worker进程发生异常后会在Manager进程内回调此函数。
        $worker_id是异常进程的编号
        $worker_pid是异常进程的ID
        $exit_code退出的状态码，范围是 1 ～255
        此函数主要用于报警和监控，一旦发现Worker进程异常退出，那么很有可能是遇到了致命错误或者进程CoreDump。
        通过记录日志或者发送报警的信息来提示开发者进行相应的处理。
     */
    private function workerErrorEvent()
    {
        $this->getSwooleServer()->on("workererror", function (\swooleServer $server, $worker_id, $worker_pid, $exit_code) {
            Event::getInstance()->onWorkerError($server, $worker_id, $worker_pid, $exit_code);
        });
    }

    private function onTaskEvent()
    {
        $num = \Core\Swoole\Config::getInstance()->getTaskWorkerNum();
        if (!empty($num)) {
            $this->getSwooleServer()->on("task", function (\swooleHttpServer $server, $taskId, $workerId, $taskObj) {
                try {
                    if (is_string($taskObj) && class_exists($taskObj)) {
                        $taskObj = new $taskObj();
                    }
                    Event::getInstance()->onTask($server, $taskId, $workerId, $taskObj);
                    if ($taskObj instanceof AbstractAsyncTask) {
                        return $taskObj->handler($server, $taskId, $workerId);
                    } else if ($taskObj instanceof SuperClosure) {
                        return $taskObj($server, $taskId);
                    }
                    return null;
                } catch (\Exception $exception) {
                    return null;
                }
            });
        }
    }

    private function onFinish()
    {
        $num = Config::getInstance()->getTaskWorkerNum();
        if (!empty($num)) {
            $this->getSwooleServer()->on("finish",
                function (\swooleServer $server, $taskId, $taskObj) {
                    try {
                        Event::getInstance()->onFinish($server, $taskId, $taskObj);
                        //仅仅接受AbstractTask回调处理
                        if ($taskObj instanceof AbstractAsyncTask) {
                            $taskObj->finishCallBack($server, $taskId, $taskObj->getDataForFinishCallBack());
                        }
                    } catch (\Exception $exception) {

                    }
                }
            );
        }
    }

    private function workerStartEvent()
    {
        $this->getSwooleServer()->on("workerStart", function (\swooleServer $server, $workerId) {
            Event::getInstance()->onWorkerStart($server, $workerId);
        });
    }

    private function workerStopEvent()
    {
        $this->getSwooleServer()->on("workerStop", function (\swooleServer $server, $workerId) {
            Event::getInstance()->onWorkerStop($server, $workerId);
        });
    }


}