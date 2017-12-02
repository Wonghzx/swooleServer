<?php


/**
 * 创建一个异步服务器程序，支持TCP、UDP、UnixSocket 3种协议，支持IPv4和IPv6，
 * 支持SSL/TLS单向双向证书的隧道加密。使用者无需关注底层实现细节，仅需要设置网络事件的回调函数即可
 * Class SwooleServer
 */
class SwooleServer extends \Swoole\Server
{

}

/**
 *增加了内置Http服务器的支持，通过几行代码即可写出一个异步非阻塞多进程的Http服务器。
 * Class swooleHttpServer
 */
class swooleHttpServer extends Swoole\Http\Server
{

}

class swooleWebSocket extends Swoole\WebSocket\Server
{

}

class swooleHttpResponse extends Swoole\Http\Response
{
}

class swooleHttpRequest extends Swoole\Http\Request
{
}