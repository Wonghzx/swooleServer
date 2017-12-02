<?php
/**
 * [CommandList.php name]
 * @author wong <[842687571@qq.com]>
 * Date: 02/12/17
 * Time: 下午5:12
 * @return    [type]    PhpStorm  frame
 */

namespace Core\Swoole\Pipe;


class CommandList
{
    private $list = [];

    function add($command, callable $handler)
    {
        $this->list[$command] = $handler;
        return $this;
    }

    function setDefaultHandler(callable $handler)
    {
        $this->list['__DEFAULT__'] = $handler;
        return $this;
    }

    function getHandler($command)
    {
        if (isset($this->list[$command])) {
            return $this->list[$command];
        } else if (isset($this->list['__DEFAULT__'])) {
            return $this->list['__DEFAULT__'];
        } else {
            return null;
        }
    }
}