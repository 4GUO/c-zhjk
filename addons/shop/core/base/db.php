<?php
namespace base;
use PDO;
use lib;
class db {
    // 当前数据库操作对象
    protected $db = null;
    // 数据库对象池
    private $_db = array();
    // 主键名称
    protected $pk = 'id';
    // 主键是否自动增长
    protected $autoinc = false;
    // 数据表前缀
    protected $tablePrefix = null;
    // 表集合
    protected $tables = array();
    // 模型名称
    protected $modelName = '';
    // 数据库名称
    protected $dbName = '';
    //数据库配置
    protected $config = array();
    // 数据表名（不包含表前缀）
    protected $tableName = '';
    // 实际数据表名（包含表前缀）
    protected $trueTableName = '';
    // 最近错误信息
    protected $error = '';
    // 字段信息
    protected $fields = array();
    // 数据信息
    protected $data = array();
    // 查询表达式参数
    protected $options = array();
    // 链操作方法列表
    protected $methods = array(
        'table',
        'field',
        'where',
        'order',
        'limit',
        'group',
        'having',
        'master',
        'explain',
        'partition',
        'lock',
    );
    /**
     * 架构函数
     * 取得DB类的实例对象 字段检查
     * @access public
     * @param string $name 模型名称
     * @param string $tablePrefix 表前缀
     * @param mixed $connection 数据库连接信息
     */
    public function __construct($name = '', $tablePrefix = '', $connection = array()) {
        # 模型初始化
        $this->_initialize();
        # 获取模型名称
        if (!empty($name)) {
            if (strpos($name, '.')) { // 支持 数据库名.模型名的 定义
                list($this->dbName, $this->modelName) = explode('.', $name);
            } else {
                $this->modelName = $name;
            }
        } else if (empty($this->modelName)) {
            $this->modelName = get_class($this);
        }
        if (empty($connection)) {
            $this->config = config('db');
        } else {
            $this->config = array_merge(config('db'), $connection);
        }
        # 设置表前缀
        if (!empty($tablePrefix)) {
            $this->tablePrefix = $tablePrefix;
        } else if (is_null($tablePrefix)) { #设置null表示无前缀
            $this->tablePrefix = '';
        } else if (isset($this->config['dbprefix'])) {
            $this->tablePrefix = $this->config['dbprefix'];
        }
        # 数据库初始化操作
        # 获取数据库操作对象
        # 当前模型有独立的数据库连接信息
        $this->db(0, $this->config, true);
    }
    # 回调方法 初始化模型
    protected function _initialize() {}
    /**
     * 切换当前的数据库连接
     * @access public
     * @param integer $linkNum  连接序号
     * @param mixed $config  数据库连接信息
     * @param boolean $force 强制重新连接
     * @return Model
     */
    public function db($linkNum = 0, $config = array(), $force = false) {
        if (0 === $linkNum && $this->db) {
            return $this->db;
        }
        if (!isset($this->_db[$linkNum]) || $force) {
            # 创建一个新的实例
            $this->_db[$linkNum] = new \db\mysql($config);
        } else if (NULL === $config) {
            $this->_db[$linkNum]->close(); // 关闭数据库连接
            unset($this->_db[$linkNum]);
            return;
        }
        # 切换数据库连接
        $this->db = $this->_db[$linkNum];
        # 拉表名
        $this->getTableName();
        # 字段检测
        if(!empty($this->modelName)) $this->_checkTableInfo();
        return $this;
    }
    /**
     * 自动检测数据表信息
     * @access protected
     * @return void
     */
    protected function _checkTableInfo() {
        # 如果不是Model类 自动记录数据表信息
        # 只在第一次执行记录
        if (empty($this->fields)) {
            # 如果数据表字段没有定义则自动获取
            if ($this->config['db_fields_cache']) {
                $db = $this->dbName ?: $this->config['dbname'];
                $fields = cache::get('_fields/' . strtolower($db . '.' . $this->tablePrefix . $this->modelName));
                if ($fields) {
                    $this->fields = $fields;
                    if (!empty($fields['_pk'])) {
                        $this->pk = $fields['_pk'];
                    }
                    return;
                }
            }
            # 每次都会读取数据表信息
            $this->flush();
        }
    }
    /**
     * 获取字段信息并缓存
     * @access public
     * @return void
     */
    public function flush() {
        // 缓存不存在则查询数据表信息
        $fields = $this->_getFields($this->getTableName());
        if (!$fields) { // 无法获取字段信息
            return false;
        }
        $this->fields = array_keys($fields);
        unset($this->fields['_pk']);
        foreach ($fields as $key => $val) {
            // 记录字段类型
            $type[$key] = $val['type'];
            if ($val['primary']) {
                // 增加复合主键支持
                if (isset($this->fields['_pk']) && $this->fields['_pk'] != null) {
                    if (is_string($this->fields['_pk'])) {
                        $this->pk = array(
                            $this->fields['_pk']
                        );
                        $this->fields['_pk'] = $this->pk;
                    }
                    $this->pk[] = $key;
                    $this->fields['_pk'][] = $key;
                } else {
                    $this->pk = $key;
                    $this->fields['_pk'] = $key;
                }
                if ($val['autoinc']) $this->autoinc = true;
            }
        }
        // 记录字段类型信息
        $this->fields['_type'] = $type;
        // 2008-3-7 增加缓存开关控制
        if ($this->config['db_fields_cache']) {
            // 永久缓存数据表信息
            $db = $this->dbName ?: $this->config['dbname'];
            cache::set('_fields/' . strtolower($db . '.' . $this->tablePrefix . $this->modelName) , $this->fields);
        }
    }
    /**
     * 取得数据表的字段信息
     */
    private function _getFields($tableName) {
        list($tableName) = explode(' ', $tableName);
        if (strpos($tableName, '.')) {
        	list($dbName, $tableName) = explode('.', $tableName);
			$sql = 'SHOW COLUMNS FROM `' . $dbName . '`.`' . $tableName . '`';
        } else {
        	$sql = 'SHOW COLUMNS FROM `' . $tableName . '`';
        }
        
        $result = $this->db->query($sql, '', array(), true);
        $info = array();
        if ($result) {
            foreach ($result as $key => $val) {
				$val = array_change_key_case($val,  CASE_LOWER);
                $info[$val['field']] = array(
                    'name' => $val['field'],
                    'type' => $val['type'],
                    'notnull' => (bool) ($val['null'] === ''), // not null is empty, null is yes
                    'default' => $val['default'],
                    'primary' => (strtolower($val['key']) == 'pri'),
                    'autoinc' => (strtolower($val['extra']) == 'auto_increment'),
                );
            }
        }
        return $info;
    }
    /**
     * 得到完整的数据表名
     * @access public
     * @return string
     */
    public function getTableName() {
        //if (empty($this->trueTableName)) {
            $tabName = !empty($this->tablePrefix) ? $this->tablePrefix : '';
            if (!empty($this->tableName)) {
                $tabName .= $this->tableName;
            } else {
                $tabName .= $this->modelName;
            }
            $this->trueTableName = strtolower($tabName);
        //}
        return (!empty($this->dbName) ? $this->dbName . '.' : '') . $this->trueTableName;
    }
    /**
     * 获取主键名称
     * @access public
     * @return string
     */
    public function getPk() {
        return $this->pk;
    }
    /**
     * 得到分表的的数据表名
     * @access public
     * @param  array  $data  操作的数据
     * @param  string $field 分表依据的字段
     * @param  array  $rule  分表规则
     * @return array
     */
    public function getPartitionTableName($data, $field, $rule = []) {
        // 对数据表进行分区
        $true_table_name = $this->tablePrefix . $rule['table_name'];
        $true_table_name = (!empty($this->dbName) ? $this->dbName . '.' : '') . $true_table_name;
        if ($field && isset($data[$field])) {
            $value = $data[$field];
            $type = $rule['type'];
            switch ($type) {
                case 'id':
                    // 按照id范围分表
                    $step = $rule['expr'];
                    $seq  = floor($value / $step) + 1;
                    break;
                case 'year':
                    // 按照年份分表
                    if (!is_numeric($value)) {
                        $value = strtotime($value);
                    }
                    $seq = date('Y', $value) - $rule['expr'] + 1;
                    break;
                case 'mod':
                    // 按照id的模数分表
                    $seq = ($value % $rule['num']) + 1;
                    break;
                case 'md5':
                    // 按照md5的序列分表
                    $seq = (ord(substr(md5($value), 0, 1)) % $rule['num']) + 1;
                    break;
                default:
                    if (function_exists($type)) {
                        // 支持指定函数哈希
                        $value = $type($value);
                    }

                    $seq = (ord(substr($value, 0, 1)) % $rule['num']) + 1;
            }

            return $true_table_name . '_' . $seq;
        }
        // 当设置的分表字段不在查询条件或者数据中
        // 进行联合查询，必须设定 partition['num']
        $tableName = [];
        for ($i = 0; $i < $rule['num']; $i++) {
            $tableName[] = 'SELECT * FROM ' . $true_table_name . '_' . ($i + 1);
        }

        return ['( ' . implode(' UNION ', $tableName) . ' )' => $rule['table']];
    }
    
    /**
     * 利用__call方法实现一些特殊的Model方法
     * @access public
     * @param string $method 方法名称
     * @param array $args 调用参数
     * @return mixed
     */
    public function __call($method, $args) {
        $method = strtolower($method);
        $methods = array_flip($this->methods);
        if (isset($methods[$method])) {
            if (empty($args[0]) || (is_string($args[0]) && trim($args[0]) === '')) {
                $this->options[$method] = '';
            } else {
                $this->options[$method] = $args;
            }
            if ($method == 'limit') {
                if ($args[0] == '0') {
                    $this->options[$method] = $args;
                }
            }
            if ($method == 'table') {
                if (strpos($args[0], '.')) { // 支持 数据库名.表名的 定义
                    list($this->dbName, $this->modelName) = explode('.', $args[0]);
                } else {
                    $this->modelName = $args[0];
                }
                # 获取表名
                $this->tableName = $this->modelName;
                $this->getTableName();
                # 获取表名
                $this->_checkTableInfo();
            }
            if ($method == 'partition') {
                $partition_data = $args[0] ?? array();
                $partition_field = $args[1] ?? '';
                $partition_rule = $args[2] ?? array();
                $this->trueTableName = $this->getPartitionTableName($partition_data, $partition_field, $partition_rule);
            }
        } else {
            throw new \Exception('Calling class ' . get_class($this) . ' Method ' . $method . '() Does not exist!');
        }
        return $this;
    }
    public function find() {
        $where = '';
        $bind = [];
        if (!empty($this->options['where'])) {
            $where_arr = $this->_comWhere($this->options['where']);
            $bind = $where_arr['bind'];
            $where = $where_arr['where'];
        }
        $fields = !empty($this->options['field']) ? $this->options['field'][0] : '*';
        $order = !empty($this->options['order']) ? ' ORDER BY ' . $this->options['order'][0] : '';
        $sql = 'SELECT ' . $fields . ' FROM ' . $this->trueTableName . $where . $order . ' LIMIT 1';
		$master = !empty($this->options['master']) ? $this->options['master'][0] : false;
        return $this->query($sql, 'find', $bind, $master);
    }
    public function select() {
        $where = '';
		$having = '';
        $bind = [];
        if (!empty($this->options['where'])) {
            $where_arr = $this->_comWhere($this->options['where']);
            $bind = $where_arr['bind'];
            $where = $where_arr['where'];
        } else if (!empty($this->options['having'])) {
            $having_arr = $this->_comWhere($this->options['having'], 'having');
            $bind = $having_arr['bind'];
            $having = $having_arr['where'];
		}
        $fields = !empty($this->options['field']) ? $this->options['field'][0] : '*';
        $order = !empty($this->options['order']) ? ' ORDER BY ' . $this->options['order'][0] : '';
        $limit = !empty($this->options['limit']) ? $this->_comLimit($this->options['limit']) : '';
        $group = !empty($this->options['group']) ? ' GROUP BY ' . $this->options['group'][0] : '';
        $sql = 'SELECT ' . $fields . ' FROM ' . $this->trueTableName . $where . $group . $having . $order . $limit;
		$master = !empty($this->options['master']) ? $this->options['master'][0] : false;
        return $this->query($sql, 'select', $bind, $master);
    }
    public function total() {
        $where = '';
        $bind = [];
        if (!empty($this->options['where'])) {
            $where_arr = $this->_comWhere($this->options['where']);
            $bind = $where_arr['bind'];
            $where = $where_arr['where'];
        }
        $sql = 'SELECT COUNT(*) as count FROM ' . $this->trueTableName . $where;
		$master = !empty($this->options['master']) ? $this->options['master'][0] : false;
        return $this->query($sql, 'total', $bind, $master);
    }
    public function insert($array = array(), $replace = false) {
        $fields = '`' . implode('`,`', array_keys($array)) . '`';
        $sql = ($replace ? 'REPLACE' : 'INSERT') . ' INTO ' . $this->trueTableName . '(' . $fields . ') VALUES (' . implode(',', array_fill(0, count($array) , '?')) . ')';
        return $this->execute($sql, 'insert', array_values($array));
    }
    public function insertAll($array = array(), $replace = false) {
        if (!is_array($array[0])) {
            return false;
        }
        $fields = array_keys($array[0]);
        $values = [];
        $data_array = [];
        foreach ($array as $item) {
            foreach ($item as $key => $val) {
                if (is_scalar($val)) {
                    $data_array[] = $val;
                }
            }
            $values[] = '(' . implode(',', array_fill(0, count($item) , '?')) . ')';
        }
        $fields = '`' . implode('`,`', $fields) . '`';
        $sql = ($replace ? 'REPLACE' : 'INSERT') . ' INTO ' . $this->trueTableName . ' (' . $fields . ') VALUES ' . implode(',', $values);
        return $this->execute($sql, 'insertAll', $data_array);
    }
    public function update($data = array()) {
        $bind = [];
        $pk = $this->getPk();
        if (is_array($data)) {
			$pri_value = '';
            if (isset($data[$pk])) {
                $pri_value = $data[$pk];
                unset($data[$pk]);
            }
            $s = '';
            foreach ($data as $k => $v) {
                $s .= '`' . $k . '`' . '=?,';
                $bind[] = $v;
            }
            $s = rtrim($s, ',');
            $setfield = $s;
        } else {
            $setfield = $data;
            $pri_value = '';
        }
        $order = !empty($this->options['order']) ? ' ORDER BY ' . $this->options['order'][0] : '';
        $limit = !empty($this->options['limit']) ? $this->_comLimit($this->options['limit']) : '';
        if (!empty($this->options['where'])) {
            $where = $this->_comWhere($this->options['where']);
            $sql = 'UPDATE ' . $this->trueTableName . ' SET ' . $setfield . $where['where'];
            if (!empty($where['bind'])) {
                foreach ($where['bind'] as $v) {
                    $bind[] = $v;
                }
            }
            $sql .= $order . $limit;
        } else {
            $sql = 'UPDATE ' . $this->trueTableName . ' SET ' . $setfield . ' WHERE ' . $pk . '=?';
            $bind[] = $pri_value;
        }
        return $this->execute($sql, 'update', $bind);
    }
    public function delete() {
        $where = '';
        $bind = [];
        $pk = $this->getPk();
        if (!empty($this->options['where'])) {
            $where_arr = $this->_comWhere($this->options['where']);
            $bind = $where_arr['bind'];
            $where = $where_arr['where'];
        }
        $order = !empty($this->options['order']) ? ' ORDER BY ' . $this->options['order'][0] : '';
        $limit = !empty($this->options['limit']) ? $this->_comLimit($this->options['limit']) : '';
        if ($where == '' && $limit == '') {
            $where = ' WHERE ' . $pk . '=\'\'';
        }
        $sql = 'DELETE FROM ' . $this->trueTableName . $where . $order . $limit;
        return $this->execute($sql, 'delete', $bind);
    }
    /**
     * SQL查询
     * @access public
     * @param string $sql  SQL指令
     * @return mixed
     */
    public function query($sql, $method = 'select', $bind = [], $master = false) {
        if (!empty($this->options['lock'])) {
			$sql .= ' FOR UPDATE ';
		}
        if (!empty($this->options['explain'])) {
			$this->_getExplain($this->db->getRealSql($sql, $bind), false);
		}
        return $this->db->query($sql, $method, $bind, $master);
    }
    /**
     * 执行SQL语句
     * @access public
     * @param string $sql  SQL指令
     * @return false | integer
     */
    public function execute($sql, $method, $bind = []) {
        return $this->db->execute($sql, $method, $bind);
    }
    private function _comWhere($args, $type = 'where') {
		if ($type == 'where') {
			$where = ' WHERE ';
		} else if ($type == 'having') {
			$where = ' HAVING ';
		}
        $bind = [];
        if (empty($args)) {
            return ['where' => '', 'bind' => $bind];
        }
        $pk = $this->getPk();
        foreach ($args as $option) {
            if (empty($option)) {
                $where = '';
                continue;
            } else {
                if (is_string($option)) {
                    if (!empty($option[0]) && is_numeric($option[0])) {
						//'1,2,3'
                        $option = explode(',', $option);
                        $where.= $pk . ' IN(' . implode(',', array_fill(0, count($option) , '?')) . ')';
                        $bind = [$option, is_string($option) ? PDO::PARAM_STR : PDO::PARAM_INT];
                        continue;
                    } else {
						//原始查询
                        $where .= $option;
                        continue;
                    }
                } else {
                    if (is_numeric($option)) {
						//1
                        $where .= $pk . '=?';
                        $bind[0] = [$option, is_string($option) ? PDO::PARAM_STR : PDO::PARAM_INT];
                        continue;
                    } else {
                        if (is_array($option)) {
                            if (isset($option[0])) {
								//array(1,2,3,4)
                                $where .= $pk . ' IN(' . implode(',', array_fill(0, count($option) , '?')) . ')';
                                $bind = [$option, is_string($option) ? PDO::PARAM_STR : PDO::PARAM_INT];
                                continue;
                            }
                            foreach ($option as $k => $v) {
                                if (!empty($v) && is_array($v)) {
                                    if (strpos($k, ' !=')) {
										//array('uid !=' => array(1,2,3,4))
                                        $k_arr = explode(' !=', $k);
                                        $where.= $k_arr[0] . ' NOT IN(' . implode(',', array_fill(0, count($v) , '?')) . ')';
                                        unset($k_arr);
                                        foreach ($v as $val) {
                                            $bind[] = [$val, is_string($val) ? PDO::PARAM_STR : PDO::PARAM_INT];
                                        }
                                    } else {
										//array('uid' => array(1,2,3,4))
                                        $where.= $k . ' IN(' . implode(',', array_fill(0, count($v) , '?')) . ')';
                                        foreach ($v as $val) {
                                            $bind[] = [$val, is_string($val) ? PDO::PARAM_STR : PDO::PARAM_INT];
                                        }
                                    }
                                } else {
                                    if (strpos($k, ' ')) {
										// array('time >' => '1234567890')，条件key中带运算符
                                        $where.= $k . '?';
										$k_arr = array_filter(explode(' ', $k));
                                        $type = $pk == $k_arr[0] ? PDO::PARAM_INT : PDO::PARAM_STR;
                                        foreach ($this->fields['_type'] as $field_name => $field_type) {
                                            if ($k_arr[0] == $field_name) {
                                                $type = $this->_getFieldBindType($field_type);
                                                break;
                                            }
                                        }
                                        $bind[] = [$v, $type];
										unset($k_arr);
                                    } else {
                                        if (isset($v[0]) && $v[0] == '%' && substr($v, -1) == '%') {
											//array('name' => '%中%')，LIKE操作
                                            $where.= $k . ' LIKE ?';
                                            $type = $pk == $k ? PDO::PARAM_INT : PDO::PARAM_STR;
                                            foreach ($this->fields['_type'] as $field_name => $field_type) {
                                                if ($k == $field_name) {
                                                    $type = $this->_getFieldBindType($field_type);
                                                    break;
                                                }
                                            }
                                            $bind[] = [$v, $type];
                                        } else {
											//array('id' => 1)
                                            $where.= $k . '=?';
                                            $type = $pk == $k ? PDO::PARAM_INT : PDO::PARAM_STR;
                                            foreach ($this->fields['_type'] as $field_name => $field_type) {
                                                if ($k == $field_name) {
                                                    $type = $this->_getFieldBindType($field_type);
                                                    break;
                                                }
                                            }
                                            $bind[] = [$v, $type];
                                        }
                                    }
                                }
                                $where.= ' AND ';
                            }
                            $where = rtrim($where, 'AND ');
                            $where.= ' OR ';
                            continue;
                        }
                    }
                }
            }
        }
        $where = rtrim($where, 'OR ');
        return ['where' => $where, 'bind' => $bind];
    }
    private function _comLimit($args) {
        if (count($args) == 2) {
            return ' LIMIT ' . $args[0] . ',' . $args[1];
        } else {
            if (count($args) == 1) {
                return ' LIMIT ' . $args[0];
            } else {
                return '';
            }
        }
    }
    /**
     * 获取字段绑定类型
     * @access public
     * @param string $type 字段类型
     * @return integer
     */
    private function _getFieldBindType($type) {
        if (0 === strpos($type, 'set') || 0 === strpos($type, 'enum')) {
            $bind = PDO::PARAM_STR;
        } elseif (preg_match('/(int|double|float|decimal|real|numeric|serial|bit)/is', $type)) {
            $bind = PDO::PARAM_INT;
        } elseif (preg_match('/bool/is', $type)) {
            $bind = PDO::PARAM_BOOL;
        } else {
            $bind = PDO::PARAM_STR;
        }
        return $bind;
    }
    private function _getExplain($sql, $debug = true) {
        $result = $this->db->query('EXPLAIN ' . $sql, 'select');
        $result = array_change_key_case($result, CASE_LOWER);
        if (isset($result['extra'])) {
            if (strpos($result['extra'], 'filesort') || strpos($result['extra'], 'temporary')) {
                lib\logging::write('[ EXPLAIN SQL ]' . $sql . '[' . $result['extra'] . ']');
            }
        }
		if ($debug === false) {
			lib\logging::write('[ EXPLAIN SQL ]' . $sql . '[' . var_export($result, true) . ']');
		}
        return $result;
    }
    /**
     * 启动事务
     * @access public
     * @return void
     */
    public function beginTransaction() {
        $this->commit();
        $this->db->startTrans();
        return;
    }
    /**
     * 提交事务
     * @access public
     * @return boolean
     */
    public function commit() {
        return $this->db->commit();
    }
    /**
     * 事务回滚
     * @access public
     * @return boolean
     */
    public function rollBack() {
        return $this->db->rollback();
    }
    /**
     * 执行数据库Xa事务
     * @access public
     * @param  callable $callback 数据操作方法回调
     * @param  array    $dbs      多个查询对象或者连接对象
     * @return mixed
     * @throws PDOException
     * @throws \Exception
     * @throws \Throwable
     */
    public function transactionXa(callable $callback, array $dbs = []) {
        return $this->db->transactionXa($callback, $dbs);
    }
    /**
     * 返回最后插入的ID
     * @access public
     * @return string
     */
    public function getLastInsID() {
        return $this->db->getLastInsID();
    }
}