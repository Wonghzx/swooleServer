<?php

namespace swoole\Http;


/**
 * 内置 Web 服务器
 * Class Server
 * @package swoole\Http
 */
class Server extends \swoole\Server
{

    /**
     * setGlobal  [启用数据合并，HTTP请求数据到PHP的GET/POST/COOKIE全局数组]
     * @param $flag
     * @param int $request_flag
     * @copyright Copyright (c)
     * @author Wongzx <842687571@qq.com>
     */
    function setGlobal($flag, $request_flag = 0)
    {
    }
}