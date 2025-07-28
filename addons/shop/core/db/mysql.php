<?php
namespace db;
use lib;
use PDO;
class mysql {
    # PDO操作实例
    protected $PDOStatement = null;
    # 当前SQL指令
    protected $queryStr = '';
    # 最后插入ID
    protected $lastInsID = null;
    # 返回或者影响记录数
    protected $numRows = 0;
    # 事务指令数
    protected $transTimes = 0;
    # 重连次数
    protected $reConnectTimes = 0;
    # 错误信息
    protected $error = '';
    # 数据库连接ID 支持多个连接
    protected $links = array();
    # 当前连接ID
    protected $_linkID = null;
    # 当前读连接ID
    protected $linkRead = null;
    # 当前写连接ID
    protected $linkWrite = null;
    # 是否读取主库
    protected $readMaster = false;
    // 数据库连接参数配置
    protected $config = array();
    // 查询次数
    protected $queryTimes = 0;
    // 执行次数
    protected $executeTimes = 0;
    // PDO连接参数
    protected $options = array(
        PDO::ATTR_CASE => PDO::CASE_LOWER,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_EMULATE_PREPARES  => false,
    );
    # 性能监控
    private $queryStartTime = 0;
    private $queryEndTime = 0;
    # 服务器断线标识字符
    protected $breakMatchStr = [
        'server has gone away',
        'no connection to the server',
        'Lost connection',
        'is dead or not enabled',
        'Error while sending',
        'decryption failed or bad record mac',
        'server closed the connection unexpectedly',
        'SSL connection has been closed unexpectedly',
        'Error writing data to the connection',
        'Resource deadlock avoided',
        'failed with errno',
        'child connection forced to terminate due to client_idle_limit',
        'query_wait_timeout',
        'reset by peer',
        'Physical connection is not usable',
        'TCP Provider: Error code 0x68',
        'ORA-03114',
        'Packets out of order. Expected',
        'Adaptive Server connection failed',
        'Communication link failure',
        'connection is no longer usable',
        'Login timeout expired',
        'SQLSTATE[HY000] [2002] Connection refused',
        'running with the --read-only option so it cannot execute this statement',
        'The connection is broken and recovery is not possible. The connection is marked by the client driver as unrecoverable. No attempt was made to restore the connection.',
        'SQLSTATE[HY000] [2002] php_network_getaddresses: getaddrinfo failed: Try again',
        'SQLSTATE[HY000] [2002] php_network_getaddresses: getaddrinfo failed: Name or service not known',
        'SQLSTATE[HY000]: General error: 7 SSL SYSCALL error: EOF detected',
        'SQLSTATE[HY000] [2002] Connection timed out',
        'SSL: Connection timed out',
        'SQLSTATE[HY000]: General error: 1105 The last transaction was aborted due to Seamless Scaling. Please retry.',
    ];
    /**
     * 架构函数 读取数据库配置信息
     * @access public
     * @param array $config 数据库配置数组
     */
    public function __construct($config = array()) {
        $this->config = $this->parseConfig($config);
        if (is_array($this->config['params'])) {
            $this->options += $this->config['params'];
        }
    }
    /**
     * 执行查询但只返回PDOStatement对象
     * @access public
     * @param string $sql       sql指令
     * @param array  $bind      参数绑定
     * @param bool   $master    是否在主服务器读操作
     * @param bool   $type 执行类型
     * @return PDOStatement
     * @throws DbException
     */
    public function getPDOStatement(string $sql, array $bind = [], bool $master = false, $type = 'query') {
        try {
            $this->initConnect($this->readMaster ?: $master);
            # 记录SQL语句
            $this->queryStr = $this->getRealSql($sql, $bind);
            //var_dump($this->queryStr);exit;
            if (!empty($bind)) {
                array_walk_recursive($bind, function (&$value, $key) {
                    if (is_string($value)) {
                        $value = $this->escapeString($value);
                    }
                });
            }
            # 释放前次的查询结果
            if (!empty($this->PDOStatement)) $this->free();
            if ($type == 'query') {
                $this->queryTimes++;
            } else {
                $this->executeTimes++;
            }
            
            $this->debug(true);
            # 预处理
            $this->PDOStatement = $this->_linkID->prepare($sql);
            if (false === $this->PDOStatement) {
                throw new \Exception($this->error());
            }
            //$bind ? var_dump($bind) : '';
            # 参数绑定
            foreach ($bind as $key => $val) {
                # 占位符，如果是数字则占位符从1开始
                $param = is_numeric($key) ? $key + 1 : $key;
                if (is_array($val)) {
                    $this->PDOStatement->bindValue($param, $val[0], $val[1]);
                } else {
                    $this->PDOStatement->bindValue($param, $val);
                }
            }
            # 执行查询
            $result = $this->PDOStatement->execute();
            if (false === $result) {
                $this->error();
            }
            $this->debug(false, $this->queryStr);

            $this->reConnectTimes = 0;

            return $this->PDOStatement;
        } catch (\Throwable | \Exception $e) {
            if ($this->transTimes > 0) {
                // 事务活动中时不应该进行重试，应直接中断执行，防止造成污染。
                if ($this->isBreak($e)) {
                    // 尝试对事务计数进行重置
                    $this->transTimes = 0;
                }
            } else {
                if ($this->reConnectTimes < 4 && $this->isBreak($e)) {
                    ++$this->reConnectTimes;
                    return $this->close()->getPDOStatement($sql, $bind, $master, $type);
                }
            }
            if ($e instanceof \PDOException) {
                throw new \Exception($e->getMessage());
            } else {
                throw $e;
            }
        }
    }
    /**
     * 执行查询 返回数据集
     * @access public
     * @param string $str  sql指令
     * @param array $bind  参数绑定
     * @return mixed
     */
    public function query($str, $result_type = 'select', $bind = array(), bool $master = false) {
        $this->getPDOStatement($str, $bind, $master, 'query');
        switch ($result_type) {
            case 'find':
                $return = $this->PDOStatement->fetch(PDO::FETCH_ASSOC);
                break;

            case 'total':
                $row = $this->PDOStatement->fetch(PDO::FETCH_NUM);
                $return = $row[0];
                break;

            default:
                $return = $this->getResult();
        }
        return $return;
    }
    /**
     * 执行语句
     * @access public
     * @param string $str  sql指令
     * @param array $bind  参数绑定
     * @return integer
     */
    public function execute($str, $result_type = 'update', $bind = array()) {
        $this->getPDOStatement($str, $bind, true, 'execute');
        # 分布式数据写入后自动读取主服务器
        if (!empty($this->config['deploy']) && !empty($this->config['read_master'])) {
            $this->readMaster = true;
        }
        switch ($result_type) {
            case 'insert':
                $return = $this->lastInsID = $this->_linkID->lastInsertId();
                break;

            default:
                $return = $this->numRows = $this->PDOStatement->rowCount();
        }
        return $return;
    }
    /**
     * 启动事务
     * @access public
     * @return void
     * @throws \PDOException
     * @throws \Exception
     */
    public function startTrans() {
        try {
            $this->initConnect(true);
            ++$this->transTimes;
            if (1 == $this->transTimes) {
                $this->_linkID->beginTransaction();
            } elseif ($this->transTimes > 1 && $this->supportSavepoint() && $this->_linkID->inTransaction()) {
                $this->_linkID->exec(
                    $this->parseSavepoint('trans' . $this->transTimes)
                );
            }
            $this->reConnectTimes = 0;
        } catch (\Throwable | \Exception $e) {
            if (1 === $this->transTimes && $this->reConnectTimes < 4 && $this->isBreak($e)) {
                --$this->transTimes;
                ++$this->reConnectTimes;
                $this->close()->startTrans();
            } else {
                if ($this->isBreak($e)) {
                    // 尝试对事务计数进行重置
                    $this->transTimes = 0;
                }
                throw $e;
            }
        }
    }
    /**
     * 用于非自动提交状态下面的查询提交
     * @access public
     * @return void
     * @throws \PDOException
     */
    public function commit() {
        $this->initConnect(true);

        if (1 == $this->transTimes && $this->_linkID->inTransaction()) {
            $result = $this->_linkID->commit();
            if (!$result) {
                $this->error();
            }
        }

        --$this->transTimes;
    }
    /**
     * 事务回滚
     * @access public
     * @return void
     * @throws \PDOException
     */
    public function rollback() {
        $this->initConnect(true);

        if ($this->_linkID->inTransaction()) {
            if (1 == $this->transTimes) {
                $result = $this->_linkID->rollBack();
                if (!$result) {
                    $this->error();
                }
            } else if ($this->transTimes > 1 && $this->supportSavepoint()) {
                $this->_linkID->exec(
                    $this->parseSavepointRollBack('trans' . $this->transTimes)
                );
            }
        }

        $this->transTimes = max(0, $this->transTimes - 1);
    }
    /**
     * 是否支持事务嵌套
     * @return bool
     */
    protected function supportSavepoint() {
        return false;
    }
    /**
     * 生成定义保存点的SQL
     * @access protected
     * @param string $name 标识
     * @return string
     */
    protected function parseSavepoint(string $name) {
        return 'SAVEPOINT ' . $name;
    }
    /**
     * 生成回滚到保存点的SQL
     * @access protected
     * @param string $name 标识
     * @return string
     */
    protected function parseSavepointRollBack(string $name) {
        return 'ROLLBACK TO SAVEPOINT ' . $name;
    }
    /**
     * 获得所有的查询数据
     * @access private
     * @return array
     */
    private function getResult() {
        //返回数据集
        $result = $this->PDOStatement->fetchAll(PDO::FETCH_ASSOC);
        $this->numRows = count($result);
        return $result;
    }
    /**
     * 获得查询次数
     * @access public
     * @param boolean $execute 是否包含所有查询
     * @return integer
     */
    public function getQueryTimes($execute = false) {
        return $execute ? $this->queryTimes + $this->executeTimes : $this->queryTimes;
    }
    /**
     * 获得执行次数
     * @access public
     * @return integer
     */
    public function getExecuteTimes() {
        return $this->executeTimes;
    }
    /**
     * 是否断线
     * @access protected
     * @param \PDOException|\Exception $e 异常对象
     * @return bool
     */
    protected function isBreak($e) {
        if (!$this->config['break_reconnect']) {
            return false;
        }
        $error = $e->getMessage();
        foreach ($this->breakMatchStr as $msg) {
            if (false !== stripos($error, $msg)) {
                return true;
            }
        }
        return false;
    }
    /**
     * 释放查询结果
     * @access public
     */
    public function free() {
        $this->PDOStatement = null;
    }
    /**
     * 关闭数据库
     * @access public
     */
    public function close() {
        $this->_linkID = null;
        $this->linkWrite = null;
        $this->linkRead = null;
        $this->links = [];
        $this->transTimes = 0;
        $this->free();
        return $this;
    }
    /**
     * 数据库错误信息
     * 并显示当前的SQL语句
     * @access public
     * @return string
     */
    public function error() {
        if ($this->PDOStatement) {
            $error = $this->PDOStatement->errorInfo();
            $this->error = $error[1] . ':' . $error[2];
        } else {
            $this->error = '';
        }
        if ('' != $this->queryStr) {
            $this->error.= PHP_EOL . ' [ SQL语句 ] : ' . $this->queryStr;
        }
        # 记录错误日志
        lib\logging::write($this->error);
        if ($this->config['debug']) { # 开启数据库调试模式
            throw new \Exception($this->error);
        } else {
            return $this->error;
        }
    }
    /**
     * 获取最近插入的ID
     * @access public
     * @return string
     */
    public function getLastInsID() {
        return $this->lastInsID;
    }
    /**
     * 获取最近的错误信息
     * @access public
     * @return string
     */
    public function getError() {
        return $this->error;
    }
    /**
     * SQL指令安全过滤
     * @access public
     * @param string $str  SQL字符串
     * @return string
     */
    public function escapeString($str) {
        $str = htmlspecialchars($str);
        $magic_quotes_gpc = ini_set('magic_quotes_runtime', 0) ? true : false;
		if ($magic_quotes_gpc) {
			$str = stripslashes($str);
		}
        return addslashes($str);
    }
    /**
     * 数据库调试 记录当前SQL
     * @access protected
     * @param boolean $start  调试开始标记 true 开始 false 结束
     */
    protected function debug($start, $str = '') {
        if ($this->config['debug']) { // 开启数据库调试模式
            if ($start) {
                $this->queryStartTime = microtime(true);
            } else {
                # 记录操作结束时间
                $this->queryEndTime = microtime(true);
                lib\logging::write($str . ' [ RunTime:' . number_format($this->queryEndTime - $this->queryStartTime, 6) . 's ]');
            }
        }
    }
    /**
     * 初始化数据库连接
     * @access protected
     * @param boolean $master 主服务器
     * @return void
     */
    protected function initConnect($master = true) {
        if (!empty($this->config['deploy'])) {
            # 采用分布式数据库
            if ($master || $this->transTimes) {
                if (!$this->linkWrite) {
                    $this->linkWrite = $this->multiConnect(true);
                }

                $this->_linkID = $this->linkWrite;
            } else {
                if (!$this->linkRead) {
                    $this->linkRead = $this->multiConnect(false);
                }

                $this->_linkID = $this->linkRead;
            }
        } else if (!$this->_linkID) {
            # 默认单数据库
            $this->_linkID = $this->connect();
        }
    }
    /**
     * 连接分布式服务器
     * @access protected
     * @param boolean $master 主服务器
     * @return void
     */
    protected function multiConnect($master = false) {
        $config = [];

        # 分布式数据库配置解析
        foreach (['username', 'password', 'hostname', 'hostport', 'database', 'dsn', 'charset'] as $name) {
            $config[$name] = is_string($this->config[$name]) ? explode(',', $this->config[$name]) : $this->config[$name];
        }

        # 主服务器序号
        $m = floor(mt_rand(0, $this->config['master_num'] - 1));

        if ($this->config['rw_separate']) {
            # 主从式采用读写分离
            if ($master) { # 主服务器写入
                $r = $m;
            } else if (is_numeric($this->config['slave_no'])) {
                # 指定服务器读
                $r = $this->config['slave_no'];
            } else {
                # 读操作连接从服务器 每次随机连接的数据库
                $r = floor(mt_rand($this->config['master_num'], count($config['hostname']) - 1));
            }
        } else {
            # 读写操作不区分服务器 每次随机连接的数据库
            $r = floor(mt_rand(0, count($config['hostname']) - 1));
        }
        $dbMaster = false;

        if ($m != $r) {
            $dbMaster = [];
            foreach (['username', 'password', 'hostname', 'hostport', 'database', 'dsn', 'charset'] as $name) {
                $dbMaster[$name] = $config[$name][$m] ?? $config[$name][0];
            }
        }

        $dbConfig = [];

        foreach (['username', 'password', 'hostname', 'hostport', 'database', 'dsn', 'charset'] as $name) {
            $dbConfig[$name] = $config[$name][$r] ?? $config[$name][0];
        }

        return $this->connect($dbConfig, $r, $r == $m ? false : $dbMaster);
    }
    /**
     * 连接数据库方法
     * @access public
     * @param array $config 连接参数
     * @param integer $linkNum 连接序号
     * @param array|bool $autoConnection 是否自动连接主数据库（用于分布式）
     * @return PDO
     * @throws PDOException
     */
    public function connect($config = array(), $linkNum = 0, $autoConnection = false) {
        if (!isset($this->links[$linkNum])) {
            if (empty($config)) {
                $config = $this->config;
            } else {
                $config = array_merge($this->config, $config);
            }
            // 连接参数
            if (isset($config['params']) && is_array($config['params'])) {
                $params = $config['params'] + $this->options;
            } else {
                $params = $this->options;
            }
            if (!empty($config['break_match_str'])) {
                $this->breakMatchStr = array_merge($this->breakMatchStr, (array) $config['break_match_str']);
            }
            try {
                if (empty($config['dsn'])) {
                    $config['dsn'] = $this->parseDsn($config);
                }
                if (version_compare(PHP_VERSION, '5.3.6', '<=')) { //禁用模拟预处理语句
                    $this->options[PDO::ATTR_EMULATE_PREPARES] = false;
                }
                $this->debug(true);
                $this->links[$linkNum] = new PDO($config['dsn'], $config['username'], $config['password'], $params);
                $this->debug(false, 'CONNECT:' . $config['dsn']);
            } catch(\PDOException $e) {
                if ($autoConnection) {
                    lib\logging::write('[ Connection error ]' . var_export($e->getMessage(), true));
                    return $this->connect($autoConnection, $linkNum);
                } else {
                    throw $e;
                }
            }
        }
        return $this->links[$linkNum];
    }
    /**
     * 执行数据库Xa事务
     * @access public
     * @param  callable $callback 数据操作方法回调
     * @param  array    $dbs      多个连接实例对象
     * @return mixed
     * @throws PDOException
     * @throws \Exception
     * @throws \Throwable
     */
    public function transactionXa(callable $callback, array $dbs = []) {
        $xid = uniqid('xa');

        if (empty($dbs)) {
            $dbs[] = $this;
        }

        foreach ($dbs as $key => $db) {
            $db->startTransXa($xid);
        }

        try {
            $result = null;
            if (is_callable($callback)) {
                $result = $callback($this);
            }

            foreach ($dbs as $db) {
                $db->prepareXa($xid);
            }

            foreach ($dbs as $db) {
                $db->commitXa($xid);
            }

            return $result;
        } catch (\Exception | \Throwable $e) {
            foreach ($dbs as $db) {
                $db->rollbackXa($xid);
            }
            throw $e;
        }
    }
    /**
     * 启动XA事务
     * @access public
     * @param  string $xid XA事务id
     * @return void
     */
    public function startTransXa(string $xid) {
        $this->initConnect(true);
        $this->_linkID->exec('XA START \'' . $xid . '\'');
    }
    /**
     * 预编译XA事务
     * @access public
     * @param  string $xid XA事务id
     * @return void
     */
    public function prepareXa(string $xid) {
        $this->initConnect(true);
        $this->_linkID->exec('XA END \'' . $xid . '\'');
        $this->_linkID->exec('XA PREPARE \'' . $xid . '\'');
    }
    /**
     * 提交XA事务
     * @access public
     * @param  string $xid XA事务id
     * @return void
     */
    public function commitXa(string $xid) {
        $this->initConnect(true);
        $this->_linkID->exec('XA COMMIT \'' . $xid . '\'');
    }

    /**
     * 回滚XA事务
     * @access public
     * @param  string $xid XA事务id
     * @return void
     */
    public function rollbackXa(string $xid) {
        $this->initConnect(true);
        $this->_linkID->exec('XA ROLLBACK \'' . $xid . '\'');
    }
    /**
     * 解析pdo连接的dsn信息
     * @access public
     * @param array $config 连接信息
     * @return string
     */
    protected function parseDsn($config) {
        $dsn = 'mysql:dbname=' . $config['database'] . ';host=' . $config['hostname'];
        if (!empty($config['hostport'])) {
            $dsn .= ';port=' . $config['hostport'];
        } else if (!empty($config['socket'])) {
            $dsn .= ';unix_socket=' . $config['socket'];
        }

        if (!empty($config['charset'])) {
            //为兼容各版本PHP,用两种方式设置编码
            $this->options[\PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES ' . $config['charset'];
            $dsn .= ';charset=' . $config['charset'];
        }
        return $dsn;
    }
    /**
     * 数据库连接参数解析
     * @static
     * @access private
     * @param mixed $config
     * @return array
     */
    private function parseConfig($config) {
        if (!empty($config)) {
            $config = array_change_key_case($config);
            $config = array(
                'username' => $config['dbuser'],
                'password' => $config['dbpwd'],
                'hostname' => $config['dbhost'],
                'hostport' => $config['dbport'],
                'database' => $config['dbname'],
                'dsn' => isset($config['dsn']) ? $config['dsn'] : null,
                'params' => isset($config['db_params']) ? $config['db_params'] : null,
                'charset' => isset($config['dbcharset']) ? $config['dbcharset'] : 'utf8',
                'deploy' => isset($config['deploy']) ? $config['deploy'] : 0,
                'read_master' => isset($config['read_master']) ? $config['read_master'] : false,
                'rw_separate' => isset($config['rw_separate']) ? $config['rw_separate'] : false,
                'master_num' => isset($config['master_num']) ? $config['master_num'] : 1,
                'slave_no' => isset($config['slave_no']) ? $config['slave_no'] : '',
                'break_reconnect' => isset($config['break_reconnect']) ? $config['break_reconnect'] : false,
                'break_match_str' => isset($config['break_match_str']) ? $config['break_match_str'] : array(),
                'debug' => isset($config['db_debug']) ? $config['db_debug'] : config('db.db_debug') ,
            );
        } else {
            $config = array(
                'username' => config('db.dbuser') ,
                'password' => config('db.dbpwd') ,
                'hostname' => config('db.dbhost') ,
                'hostport' => config('db.dbport') ,
                'database' => config('db.dbname') ,
                'dsn' => config('db.dsn') ,
                'params' => config('db.db_params') ,
                'charset' => config('db.dbcharset') ,
                'deploy' => config('db.deploy') ,
                'read_master' => config('db.read_master') ,
                'rw_separate' => config('db.rw_separate') ,
                'master_num' => config('db.master_num') ,
                'slave_no' => config('db.slave_no') ,
                'break_reconnect' => config('db.break_reconnect') ,
                'break_match_str' => config('db.break_match_str') ,
                'debug' => config('db.db_debug') ,
            );
        }
        return $config;
    }
    public function getRealSql($sql, array $bind = []) {
        if (false === strpos($sql, '?') || count($bind) == 0) {
            return $sql;
        }
        foreach ($bind as $val) {
            $value = is_array($val) ? $val[0] : $val;
            $type = is_array($val) ? $val[1] : (is_string($value) ? PDO::PARAM_STR : PDO::PARAM_INT);
            if (PDO::PARAM_STR == $type) {
                $value = '\'' . $this->escapeString($value) . '\'';
            } elseif (PDO::PARAM_INT == $type) {
                $value = (float)$value;
            }
            $sql = substr_replace($sql, $value, strpos($sql, '?') , 1);
        }
        return rtrim($sql);
    }
    /**
     * 析构方法
     * @access public
     */
    public function __destruct() {
        // 释放查询
        if ($this->PDOStatement) {
            $this->free();
        }
        // 关闭连接
        $this->close();
    }
}