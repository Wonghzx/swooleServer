<?php

/**
 * [AbstractCommandRegister.php name]
 * @author wong <[842687571@qq.com]>
 * Date: 02/12/17
 * Time: 下午5:15
 * @return    [type]    PhpStorm  frame
 */

namespace Core\Swoole\Pipe;

abstract class AbstractCommandRegister
{
    abstract function register(CommandList $commandList);
}