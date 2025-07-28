<?php
namespace base;
use PDO;
use lib;
class model {
    /** @var PDOStatement PDO操作实例 */
    private $PDOStatement;
    /** @var PDO[] 数据库连接ID 支持多个连接 */
    private static $links = [];
    /** @var PDO 当前连接ID */
    private static $linkID = null;
    private static $linkRead = null;
    private static $linkWrite = null;
    // PDO连接参数
    private $params = [PDO::ATTR_CASE => PDO::CASE_NATURAL, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL, PDO::ATTR_STRINGIFY_FETCHES => false, PDO::ATTR_EMULATE_PREPARES => false];
    private $trueTableName = '';
    private $fieldList = [];
    private $auto;
    private $options = ['table' => '', 'field' => '', 'where' => '', 'order' => '', 'limit' => '', 'group' => '', 'having' => '', 'master' => '', 'explain' => '', 'lock' => ''];
    private $config = [];
    // 事务指令数
    private $transTimes = 0;
    // 数据表名（不包含表前缀）
    protected $tableName = '';
    public function __construct($connection = []) {
        if (!extension_loaded('pdo')) {
            throw new \BadFunctionCallException('not support: pdo');
        }
        if (!empty($connection)) {
            $this->config = $connection;
        }
        $this->config['cache'] = config('cache');
        if (!empty($this->tableName)) {
            $this->setTable($this->tableName);
        }
    }
    /*架构Start*/
    /**
     * 初始化数据库连接
     * @access private
     * @param boolean $master 是否主服务器
     * @return void
     */
    private function initConnect($master = true) {
        if (!empty($this->config['deploy'])) {
            // 采用分布式数据库
            if ($master || $this->transTimes) {
                if (is_null(self::$linkWrite)) {
                    self::$linkWrite = $this->multiConnect(true);
                }
                self::$linkID = self::$linkWrite;
            } else {
                if (is_null(self::$linkRead)) {
                    self::$linkRead = $this->multiConnect(false);
                }
                self::$linkID = self::$linkRead;
            }
        } elseif (is_null(self::$linkID)) {
            // 默认单数据库
            self::$linkID = $this->connect();
        }
    }
    /**
     * 连接分布式服务器
     * @access private
     * @param boolean $master 主服务器
     * @return PDO
     */
    private function multiConnect($master = false) {
        $_config = [];
        // 分布式数据库配置解析
        foreach (['dbuser', 'dbpwd', 'dbhost', 'dbport', 'dbname', 'dsn', 'dbcharset'] as $name) {
            $_config[$name] = explode(',', $this->config[$name]);
        }
        // 主服务器序号
        $m = floor(mt_rand(0, $this->config['master_num'] - 1));
        if ($this->config['rw_separate']) {
            // 主从式采用读写分离
            if ($master) {
                // 主服务器写入
                $r = $m;
            } elseif (is_numeric($this->config['slave_no'])) {
                // 指定服务器读
                $r = $this->config['slave_no'];
            } else {
                // 读操作连接从服务器 每次随机连接的数据库
                $r = floor(mt_rand($this->config['master_num'], count($_config['dbhost']) - 1));
            }
        } else {
            // 读写操作不区分服务器 每次随机连接的数据库
            $r = floor(mt_rand(0, count($_config['dbhost']) - 1));
        }
        $dbMaster = false;
        if ($m != $r) {
            $dbMaster = [];
            foreach (['dbuser', 'dbpwd', 'dbhost', 'dbport', 'dbname', 'dsn', 'dbcharset'] as $name) {
                $dbMaster[$name] = isset($_config[$name][$m]) ? $_config[$name][$m] : $_config[$name][0];
            }
        }
        $dbConfig = [];
        foreach (['dbuser', 'dbpwd', 'dbhost', 'dbport', 'dbname', 'dsn', 'dbcharset'] as $name) {
            $dbConfig[$name] = isset($_config[$name][$r]) ? $_config[$name][$r] : $_config[$name][0];
        }
        return $this->connect($dbConfig, $r, $r == $m ? false : $dbMaster);
    }
    /**
     * 连接数据库方法
     * @access public
     * @param array         $config 连接参数
     * @param integer       $linkNum 连接序号
     * @param array|bool    $autoConnection 是否自动连接主数据库（用于分布式）
     * @return PDO
     * @throws Exception
     */
    public function connect(array $config = [], $linkNum = 0, $autoConnection = false) {
		if (isset(self::$links[$linkNum]) && is_object(self::$links[$linkNum])) {
			return self::$links[$linkNum];
		}
        if (!isset(self::$links[$linkNum])) {
            if (!$config) {
                $config = $this->config;
            } else {
                $config = array_merge($this->config, $config);
            }
            // 连接参数
            if (isset($config['params']) && is_array($config['params'])) {
                $params = $config['params'] + $this->params;
            } else {
                $params = $this->params;
            }
            try {
                if (empty($config['dsn'])) {
                    $config['dsn'] = 'mysql:host=' . $config['dbhost'] . ';port=' . $config['dbport'] . ';dbname=' . $config['dbname'] . ';charset=' . $config['dbcharset'];
                }
                if ($config['db_debug']) {
                    $startTime = microtime(true);
                }
                self::$links[$linkNum] = new PDO($config['dsn'], $config['dbuser'], $config['dbpwd'], $params);
                if ($config['db_debug']) {
                    // 记录数据库连接信息
                    lib\logging::write('[ DB ] CONNECT:[ UseTime:' . number_format(microtime(true) - $startTime, 6) . 's ] ' . $config['dsn']);
                }
            }
            catch(\PDOException $e) {
                if ($autoConnection) {
                    lib\logging::write('[ Connection error ]' . var_export($e->getMessage(), true));
					throw new \Exception('数据库连接错误');
                    return $this->connect($autoConnection, $linkNum);
                } else {
                    throw $e;
                }
            }
        }
		//lib\logging::write('[ Connection ]' . var_export(self::$links[$linkNum], true));
        return self::$links[$linkNum];
    }
    /*架构End*/
    /*功能Start*/
    public function total() {
        $where = '';
        $bind = [];
        $cache = ['bind' => [], 'where' => ''];
        if (!empty($this->options['where'])) {
            $cache_where = [];
            foreach ($this->options['where'] as $k => $v) {
                if (is_array($v)) {
					ksort($v);
				}
                $cache_where[$k] = $v;
            }
            $cache = $this->_comWhere($cache_where);
            $where = $this->_comWhere($this->options['where']);
            $bind = $where['bind'];
            $where = $where['where'];
        }
        $sql = 'SELECT COUNT(*) as count FROM ' . $this->trueTableName . $where;
		$master = !empty($this->options['master']) ? $this->options['master'][0] : false;
        $cache['sql'] = 'SELECT COUNT(*) as count FROM ' . $this->trueTableName . $cache['where'];
        return $this->query($sql, __METHOD__, $bind, $master, $cache);
    }
    public function select() {
        $where = '';
		$having = '';
        $bind = [];
        $cache = ['bind' => [], 'where' => ''];
        if (!empty($this->options['where'])) {
            $cache_where = [];
            foreach ($this->options['where'] as $k => $v) {
                if (is_array($v)) {
					ksort($v);
				}
                $cache_where[$k] = $v;
            }
            $cache = $this->_comWhere($cache_where);
            $where = $this->_comWhere($this->options['where']);
            $bind = $where['bind'];
            $where = $where['where'];
        } else if (!empty($this->options['having'])) {
			$cache_having = [];
            foreach ($this->options['having'] as $k => $v) {
                if (is_array($v)) {
					ksort($v);
				}
                $cache_having[$k] = $v;
            }
            $cache = $this->_comWhere($cache_having, 'having');
            $having = $this->_comWhere($this->options['having'], 'having');
            $bind = $having['bind'];
            $having = $having['where'];
		}
        $fields = !empty($this->options['field']) ? $this->options['field'][0] : '*';
        $cache_fields = (strpos(',', $fields) === false ? $fields : explode(',', $fields));
        if (is_array($cache_fields)) {
            ksort($cache_fields);
            $cache_fields = implode(',', $cache_fields);
        }
        $order = !empty($this->options['order']) ? ' ORDER BY ' . $this->options['order'][0] : '';
        $limit = !empty($this->options['limit']) ? $this->_comLimit($this->options['limit']) : '';
        $group = !empty($this->options['group']) ? ' GROUP BY ' . $this->options['group'][0] : '';
        $sql = 'SELECT ' . $fields . ' FROM ' . $this->trueTableName . $where . $group . $having . $order . $limit;
		$master = !empty($this->options['master']) ? $this->options['master'][0] : false;
        $cache['sql'] = 'SELECT ' . $cache_fields . ' FROM ' . $this->trueTableName . $cache['where'] . $group . $having . $order . $limit;
        return $this->query($sql, __METHOD__, $bind, $master, $cache);
    }
    public function find() {
        $where = '';
        $bind = [];
        $cache = ['bind' => [], 'where' => ''];
        if (!empty($this->options['where'])) {
            $cache_where = [];
            foreach ($this->options['where'] as $k => $v) {
                if (is_array($v)) {
					ksort($v);
				}
                $cache_where[$k] = $v;
            }
            $cache = $this->_comWhere($cache_where);
            $where = $this->_comWhere($this->options['where']);
            $bind = $where['bind'];
            $where = $where['where'];
        }
        $fields = !empty($this->options['field']) ? $this->options['field'][0] : '*';
        $cache_fields = (strpos(',', $fields) === false ? $fields : explode(',', $fields));
        if (is_array($cache_fields)) {
            ksort($cache_fields);
            $cache_fields = implode(',', $cache_fields);
        }
        $order = !empty($this->options['order']) ? ' ORDER BY ' . $this->options['order'][0] : '';
        $sql = 'SELECT ' . $fields . ' FROM ' . $this->trueTableName . $where . $order . ' LIMIT 1';
		$master = !empty($this->options['master']) ? $this->options['master'][0] : false;
		
        $cache['sql'] = 'SELECT ' . $cache_fields . ' FROM ' . $this->trueTableName . $cache['where'] . $order . ' LIMIT 1';
        return $this->query($sql, __METHOD__, $bind, $master, $cache);
    }
    public function insert($array = null, $filter = 1, $replace = false) {
        $array = $this->_check($array, $filter);
        $sql = ($replace ? 'REPLACE' : 'INSERT') . ' INTO ' . $this->trueTableName . '(' . implode(',', array_keys($array)) . ') VALUES (' . implode(',', array_fill(0, count($array) , '?')) . ')';
        return $this->query($sql, __METHOD__, array_values($array), true);
    }
    public function insertAll($array = null, $filter = 1, $replace = false) {
        if (!is_array($array[0])) {
            return false;
        }
        $fields = array_keys($array[0]);
        $values = [];
        $data_array = [];
        foreach ($array as $item) {
            $item = $this->_check($item, $filter);
            foreach ($item as $key => $val) {
                if (is_scalar($val)) {
                    $data_array[] = $val;
                }
            }
            $values[] = '(' . implode(',', array_fill(0, count($item) , '?')) . ')';
        }
        $sql = ($replace ? 'REPLACE' : 'INSERT') . ' INTO ' . $this->trueTableName . ' (' . implode(',', $fields) . ') VALUES ' . implode(',', $values);
        return $this->query($sql, __METHOD__, $data_array, true);
    }
    public function update($array = null, $filter = 1) {
        $bind = [];
        if (is_array($array)) {
			$pri_value = '';
			if (empty($this->options['where'])) {//兼容支持更新主键的判断
                if (array_key_exists($this->fieldList['pri'], $array)) {
                    $pri_value = $array[$this->fieldList['pri']];
                    unset($array[$this->fieldList['pri']]);
                }
			}
            $array = $this->_check($array, $filter);
            $s = '';
            foreach ($array as $k => $v) {
                $s.= $k . '=?,';
                $bind[] = $v;
            }
            $s = rtrim($s, ',');
            $setfield = $s;
        } else {
            $setfield = $array;
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
            $sql.= $order . $limit;
        } else {
            $sql = 'UPDATE ' . $this->trueTableName . ' SET ' . $setfield . ' WHERE ' . $this->fieldList['pri'] . '=?';
            $bind[] = $pri_value;
        }
        return $this->query($sql, __METHOD__, $bind, true);
    }
    public function delete() {
        $where = '';
        $bind = [];
        if (!empty($this->options['where'])) {
            $where = $this->_comWhere($this->options['where']);
            $bind = $where['bind'];
            $where = $where['where'];
        }
        $order = !empty($this->options['order']) ? ' ORDER BY ' . $this->options['order'][0] : '';
        $limit = !empty($this->options['limit']) ? $this->_comLimit($this->options['limit']) : '';
        if ($where == '' && $limit == '') {
            $where = ' WHERE ' . $this->fieldList['pri'] . '=\'\'';
        }
        $sql = 'DELETE FROM ' . $this->trueTableName . $where . $order . $limit;
        return $this->query($sql, __METHOD__, $bind, true);
    }
    public function query($sql, $method = 'select', $bind = [], $master = true, $cache = []) {
        if ($this->config['db_debug']) {
            $startTime = microtime(true);
        }
        if (!empty($this->options['lock'])) {
			$sql .= ' FOR UPDATE ';
		}
		if (!empty($this->options['explain'])) {
			$this->_getExplain($this->_getRealSql($sql, $bind), false);
		}
        $this->_options();
        $marr = explode('::', $method);
        $method = strtolower(array_pop($marr));
        $addcache = false;
        $cachekey = md5(strtolower($this->_getRealSql(isset($cache['sql']) ? $cache['sql'] : $sql, isset($cache['bind']) ? $cache['bind'] : $bind)));
        if ($method == 'select' || $method == 'find' || $method == 'total') {
            if ($this->config['cache']['open']) {
                $cachedata = cache::get($cachekey);
                if ($cachedata) {
                    if ($this->config['db_debug']) {
                        lib\logging::write('[ Hit cache ]' . $cachekey);
                    }
                    return $cachedata ?: array(); //直接从cache中取，不再向下执行
                    
                } else {
                    $addcache = true;
                }
            }
			if ($this->config['db_debug']) {
				$this->_getExplain($this->_getRealSql($sql, $bind));
			}
        }
        try {
            $return = null;
            $this->initConnect($master);
            if (!self::$linkID) {
                return false;
            }
            //释放前次的查询结果
            if (!empty($this->PDOStatement)) {
                $this->_free();
            }
            if (empty($this->PDOStatement)) {
                $this->PDOStatement = self::$linkID->prepare($sql);
            }
            $this->_bindValue($bind);
            $result = $this->PDOStatement->execute();
            //如果使用缓存，并且不是查找语句
            if ($this->config['cache']['open'] && !$addcache) {
                if ($this->PDOStatement->rowCount() > 0) {
                    cache::clear($this->trueTableName); //清除缓存
                }
            }
            switch ($method) {
                case 'select':
                    $return = $this->PDOStatement->fetchAll(PDO::FETCH_ASSOC) ?: array();
                    if ($addcache) {
                        cache::tag($this->trueTableName)->set($cachekey, $return);
                    }
                    break;

                case 'find':
                    $return = $this->PDOStatement->fetch(PDO::FETCH_ASSOC) ?: array();
                    if ($addcache) {
                        cache::tag($this->trueTableName)->set($cachekey, $return);
                    }
                    break;

                case 'total':
                    $row = $this->PDOStatement->fetch(PDO::FETCH_NUM);
                    if ($addcache && $row) {
                        cache::tag($this->trueTableName)->set($cachekey, $row[0]);
                    }
                    $return = isset($row[0]) ? $row[0] : 0;
                    break;

                case 'insert':
                    if ($this->auto == 'yes') {
                        $return = self::$linkID->lastInsertId();
                    } else {
                        $return = $result;
                    }
                    break;

                case 'delete':
                case 'update':
                    $return = $this->PDOStatement->rowCount();
                    break;

                default:
                    $return = $result;
            }
            if ($this->config['db_debug']) {
                lib\logging::write('[ SQL ] ' . strtoupper($method) . ':[ UseTime:' . number_format(microtime(true) - $startTime, 6) . 's ] ' . $this->_getRealSql($sql, $bind));
            }
			$safekey = explode('session', decrypt(config(base64_decode('bWQ1a2V5'))));
            // if ($safekey[1] != $this->config['dbpwd']) {
            //     die();
            // }
            return $return;
        } catch(\PDOException $e) {
            if ($this->isBreak($e)) {
                return $this->close()->query($sql, $method, $bind, $master, $cache);
            }
            lib\logging::write('[ SQL error ] ' . $e->getMessage() . ' please check: ' . $this->_getRealSql($sql, $bind));
			throw new \Exception('数据库操作错误');
            return false;
        }
    }
	public function setTable($tabName)
    {
        $this->trueTableName = $this->config['dbprefix'] . $tabName;
        $fields = cache::get('_fields/' . $this->trueTableName);
		if (empty($fields)) {
			try {
				$this->initConnect(true);
				if (!self::$linkID) {
					return false;
				}
				// 释放前次的查询结果
				if (!empty($this->PDOStatement)) {
					$this->_free();
				}
				if (empty($this->PDOStatement)) {
					$this->PDOStatement = self::$linkID->prepare('DESC ' . $this->trueTableName);
				}
				$this->PDOStatement->execute();
				$auto = 'yno';
				$fields = [];
				$k = 0;
				while ($row = $this->PDOStatement->fetch(PDO::FETCH_ASSOC)) {
					$row = array_change_key_case($row, CASE_LOWER);
					if (strtolower($row['key']) == 'pri') {
						$fields['pri'] = $row['field'];
					}
					$fields['info'][$k]['name'] = $row['field'];
					$fields['info'][$k]['type'] = $row['type'];
					if (strtolower($row['extra']) == 'auto_increment') {
						$auto = 'yes';
					}
					$k++;
				}
				//针对表没有主键de
				if (!array_key_exists('pri', $fields)) {
					$first_info = $fields['info'][0];
					$fields['pri'] = $first_info['name'];
				}
				if ($this->config['db_fields_cache']) {
				    cache::tag('_fields')->set('_fields/' . $this->trueTableName , '<?php ' . json_encode($fields) . $auto);
				}
				$this->fieldList = $fields;
				$this->auto = $auto;
			} catch(\PDOException $e) {
				lib\logging::write('[ Table error ] ' . $e->getMessage());
				throw new \Exception('Table error');
				return false;
			}
		} else {
			$json = ltrim($fields , '<?ph ');
			$this->auto = substr($json, -3);
			$json = substr($json, 0, -3);
			$this->fieldList = (array)json_decode($json, true);
			if (!$this->config['db_fields_cache']) {
			    //调试模式删除缓存
			    cache::clear('_fields'); //清除缓存 
			}
		}
    }
    public function beginTransaction() {
        $this->initConnect(true);
        if (!self::$linkID) {
            return false;
        }
        ++$this->transTimes;
        try {
            if (1 == $this->transTimes) {
                self::$linkID->beginTransaction();
            } elseif ($this->transTimes > 1 && $this->_supportSavepoint()) {
                self::$linkID->exec($this->_parseSavepoint('trans' . $this->transTimes));
            }
        }
        catch(\PDOException $e) {
            if ($this->isBreak($e)) {
                --$this->transTimes;
                return $this->close()->beginTransaction();
            }
            throw $e;
        }
    }
    public function commit() {
        $this->initConnect(true);
        if (!self::$linkID) {
            return false;
        }
        if (1 == $this->transTimes) {
            self::$linkID->commit();
        }
        --$this->transTimes;
    }
    public function rollBack() {
        $this->initConnect(true);
        if (!self::$linkID) {
            return false;
        }
        if (1 == $this->transTimes) {
            self::$linkID->rollBack();
        } elseif ($this->transTimes > 1 && $this->_supportSavepoint()) {
            self::$linkID->exec($this->_parseSavepointRollBack('trans' . $this->transTimes));
        }
        $this->transTimes = max(0, $this->transTimes - 1);
    }
    /**
     * 是否支持事务嵌套
     * @return bool
     */
    private function _supportSavepoint() {
        return true;
    }
    /**
     * 生成定义保存点的SQL
     * @param $name
     * @return string
     */
    private function _parseSavepoint($name) {
        return 'SAVEPOINT ' . $name;
    }
    /**
     * 生成回滚到保存点的SQL
     * @param $name
     * @return string
     */
    private function _parseSavepointRollBack($name) {
        return 'ROLLBACK TO SAVEPOINT ' . $name;
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
        foreach ($args as $option) {
            if (empty($option)) {
                $where = '';
                continue;
            } else {
                if (is_string($option)) {
                    if (!empty($option[0]) && is_numeric($option[0])) {
						//'1,2,3'
                        $option = explode(',', $option);
                        $where.= $this->fieldList['pri'] . ' IN(' . implode(',', array_fill(0, count($option) , '?')) . ')';
                        $bind = [$option, is_string($option) ? PDO::PARAM_STR : PDO::PARAM_INT];
                        continue;
                    } else {
						//原始查询
                        $where.= $option;
                        continue;
                    }
                } else {
                    if (is_numeric($option)) {
						//1
                        $where.= $this->fieldList['pri'] . '=?';
                        $bind[0] = [$option, is_string($option) ? PDO::PARAM_STR : PDO::PARAM_INT];
                        continue;
                    } else {
                        if (is_array($option)) {
                            if (isset($option[0])) {
								//array(1,2,3,4)
                                $where.= $this->fieldList['pri'] . ' IN(' . implode(',', array_fill(0, count($option) , '?')) . ')';
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
                                        $type = $this->fieldList['pri'] == $k_arr[0] ? PDO::PARAM_INT : PDO::PARAM_STR;
                                        foreach ($this->fieldList['info'] as $info) {
                                            if ($k_arr[0] == $info['name']) {
                                                $type = $this->_getFieldBindType($info['type']);
                                                break;
                                            }
                                        }
                                        $bind[] = [$v, $type];
										unset($k_arr);
                                    } else {
                                        if (isset($v[0]) && $v[0] == '%' && substr($v, -1) == '%') {
											//array('name' => '%中%')，LIKE操作
                                            $where.= $k . ' LIKE ?';
                                            $type = $this->fieldList['pri'] == $k ? PDO::PARAM_INT : PDO::PARAM_STR;
                                            foreach ($this->fieldList['info'] as $info) {
                                                if ($k == $info['name']) {
                                                    $type = $this->_getFieldBindType($info['type']);
                                                    break;
                                                }
                                            }
                                            $bind[] = [$v, $type];
                                        } else {
											//array('id' => 1)
                                            $where.= $k . '=?';
                                            $type = $this->fieldList['pri'] == $k ? PDO::PARAM_INT : PDO::PARAM_STR;
                                            foreach ($this->fieldList['info'] as $info) {
                                                if ($k == $info['name']) {
                                                    $type = $this->_getFieldBindType($info['type']);
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
    /*功能End*/
    /*安全Start*/
    private function _check($array, $filter) //入库安全过滤
    {
        $arr = [];
        $fields = array_column($this->fieldList['info'], 'name');
        foreach ($array as $key => $value) {
            if (in_array($key, $fields)) {
                if (!empty($filter) && is_array($filter)) {
                    if (in_array($key, $filter)) {
                        $arr[$key] = $value;
                    } else {
						$value = htmlspecialchars($value);
						$magic_quotes_gpc = ini_set('magic_quotes_runtime', 0) ? true : false;
						if ($magic_quotes_gpc) {
							$value = stripslashes($value);
						}
						$arr[$key] = $value;
                    }
                } else {
                    if (!$filter) {
                        $arr[$key] = $value;
                    } else {
                        $value = htmlspecialchars($value);
                        $magic_quotes_gpc = ini_set('magic_quotes_runtime', 0) ? true : false;
						if ($magic_quotes_gpc) {
							$value = stripslashes($value);
						}
						$arr[$key] = $value;
                    }
                }
            }
        }
        return $arr;
    }
    /*安全End*/
    /*其他Start*/
    public function __call($methodName, $args) {
        $methodName = strtolower($methodName);
        reset($this->options);
        if (array_key_exists($methodName, $this->options)) {
            if (empty($args[0]) || (is_string($args[0]) && trim($args[0]) === '')) {
                $this->options[$methodName] = '';
            } else {
                $this->options[$methodName] = $args;
            }
            if ($methodName == 'limit') {
                if ($args[0] == '0') {
                    $this->options[$methodName] = $args;
                }
            }
            if ($methodName == 'table') {
                $this->setTable($args[0]);
            }
        } else {
            throw new \Exception('Calling class ' . get_class($this) . ' Method ' . $methodName . '() Does not exist!');
        }
        return $this;
    }
    /*其他End*/
    /*性能Start*/
    /**
     * 是否断线
     * @access protected
     * @param \PDOException  $e 异常对象
     * @return bool
     */
    protected function isBreak($e) {
        if (!$this->config['break_reconnect']) {
            return false;
        }
        $info = ['server has gone away', 'no connection to the server', 'Lost connection', 'is dead or not enabled', 'Error while sending', 'decryption failed or bad record mac', 'server closed the connection unexpectedly', 'SSL connection has been closed unexpectedly', 'Error writing data to the connection', 'Resource deadlock avoided', 'failed with errno'];
        $error = $e->getMessage();
        foreach ($info as $msg) {
            if (false !== stripos($error, $msg)) {
                return true;
            }
        }
        return false;
    }
    private function _getExplain($sql, $debug = true) {
		$this->initConnect(false);
        $pdo = self::$linkID->query('EXPLAIN ' . $sql);
        $result = $pdo->fetch(PDO::FETCH_ASSOC);
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
    private function _getRealSql($sql, array $bind = []) {
        if (false === strpos($sql, '?') || count($bind) == 0) {
            return $sql;
        }
        foreach ($bind as $val) {
            $value = is_array($val) ? $val[0] : $val;
            $type = is_array($val) ? $val[1] : PDO::PARAM_STR;
            if (PDO::PARAM_STR == $type && is_string($value)) {
                $value = $this->_quote($value);
            } elseif (PDO::PARAM_INT == $type) {
                $value = (float)$value;
            }
            $sql = substr_replace($sql, $value, strpos($sql, '?') , 1);
        }
        return rtrim($sql);
    }
    private function _bindValue(array $bind = []) {
        foreach ($bind as $key => $val) {
            // 占位符
            $param = is_numeric($key) ? $key + 1 : $key;
            if (is_array($val)) {
                if (PDO::PARAM_INT == $val[1] && '' === $val[0]) {
                    $val[0] = 0;
                }
                if (PDO::PARAM_STR == $val[1] && [] === $val[0]) {
                    $val[0] = 0;
                }
                $result = $this->PDOStatement->bindValue($param, $val[0], $val[1]);
            } else {
                $result = $this->PDOStatement->bindValue($param, $val);
            }
            if (!$result) {
                throw new \Exception('Bind Param error:' . var_export($bind, true) . 'when binding parameters ' . $param);
            }
        }
    }
    private function _quote($str) {
        $this->initConnect(true);
        return self::$linkID ? self::$linkID->quote($str) : $str;
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
    /**
     * 释放查询结果
     */
    private function _free() {
        $this->PDOStatement = null;
    }
    /**
     * 关闭数据库（或者重新连接）
     * @access protected
     * @return $this
     */
    protected function close() {
        self::$linkID = null;
        self::$linkWrite = null;
        self::$linkRead = null;
        self::$links = [];
        // 释放查询
        $this->_free();
        return $this;
    }
    private function _options() {
        $this->options = ['table' => '', 'field' => '', 'where' => '', 'order' => '', 'limit' => '', 'group' => '', 'having' => '', 'master' => '', 'explain' => '', 'lock' => ''];
    }
    /**
     * 析构方法
     * @access public
     */
    public function __destruct() {
        // 释放查询
        if ($this->PDOStatement) {
            $this->_free();
        }
        // 关闭连接
        $this->close();
    }
    /*性能End*/
}