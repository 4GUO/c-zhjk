<?php
namespace model;
use base;
class cms_model extends base\model
{
    protected $tableName = 'cms_model';
    protected $ext_table = '_data';
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
    /**
     * 创建模型
     * @param type $data 提交数据
     * @return boolean
     */
    public function addModelFormguide($data = array(), $module = 'formguide') {
        if (empty($data)) {
            throw new \Exception('数据不得为空！');
        }
        $data['tablename'] = $data['tablename'] ? 'form_' . $data['tablename'] : '';
        $data['module'] = $module;
        $data['setting'] = serialize($data['setting']);
        //添加模型记录
        if ($result = $this->insert($data)) {
            //创建模型表
            $sql = 'CREATE TABLE IF NOT EXISTS `think_form_table` (
                `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
                `uid` mediumint(8) unsigned NOT NULL DEFAULT 0,
                `username` varchar(20) NOT NULL DEFAULT \'\',
                `inputtime` int(10) unsigned NOT NULL DEFAULT 0,
                `src` varchar(200) NOT NULL DEFAULT \'\',
                `ip` char(15) NOT NULL DEFAULT \'\',
                `remark` text COMMENT \'备注\',
                `status` tinyint(1) unsigned NOT NULL DEFAULT \'2\' COMMENT \'状态，0为异常，1为正常，默认为2\',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
            //表名替换
            $sql = str_replace('think_form_table', config('db.dbprefix') . $data['tablename'], $sql);
            $this->query($sql, 'create');
        }
        return $result;
    }
    //处理post提交的模型数据
    public function dealModelPostData($modeId, $data, $dataExt = []) {
        //字段类型
        $where['modelid'] = $modeId;
        $where['status'] = 1;
        $filedTypeList = model('cms_model_field')->where($where)->order('listorder,id')->field('name,title,type,ifsystem,ifrequire,ifonly,pattern,errortips')->select();

        foreach ($filedTypeList as $vo) {
            $name = $vo['name'];
            $arr = $vo['ifsystem'] ? 'data' : 'dataExt';
            if (!isset(${$arr}[$name])) {
                switch ($vo['type']) {
                    // 开关
                    case 'switch':
                        ${$arr}[$name] = 0;
                        break;
                    case 'checkbox':
                        ${$arr}[$name] = '';
                        break;
                }
            } else {
                if (is_array(${$arr}[$name])) {
                    ${$arr}[$name] = implode(',', ${$arr}[$name]);
                }
                switch ($vo['type']) {
                    // 开关
                    case 'switch':
                        ${$arr}[$name] = 1;
                        break;
                    // 日期+时间
                    case 'datetime':
                        ${$arr}[$name] = strtotime(${$arr}[$name]);
                        break;
                    // 日期
                    case 'date':
                        ${$arr}[$name] = strtotime(${$arr}[$name]);
                        break;
                    // 编辑器
                    case 'markdown':
                    case 'Ueditor':
                        ${$arr}[$name] = htmlspecialchars(stripslashes(${$arr}[$name]));
                        break;
                }
            }
            //数据必填验证
            if ($vo['ifrequire'] && isset(${$arr}[$name]) && ${$arr}[$name] == '') {
                throw new \Exception('\'' . $vo['title'] . '\'必须填写~');
            }
            //唯一验证
            if ($vo['ifonly'] && !empty(${$arr}[$name])) {
                $tableName = model('cms_model_field')->getModelTableName($modeId);
                $model = model()->table($tableName);
                if ($model->where(array($name => ${$arr}[$name]))->total()) {
                    throw new \Exception($vo['title'] . ':' . ${$arr}[$name] . '已经存在~');
                }
            }
            //正则校验
            if (isset(${$arr}[$name]) && ${$arr}[$name] && $vo['pattern'] && !preg_match($vo['pattern'], ${$arr}[$name])) {
                throw new \Exception('\'' . $vo['title'] . '\'' . (!empty($vo['errortips']) ? $vo['errortips'] : '正则校验失败'));
            }
            //数据格式验证
            if (!empty(${$arr}[$name]) && in_array($vo['type'], ['number']) && !is_numeric(${$arr}[$name])) {
                throw new \Exception('\'' . $vo['title'] . '\'格式错误~');
                //安全过滤
            } else {

            }
        }
        return [$data, $dataExt];
    }
    
    //查询解析模型数据用以构造from表单
    public function getFieldList($modelId, $id = null) {
        $list = model('cms_model_field')->where(array('modelid' => $modelId, 'status' => 1))->order('listorder DESC')->field('name,title,remark,type,isadd,isindex,iscore,ifsystem,ifrequire,setting')->select();
        if (!empty($list)) {
            //编辑信息时查询出已有信息
            if ($id) {
                $modelInfo = model('cms_model')->where(array('id' => $modelId))->field('tablename,type')->find();
                $dataInfo  = $this->table($modelInfo['tablename'])->where(array('id' => $id))->find();
                //查询附表信息
                if ($modelInfo['type'] == 2 && !empty($dataInfo)) {
                    $dataInfoExt = $this->table($modelInfo['tablename'] . $this->ext_table)->where(array('did' => $dataInfo['id']))->find();
                }
            }
            foreach ($list as $key => &$value) {
                //内部字段不显示
                if ($value['iscore']) {
                    unset($list[$key]);
                }
                //核心字段做标记
                if ($value['ifsystem']) {
                    $value['fieldArr'] = 'modelField';
                    if (isset($dataInfo[$value['name']])) {
                        $value['value'] = $dataInfo[$value['name']];
                    }
                } else {
                    $value['fieldArr'] = 'modelFieldExt';
                    if (isset($dataInfoExt[$value['name']])) {
                        $value['value'] = $dataInfoExt[$value['name']];
                    }
                }

                //扩展配置
                $value['setting'] = fxy_unserialize($value['setting']);
                $value['options'] = $value['setting']['options'] ?? '';
                //在新增时候添加默认值
                if (!$id) {
                    $value['value'] = $value['setting']['value'] ?? '';
                }
                if ($value['type'] == 'custom') {
                    if ($value['options'] != '') {
                        $tpar = explode('.', $value['options'], 2);
                        $value['options'] = \think\Response::create('admin@custom/' . $tpar[0], 'view')->assign('vo', $value)->getContent();
                        unset($tpar);
                    }
                } elseif ($value['options'] != '') {
                    $value['options'] = $this->parse_attr($value['options']);
                }
                if ($value['type'] == 'checkbox') {
                    $value['value'] = empty($value['value']) ? [] : explode(',', $value['value']);
                }
                if ($value['type'] == 'datetime') {
                    $value['value'] = empty($value['value']) ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s', $value['value']);
                }
                if ($value['type'] == 'date') {
                    $value['value'] = empty($value['value']) ? '' : date('Y-m-d', $value['value']);
                }
                if ($value['type'] == 'Ueditor' || $value['type'] == 'markdown') {
                    $value['value'] = isset($value['value']) ? htmlspecialchars_decode($value['value']) : '';
                }
            }
        }
        return $list;
    }
    /**
     * 解析配置
     * @param string $value 配置值
     * @return array|string
     */
    public function parse_attr($value = '') {
        $array = preg_split('/[,;\r\n]+/', trim($value, ',;' . PHP_EOL));
        if (strpos($value, ':')) {
            $value = array();
            foreach ($array as $val) {
                list($k, $v) = explode(':', $val);
                $value[$k]   = $v;
            }
        } else {
            $value = $array;
        }
        return $value;
    }
}