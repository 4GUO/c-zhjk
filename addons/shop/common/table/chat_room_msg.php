<?php
namespace table;
use base;
class chat_room_msg extends base\db {
    protected $tableName = 'chat_room_msg';
	private function getRule() {
        return [
            'table_name' => 'chat_room_msg',
            'type' => 'mod', # 分表方式
            'num' => 8     # 分表数量
        ];
    }
    #获取插入ID，根据ID来取模分表
    public function getLastId() {
        return $this->table('chat_room_msg_lastinsertid')->insert(array('add_time' => time()));
    }
    public function add($data) {
        try {
			$this->beginTransaction();
			$id = $this->getLastId();
			$data['id'] = $id;
    		$this->partition(['id' => $id], 'id', $this->getRule())->insert($data);
            $this->table('chat_room_msg_counter')->where(array('room_id' => $data['room_id']))->update('records=records+1');
    		$this->commit();
		} catch (\Exception $e) {
			$this->rollBack();
			\lib\logging::write(var_export($e->getMessage(), true));
			return callback(false, $e->getMessage());
	    }
        return callback(true, '', array('id' => $id));
	}
	public function getInfo($condition, $field, $order, $id) {
	    return $this->partition(['id' => $id], 'id', $this->getRule())->where($condition)->field($field)->order($order)->find();
    }
    public function edit($condition, $data, $id) {
		$result = $this->partition(['id' => $id], 'id', $this->getRule())->where($condition)->update($data);
		return $result;
	}
	public function del($condition, $id) {
	    try {
	        $this->beginTransaction();
		    $this->partition(['id' => $id], 'id', $this->getRule())->where($condition)->delete();
		    $this->table('chat_room_msg_counter')->where(array('room_id' => $condition['room_id']))->update('records=records-1');
		    $this->commit();
	    } catch (\Exception $e) {
			$this->rollBack();
			\lib\logging::write(var_export($e->getMessage(), true));
			return callback(false, $e->getMessage());
		}
		return callback(true, '', array('id' => $id));
	}
    /**

     * 构造获取总记录数及主键ID的sql⼦查询语句
    
     * @param $table 主表名称
    
     * @param $idKey 主键id字段名称
    
     * @param string $fields 其它字段名称，多个字段⽤英⽂逗号分隔
    
     * @param string $where 查询条件
    
     * @return array
    
    */
    
    public function buildPartitionSql($table, $idKey, $fields = null, $where = null) {
        $truetable = strpos($table, config('db.dbprefix')) === false ? (config('db.dbprefix') . $table) : $table;
        $rule = $this->getRule();
        $countTable = [];
        $listTable = [];
        $fieldList = [$idKey];
        if ($fields) {
            $fieldList = array_merge($fieldList, explode(',', $fields));
            $fieldList = array_unique($fieldList);
        }
        $fieldStr = implode(',', $fieldList);
        if (empty($where)) {
            $where = '1=1';
        }
        for ($i = 0; $i < $rule['num']; $i++) {
            $countTable[] = sprintf('SELECT %s FROM %s_%s WHERE %s', $idKey, $truetable, ($i + 1), $where); 
            $listTable[] = sprintf('SELECT %s FROM %s_%s WHERE %s', $fieldStr, $truetable, ($i + 1), $where); 
        }
        $otherStr = ' ';
        $countTable = '( ' . implode(' UNION ', $countTable) . ') AS ' . $table . $otherStr;
        $listTable = '( ' . implode(' UNION ', $listTable) . ') AS ' . $table . $otherStr;
        $tables = ['countSql' => $countTable, 'listSql' => $listTable];
        return $tables;
    }
    /**

     * 构造获取主键ID的sql⼦查询语句 性能更牛逼
    
     * @param $table 主表名称
    
     * @param $idKey 主键id字段名称
    
     * @param string $fields 其它字段名称，多个字段⽤英⽂逗号分隔
    
     * @param string $where 查询条件
    
     * @return array
    
    */
    
    public function buildPartitionSqlNew($table, $idKey, $fields = null, $where = null, $limitpage = null, $page = null) {
        $truetable = strpos($table, config('db.dbprefix')) === false ? (config('db.dbprefix') . $table) : $table;
        $rule = $this->getRule();
        $listTable = [];
        $fieldList = [$idKey];
        if ($fields) {
            $fieldList = array_merge($fieldList, explode(',', $fields));
            $fieldList = array_unique($fieldList);
        }
        $fieldStr = implode(',', $fieldList);
        if (empty($where)) {
            $where = '1=1';
        }
        for ($i = 0; $i < $rule['num']; $i++) {
            if ($i == 0) {
                $listTable[] = sprintf('(SELECT %s FROM %s_%s WHERE %s ORDER BY %s DESC LIMIT %s,%s) AS %s_%s', $fieldStr, $truetable, ($i + 1), $where, $idKey, $limitpage, $page, $table, ($i + 1)); 
            } else {
                $listTable[] = sprintf('(SELECT %s FROM %s_%s WHERE %s ORDER BY %s DESC LIMIT %s,%s)', $fieldStr, $truetable, ($i + 1), $where, $idKey, $limitpage, $page);  
            }
            
        }
        $otherStr = ' ';
        $listTable = implode(' UNION ALL ', $listTable) . $otherStr;
        $tables = ['listSql' => $listTable];
        return $tables;
    }
    # $subwhere 思想可以学学，整体性能不如buildPartitionSqlNew方法
    public function buildPartitionSqlBom($table, $idKey, $fields = null, $where = null, $limitpage = null, $page = null) {
        $truetable = strpos($table, config('db.dbprefix')) === false ? (config('db.dbprefix') . $table) : $table;
        $rule = $this->getRule();
        $listTable = [];
        $fieldList = [$idKey];
        if ($fields) {
            $fieldList = array_merge($fieldList, explode(',', $fields));
            $fieldList = array_unique($fieldList);
        }
        $fieldStr = implode(',', $fieldList);
        if (empty($where)) {
            $where = '1=1';
        }
        for ($i = 0; $i < $rule['num']; $i++) {
            if ($i == 0) {
                $subwhere = sprintf('(select %s from %s_%s where %s order by %s desc limit %s,1)', $fieldStr, $truetable, ($i + 1), $where, $idKey, $page);
                $listTable[] = sprintf('(SELECT %s FROM %s_%s WHERE %s AND %s > %s ORDER BY %s) AS %s_%s', $fieldStr, $truetable, ($i + 1), $where, $idKey, $subwhere, $idKey, $table, ($i + 1));
            } else {
                $subwhere = sprintf('(select %s from %s_%s where %s order by %s desc limit %s,1)', $fieldStr, $truetable, ($i + 1), $where, $idKey, $page);
                $listTable[] = sprintf('(SELECT %s FROM %s_%s WHERE %s AND %s > %s ORDER BY %s DESC)', $fieldStr, $truetable, ($i + 1), $where, $idKey, $subwhere, $idKey);
            }
            
        }
        $otherStr = ' ';
        $listTable = implode(' UNION ', $listTable) . $otherStr;
        $tables = ['listSql' => $listTable];
        return $tables;
    }
    /**
    
     * 构造获取指定id对应记录的sql⼦查询语句
    
     * @param $table 主表名称
    
     * @param $idKey 指定的id字段名称
    
     * @param $idValues 指定的id字段值
    
     * @param string $fields 字段
    
     * @return string
    
    */
    
    public function buildPartitionListSqlById($table, $idKey, $idValues, $fields = '*') {
        $truetable = strpos($table, config('db.dbprefix')) === false ? (config('db.dbprefix') . $table) : $table;
        $rule = $this->getRule();
        $sql = '';
        $ids = is_array($idValues) ? implode(',', $idValues) : $idValues;
        if ($ids !== '') {
            $listTable = [];
            for ($i = 0; $i < $rule['num']; $i++) {
                $listTable[] = sprintf('SELECT %s FROM %s_%s WHERE %s IN (%s)', $fields, $truetable, ($i + 1), $idKey, $ids); 
            }
            $sql = '( ' . implode(' UNION ', $listTable) . ') AS ' . $table;
        }
        return $sql;
    }
}