<?php

namespace Core\Http\Session;


class Base
{
    protected $session;

    function __construct()
    {
        $this->session = Session::getInstance();
    }

    function sessionName($name = null)
    {
        return $this->session->sessionName($name);
    }

    function savePath($path = null)
    {
        return $this->session->savePath($path);
    }

    function sessionId($sid = null)
    {
        return $this->session->sessionId($sid);
    }

    function destroy()
    {
        return $this->session->destroy();
    }

    function close()
    {
        return $this->session->close();
    }

    function start()
    {
        if (!$this->session->isStart()) {
            return $this->session->start();
        } else {
            trigger_error("session has start");
            return false;
        }
    }
}