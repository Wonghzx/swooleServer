<?php

class Server
{
    private $servers;

    private $fd;

    private $conn;

    private $groups;

    function __construct()
    {
        $this->DB();
        $this->servers = new swoole_websocket_server("0.0.0.0", 9502);
        $this->servers->set(array(
            'worker_num' => 8,
            'daemonize' => false,
            'max_request' => 10000,
            'dispatch_mode' => 2,
            'debug_mode' => 1
        ));

        $this->servers->on('open',array($this,'onOpen'));
        $this->servers->on('message',array($this,'onMessage'));
        $this->servers->on('close',array($this,'onClose'));

        $this->servers->start();
    }

    /**
     * [onOpen description]
     * @param  [type] $server [description]
     * @param  [type] $frame  [description]
     * @return [type]         [description]
     */
    public function onOpen($server, $frame)
    {
       // $this->fd = $frame->fd;
       file_put_contents('log.txt' , $frame->fd);
    }

    /**
     * [onMessage description]
     * @param  [type] $server [description]
     * @param  [type] $frame  [description]
     * @return [type]         [description]
     */
    public function onMessage($server, $frame)
    {
        $chatData = json_decode($frame->data,true);
        $result = [];
        if (isset($chatData['content'])) {
            $result = $this->addMessage($chatData);
            $result = json_encode($result);
            $msg = file_get_contents('log.txt');
            $fdArr = $this->getBindId($chatData['groups']);
            $fdArr = array_column($fdArr, 'fd');
            foreach ($fdArr as &$value) {
                $server->push($value, $result);
            }
        } else {
            $this->unBind($chatData['fid'],$chatData['groups']);
            if ($this->bindId($frame->fd,$chatData['groups'],$chatData['fid'])) {
                $result = $this->loadHistory($chatData['fid'],$chatData['tid'],'',$chatData['groups']);
                $result = json_encode($result);
                $server->push($frame->fd, $result);
            }
        }
    }

    /**
     * [onClose description]
     * @param  [type] $server [description]
     * @param  [type] $fd     [description]
     * @return [type]         [description]
     */
    public function onClose($server, $fd)
    {
        // $this->unBind($fd);
        echo "client {$fd} closed\n";
    }


    /**
     * [bind 绑定 链接客户端自增ID]
     * @param  [type] $uid [description]
     * @param  [type] $fd  [description]
     * @return [type]      [description]
     */
    private function bindId($fd,$groups_id,$haystack = null)
    {

        $sql = " INSERT INTO fd (fd,groups_id,client_id) values ('$fd','$groups_id','$haystack') ";
        if ($this->conn->query($sql)) {
            return true;
        }
    }


    /**
     * [getBindId 得到客户端自增ID]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    private function getBindId($id)
    {
        $sql = " SELECT * FROM fd WHERE groups_id = '{$id}' ";
        $data = [];
        if ($query = $this->conn->query($sql)) {
            while ($row = mysqli_fetch_assoc($query)) {
                $data[] = $row;
            }
        }
        return $data;
    }

    /**
     * [unBind 删除绑定的ID]
     * @return [type] [description]
     */
    private function unBind($client_id = null,$groupsID = null)
    {
        if (empty($client_id)) {
            $sql = " DELETE FROM fd WHERE groups_id = '{$groupsID}'";
        } else {
            $sql = " DELETE FROM fd WHERE groups_id = '{$groupsID}' AND client_id = '{$client_id}' ";
        }
        if ($this->conn->query($sql)) {
            return true;
        }
    }

    /**
     * [addMessage 添加聊天信息]
     * @param array $params [description]
     */
    private function addMessage(array $params)
    {
        $data = [];
        $sql = " INSERT INTO msg (fid,tid,content,groups) VALUES ('".$params['fid']."','".$params['tid']."','".$params['content']."','".$params['groups']."') ";
        if ($this->conn->query($sql)) {
            $id = $this->conn->insert_id; //得到插入最新数据的ID
            $data = $this->loadHistory($params['fid'],$params['tid'],$id); //根据 用户ID && 聊天室ID 拿出最聊天室信息 
            return $data;
        }
    }


    /**
     * [loadHistory description]
     * @param  [type] $fid [发送ID]
     * @param  [type] $tid [接收ID]
     * @param  [type] $id  [是否历史]
     * @return [type] []   [description]
     */
    private function loadHistory($fid, $tid, $id = null,$groups = null)
    {
        if (empty($id)) {
            $sql = "SELECT m.*,u.user_nickname,u.user_photo FROM msg AS m LEFT JOIN `user` AS u ON
                    m.fid = u.id
                    WHERE m.groups = {$groups} AND m.fid = '{$fid}' 
                    OR (m.tid LIKE '%{$fid}%') ";
        } else {
            $sql = "SELECT m.*,u.user_nickname,u.user_photo FROM msg AS m LEFT JOIN `user` AS u ON m.fid = u.id
                    WHERE m.id = '{$id}' ";
        }
        $data = [];
        if ($query = $this->conn->query($sql)) {
            while ($row = mysqli_fetch_assoc($query)) {
                $data[] = $row;
            }
        }
        return $data;
    }


    /**
     * [getGroups description]
     * @return [type] [description]
     */
    private function getGroups($groupsId)
    {
        $sql = " SELECT users FROM groups WHERE id = '{$groupsId}' ";
        $row = [];
        if ($query = $this->conn->query($sql)) {
            $data = mysqli_fetch_assoc($query);
            $row = $data;
        }
        return $row;
    }

    /**
     * [DB description]
     */
    private function DB()
    {

        $conn = mysqli_connect("192.168.1.161", "root", "xcrozz###");
        if (!$conn) {
            die('Could not connect: ' . mysql_error());
        } else {
            mysqli_select_db($conn, "test");
        }
        $this->conn = $conn;
    }
}
$server = new Server();