<?php
//模型类基类
class Model {
    protected $db; //数据库连接对象
    protected $table; //表名
    protected $fields = array(); //字段列表

    public function __construct($table) {
        $dbconfig['host'] = $GLOBALS['config']['host'];
        $dbconfig['user'] = $GLOBALS['config']['user'];
        $dbconfig['password'] = $GLOBALS['config']['password'];
        $dbconfig['dbname'] = $GLOBALS['config']['dbname'];
        $dbconfig['port'] = $GLOBALS['config']['port'];
        $dbconfig['charset'] = $GLOBALS['config']['charset'];

        $this->db = new Mysql($dbconfig);
        $this->table = $GLOBALS['config']['prefix'] . $table;

        //调用get_fields字段
        $this->get_fields();
    }

    /**
     * 获取表字段列表
     */
    private function get_fields() {
        $sql = "DESC " . $this->table;
        $result = $this->db->get_all($sql);

        foreach ($result as $v) {
            $this->fields[] = $v['Field'];
            if ($v['Key'] == "PRI") {
                //若存在主键，则将其保存在变量$pk中
                $pk = $v['Field'];
            }
        }
        //如果存在主键，则将其加入到字段列表fields中
        if (isset($pk)) {
            $this->fields['pk'] = $pk;
        }
    }

    /**
     * 自动插入记录[单条]
     * @param mixed $list 关联数组
     * @return mixed 成功返回插入的id 失败则返回false
     */
    public function insert($list) {
        $field_list = ''; //字段列表
        $value_list = ''; //值列表字符串
        foreach ($list as $k => $v) {
            if (in_array($k, $this->fields)) {
                $field_list .= "`" . $k . "`,";
                $value_list .= "'" . $v . "',";
            }
        }
        //去除右边逗号
        $field_list = rtrim($field_list, ',');
        $value_list = rtrim($value_list, ',');
        //构造sql语句
        $sql = "INSERT INTO `{$this->table}` ($field_list) VALUES ($value_list)";
        if ($this->db->query($sql)) {
            return $this->db->get_insert_id();
        } else {
            return false;
        }
    }

    /**
     * 自动更新记录
     * @param $list 需要更新的关联数组
     */
    public function update($list) {
        $uplist = '';
        $where = 0;
        foreach ($list as $k => $v) {
            if (in_array($k, $this->fields)) {
                if ($k == $this->fields['pk']) {
                    $where = "`$k`=$v";
                } else {
                    $uplist .= "`$k`='$v'" . ",";
                }
            }
        }
        $uplist = rtrim($uplist, ',');
        $sql = "UPDATE `{$this->table}` SET {$uplist} WHERE {$where}";
        return $this->db->query($sql);
    }

    /**
     * 自动删除
     * 
     */
    public function delete($pk) {
        $where = 0;
        if (is_array($pk)) {
            $where = "`{$this->fields['pk']}` in (" . implode(",", $pk) . ")";
        } else {
            $where = "`{$this->fields['pk']}`=$pk";
        }
        $sql = "DELETE FROM `{$this->table}` WHERE {$where}";
        return $this->db->query($sql); 
    }

    /**
     * 通过主键获取信息
     */
    public function select_by_pk($pk) {
        $sql = "select * from `{$this->table}` where `{$this->fields['pk']}`=$pk";
        return $this->db->get_row($sql);
    }

    /**
     * 获取总的记录数
     */
    public function total($where) {
        if (empty($where)) {
            $sql = "select count(*) from {$this->table}";
        } else {
            $sql = "select count(*) from {$this->table} where $where";
        }
        return $this->db->get_one($sql);
    }

    /**
     * 分页获取信息
     * @param $offset int 偏移量
     * @param $limit int 每次取记录的条数
     * @param $where string where条件,默认为空
     */
    public function page_rows($offset, $limit, $where = '') {
        if (empty($where)) {
            $sql = "select * from {$this->table} limit $offset, $limit";
        } else {
            $sql = "select * from {$this->table}  where $where limit $offset, $limit";
        }
        return $this->db->get_all($sql);
    }

}
