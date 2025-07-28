<?php
namespace model;
use base;
class shop_waybill extends base\model
{
    protected $tableName = 'fxy_shop_waybill';
	public function getList($condition = array(), $field = '*', $order = '', $page = null, $get_p = null){
		if($page && $get_p){
			$total = $this->where($condition)->total();
			$totalpage = ceil($total / $page);//总计页数
			$limitpage = ($get_p - 1) * $page;//每次查询取记录
			$list = $this->where($condition)->field($field)->order($order)->limit($limitpage, $page)->select();
		}else{
			$totalpage = 0;
			$list = $this->where($condition)->field($field)->order($order)->select();
		}
		return array('list' => $list, 'totalpage' => $totalpage);
	}
	public function getInfo($condition = array(), $field = '*')
	{
		$result = $this->where($condition)->field($field)->find();
		return $result;
	}
	public function add($data)
	{
		$result = $this->insert($data);
		return $result;
	}
	public function edit($condition = array(), $data = array())
	{
		$result = $this->where($condition)->update($data);
		return $result;
	}
	public function del($condition)
	{
		$this->where($condition)->delete();
	}
	
	/**
     * 根据订单信息获取打印数据
     * @param array $order_info 需要包括扩展数据
     */
    public function getPrintInfoByOrderInfo($order_info)
    {
        $model_daddress = model('shop_daddress');
        //获取打印数据
        $print_info = array();
        $daddress_id = $order_info['extend_order_common']['daddress_id'];
        $daddress_info = array();
        if (!empty($daddress_id)) {
            $daddress_info = $model_daddress->getInfo(array('address_id' => $daddress_id));
        }
        $reciver_info = $order_info['extend_order_common']['reciver_info'];
        $print_info['buyer_name'] = $order_info['extend_order_common']['reciver_name'];
        $print_info['buyer_area'] = $reciver_info['area'];
        $print_info['buyer_address'] = $reciver_info['street'];
        $print_info['buyer_mobile'] = $reciver_info['tel_phone'];
        $print_info['buyer_phone'] = $reciver_info['tel_phone'];
        $print_info['seller_name'] = isset($daddress_info['seller_name']) ? $daddress_info['seller_name'] : '';
        $print_info['seller_area'] = isset($daddress_info['area_info']) ? $daddress_info['area_info'] : '';
        $print_info['seller_address'] = isset($daddress_info['address']) ? $daddress_info['address'] : '';
        $print_info['seller_phone'] = isset($daddress_info['telphone']) ? $daddress_info['telphone'] : '';
        $print_info['seller_company'] = isset($daddress_info['company']) ? $daddress_info['company'] : '';
        return $print_info;
    }
}