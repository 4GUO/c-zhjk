<?php
namespace model;
use base;
class cms_model_field extends base\model
{
    protected $tableName = 'cms_model_field';
    public function getList($condition = array(), $field = '*', $order = '', $page = null, $get_p = null) {
		if ($page && $get_p) {
			$total = $this->where($condition)->total();
			$totalpage = ceil($total / $page);//总计页数
			$limitpage = ($get_p - 1) * $page;//每次查询取记录
			$list = $this->where($condition)->field($field)->order($order)->limit($limitpage, $page)->select();
			$hasmore = $total > $get_p * $page ? true : false;
		} else {
			$totalpage = 0;
			$list = $this->where($condition)->field($field)->order($order)->select();
			$hasmore = false;
		}
		return array('list' => $list, 'totalpage' => $totalpage, 'hasmore' => $hasmore);
	}
	//添加字段
    public function addField($data = null) {
        $data['name'] = strtolower($data['name']);
        $data['ifsystem'] = isset($data['ifsystem']) ? intval($data['ifsystem']) : 0;
        //模型id
        $modelid = $data['modelid'];
        //完整表名获取 判断主表 还是副表
        $tablename = $this->getModelTableName($modelid, $data['ifsystem']);
        if (!$this->table_exists($tablename)) {
            throw new \Exception('数据表不存在！');
        }
        $tablename = config('db.dbprefix') . $tablename;
        //判断字段名唯一性
        if ($this->where(array('name' => $data['name'], 'modelid' => $modelid))->find()) {
            throw new \Exception('字段`' . $data['name'] . '`已经存在');
        }

        $data['isadd'] = isset($data['isadd']) ? intval($data['isadd']) : 0;
        $data['ifrequire'] = isset($data['ifrequire']) ? intval($data['ifrequire']) : 0;
        if ($data['ifrequire'] && !$data['isadd']) {
            throw new \Exception('必填字段不可以隐藏！');
        }

        if ($data['setting']['value'] === '') {
            if (strstr(strtolower($data['setting']['define']), 'int') || strstr(strtolower($data['setting']['define']), 'decimal')) {
                $default = ' DEFAULT \'0\'';
            } elseif (strstr(strtolower($data['setting']['define']), 'text') || strstr(strtolower($data['setting']['define']), 'blob')) {
				$default = '';
			} else {
                $default = ' DEFAULT \'\'';
            }
        } elseif (strstr(strtolower($data['setting']['define']), 'text') || strstr(strtolower($data['setting']['define']), 'blob')) {
            $default = '';
        } else {
            $default = ' DEFAULT \'' . $data['setting']['value'] . '\'';
        }

        //先将字段存在设置的主表或附表里面 再将数据存入ModelField
        $sql = <<<EOF
            ALTER TABLE `{$tablename}`
            ADD COLUMN `{$data['name']}` {$data['setting']['define']} {$default} COMMENT '{$data['title']}';
EOF;
        $this->query($sql, 'alter');
        $fieldInfo = model('cms_field_type')->where(array('name' => $data['type']))->field('ifoption,ifstring')->find();
        //只有主表文本类字段才可支持搜索
        $data['ifsearch'] = isset($data['ifsearch']) ? ($fieldInfo['ifstring'] && $data['ifsystem'] ? intval($data['ifsearch']) : 0) : 0;
        $data['status'] = isset($data['status']) ? intval($data['status']) : 0;
        $data['iffixed'] = 0;
        $data['setting']['options'] = $fieldInfo['ifoption'] ? $data['setting']['options'] : '';
        //附加属性值
        $data['setting'] = serialize($data['setting']);
        $fieldid = $this->insert($data);
        if ($fieldid) {
            return true;
        } else {
            //回滚
            $this->query('ALTER TABLE  `' . $tablename . '` DROP  `' . $data['name'] . '`', 'alert');
            throw new \Exception('字段信息入库失败！');
        }
        return true;
    }
    /**
     *  编辑字段
     * @param type $data 编辑字段数据
     * @param type $fieldid 字段id
     * @return boolean
     */
    public function editField($data, $fieldid = 0) {
        $data['name'] = strtolower($data['name']);
        $data['ifsystem'] = isset($data['ifsystem']) ? intval($data['ifsystem']) : 0;
        if (!$fieldid && !isset($data['fieldid'])) {
            throw new \Exception('缺少字段id！');
        } else {
            $fieldid = $fieldid ? $fieldid : (int) $data['fieldid'];
        }
        //原字段信息
        $info = $this->where(array('id' => $fieldid))->find();
        if (empty($info)) {
            throw new \Exception('该字段不存在！');
        }
        //模型id
        $data['modelid'] = $modelid = $info['modelid'];
        //完整表名获取 判断主表 还是副表
        $tablename = $this->getModelTableName($modelid, $data['ifsystem']);
        if (!$this->table_exists($tablename)) {
            throw new \Exception('数据表不存在！');
        }
        $tablename = config('db.dbprefix') . $tablename;
        //判断字段名唯一性
        if ($this->where(array('name' => $data['name'], 'modelid' => $modelid, 'id !=' => $fieldid))->find()) {
            throw new \Exception('字段`' . $data['name'] . '`已经存在');
        }
        $data['isadd'] = isset($data['isadd']) ? intval($data['isadd']) : 0;
        $data['ifrequire'] = isset($data['ifrequire']) ? intval($data['ifrequire']) : 0;
        if ($data['ifrequire'] && !$data['isadd']) {
            throw new \Exception('必填字段不可以隐藏！');
        }

        if ($data['setting']['value'] === '') {
            if (strstr(strtolower($data['setting']['define']), 'int') || strstr(strtolower($data['setting']['define']), 'decimal')) {
                $default = ' DEFAULT \'0\'';
                $data['setting']['value'] = 0;
            } elseif (strstr(strtolower($data['setting']['define']), 'text') || strstr(strtolower($data['setting']['define']), 'blob')) {
				$default = '';
			} else {
                $default = ' DEFAULT \'\'';
            }
        } elseif (strstr(strtolower($data['setting']['define']), 'text') || strstr(strtolower($data['setting']['define']), 'blob')) {
            $default = '';
        } else {
            $default = ' DEFAULT \'' . $data['setting']['value'] . '\'';
        }
        $sql = <<<EOF
            ALTER TABLE `{$tablename}`
            CHANGE COLUMN `{$info['name']}` `{$data['name']}` {$data['setting']['define']} {$default} COMMENT '{$data['title']}';
EOF;
        try {
            $this->query($sql, 'alter');
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        $fieldInfo = model('cms_field_type')->where(array('name' => $data['type']))->field('ifoption,ifstring')->find();
        //只有主表文本类字段才可支持搜索
        $data['ifsearch'] = isset($data['ifsearch']) ? ($fieldInfo['ifstring'] && $data['ifsystem'] ? intval($data['ifsearch']) : 0) : 0;
        $data['status'] = isset($data['status']) ? intval($data['status']) : 0;
        //$data['options'] = $fieldInfo['ifoption'] ? $data['options'] : '';
        $data['setting']['options'] = $fieldInfo['ifoption'] ? $data['setting']['options'] : '';
        //附加属性值
        $data['setting'] = serialize($data['setting']);
        $this->where(array('id' => $fieldid))->update($data);
        return true;
    }

    /**
     * 删除字段
     * @param type $fieldid 字段id
     * @return boolean
     */
    public function deleteField($fieldid) {

        //原字段信息
        $info = self::where(array('id' => $fieldid))->find();
        if (empty($info)) {
            throw new \Exception('该字段不存在！');
        }
        //模型id
        $modelid = $info['modelid'];
        //完整表名获取 判断主表 还是副表
        $tablename = $this->getModelTableName($modelid, $info['ifsystem']);
        if (!$this->table_exists($tablename)) {
            throw new \Exception('数据表不存在！');
        }
        $tablename = config('db.dbprefix') . $tablename;
        //判断是否允许删除
        $sql = <<<EOF
            ALTER TABLE `{$tablename}`
            DROP COLUMN `{$info['name']}`;
EOF;
        $this->query($sql, 'alter');
        $this->where(array('id' => $fieldid))->delete();
        return true;
    }
    /**
     * 根据模型ID，返回表名
     * @param type $modelid
     * @param type $modelid
     * @return string
     */
    public function getModelTableName($modelid, $ifsystem = 1) {
        //表名获取
        $info = model('cms_model')->where(array('id' => $modelid))->find();
        $model_table = $info['tablename'];
        //完整表名获取 判断主表 还是副表
        $tablename = $ifsystem ? $model_table : $model_table . '_data';
        return $tablename;
    }
    /**
     * 检查表是否存在
     * $table 不带表前缀
     */
    public function table_exists($table) {
        $table = config('db.dbprefix') . strtolower($table);
        if (true == $this->query('SHOW TABLES LIKE \'' . $table . '\'', 'find')) {
            return true;
        } else {
            return false;
        }
    }
}