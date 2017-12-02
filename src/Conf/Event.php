<?php

namespace Conf;

use Core\Http\Request;
use Core\Http\Response;

class Event extends \Core\AbstractInterface\AbstractEvent
{

    /**
     * frameInitialize  [框架初始化接口]
     * @copyright Copyright (c)
     * @author Wongzx <842687571@qq.com>
     * @return mixed
     */
    function frameInitialize()
    {
        // TODO: Implement frameInitialize() method.
        date_default_timezone_set('Asia/Shanghai');
    }

    /**
     * frameInitialized  [框架初始化]
     * @copyright Copyright (c)
     * @author Wongzx <842687571@qq.com>
     * @return mixed
     */
    function frameInitialized()
    {
        // TODO: Implement frameInitialized() method.
    }

    /**
     *[beforeWorkerStart 未执行swoole_http_server start]
     * @author  Wongzx <[842687571@qq.com]>
     * @param \swooleServer $server
     * @copyright Copyright (c)
     * @return    [type]        [description]
     */
    function beforeWorkerStart(\swooleServer $server)
    {
        // TODO: Implement beforeWorkerStart() method.
    }

    /**
     *[onStart Server启动在主进程的主线程回调此函数]
     * 在此事件之前Swoole Server已进行了如下操作
     * 已创建了manager进程
     * 已创建了worker子进程
     * 已监听所有TCP/UDP端口
     * 已监听了定时器
     * 接下来要执行
     * 主Reactor开始接收事件，客户端可以connect到Server
     * onStart回调中，仅允许echo、打印Log、修改进程名称。不得执行其他操作。
     * onWorkerStart和onStart回调是在不同进程中并行执行的，不存在先后顺序。
     * 在onStart中创建的全局资源对象不能在worker进程中被使用，因为发生onStart调用时，
     * worker进程已经创建好了。新创建的对象在主进程内，worker进程无法访问到此内存区域。
     * 因此全局对象创建的代码需要放置在swoole_server_start之前。
     * @author  Wongzx <[842687571@qq.com]>
     * @copyright Copyright (c)
     * @return    [type]        [description]
     */
    function onStart(\swooleServer $server)
    {
        // TODO: Implement onStart() method.
    }


    function onShutdown(\swooleServer $server)
    {
        // TODO: Implement onShutdown() method.
    }

    function onWorkerError(\swooleServer $server, $worker_id, $worker_pid, $exit_code)
    {
        // TODO: Implement onWorkerError() method.
    }

    function onTask(\swooleServer $server, $taskId, $workerId, $callBackObj)
    {
        // TODO: Implement onTask() method.
    }

    function onFinish(\swooleServer $server, $taskId, $callBackObj)
    {
        // TODO: Implement onFinish() method.
    }

    function onWorkerStart(\swooleServer $server, $workerId)
    {
        // TODO: Implement onWorkerStart() method.
    }

    function onWorkerStop(\swooleServer $server, $workerId)
    {
        // TODO: Implement onWorkerStop() method.
    }

    function onDispatcher(Request $request, Response $response, $targetControllerClass, $targetAction)
    {
        // TODO: Implement onDispatcher() method.
    }

    function onRequest(Request $request, Response $response)
    {
        // TODO: Implement onRequest() method.
    }

    function onResponse(Request $request, Response $response)
    {
        // TODO: Implement onResponse() method.
    }
}