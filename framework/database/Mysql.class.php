<?php

//使用mysql扩展 mysqli来进行调用
class Mysql {
    protected $mysqli;  //数据库连接资源
    protected $sql;

    /**
     * 构造函数，连接服务器，选择数据库，设置字符集
     */
    public function __construct($config = array()) {
        $host = isset($config['host']) ? $config['host'] : 'localhost';
        $user = isset($config['user']) ? $config['user'] : 'root';
        $password = isset($config['password']) ? $config['password'] : '';
        $dbname = isset($config['dbname']) ? $config['dbname'] : '';
        $port = isset($config['port']) ? $config['port'] : '3306';
        $charset = isset($config['charset']) ? $config['charset'] : 'utf8';

        $this->mysqli = new Mysqli($host, $user, $password, $dbname);
        if (!$this->mysqli) {
            die("连接数据库错误");
        }
        $this->mysqli->set_charset($charset);
    }

    /**
     * 执行sql语句
     */
    public function query($sql) {
        //写日志
        if ($GLOBALS['config']['log']) {
            $str = "[" . date("Y-m-d H:i:s") . "]" . $sql . PHP_EOL;
            file_put_contents("log.txt", $str, FILE_APPEND);
        }

        $this->sql = $sql;
        $result = $this->mysqli->query($this->sql);
        if (!$result) {
            die($this->mysqli->errno . ":" . $this->mysqli->error . "<br />出错语句为:" . $this->sql . "<br />");
        }
        return $result;
    }

    /**
     * 获取第一条记录的第一个字段
     */
    public function get_one($sql) {
        $result = $this->query($sql);
        $row = $result->fetch_row();
        if ($row) {
            return $row[0];
        } else {
            return false;
        }
    }

    /**
     * 获取一条记录
     */
    public function get_row($sql) {
        if ($result = $this->query($sql)) {
            $row = $result->fetch_assoc();
            return $row;
        } else {
            return false;
        }
    }

    /**
     * 获取所有记录
     */
    public function get_all($sql) {
        $result = $this->query($sql);
        //$list = array();
        $list = $result->fetch_all(MYSQLI_ASSOC);
//        while ($row = $result->fetch_assoc()) {
//            $list[] = $row;
//        }
        return $list;
    }

    /**
     * 获取某一列的值
     */
    public function get_col($sql) {
        $result = $this->query($sql);
        $list = array();
        while ($row = $result->fetch_row()) {
            $list[] = $row[0];
        }
        return $list;
    }

    /**
     * 获取上一步insert操作产生的id
     */
    public function get_insert_id() {
        return $this->mysqli->insert_id;
    }

    /**
     *
     */
}

