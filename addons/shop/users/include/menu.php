<?php
defined('SAFE_CONST') or exit('Access Invalid!');
$menu_list = array(
	'goods' => array(
		'icon' => 'shangpin',
		'size' => '16px',
        'name' => '商品',
        'child' => array(
		    array(
                'name' => '商品分类',
                'act' => 'shop_goods_class',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '添加',
						'act' => 'shop_goods_class',
						'op' => 'add'
					),
					array(
						'name' => '编辑',
						'act' => 'shop_goods_class',
						'op' => 'edit'
					),
					array(
						'name' => '删除',
						'act' => 'shop_goods_class',
						'op' => 'del'
					),
				),
            ) ,
			array(
                'name' => '商品管理',
                'act' => 'shop_goods',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '上架',
						'act' => 'shop_goods',
						'op' => 'check'
					),
					array(
						'name' => '下架',
						'act' => 'shop_goods',
						'op' => 'uncheck'
					),
					array(
						'name' => '操作',
						'act' => 'shop_goods',
						'op' => 'publish'
					),
					array(
						'name' => '编辑图片',
						'act' => 'shop_goods',
						'op' => 'edit_images'
					),
					array(
						'name' => '删除',
						'act' => 'shop_goods',
						'op' => 'del'
					),
				),
            ),
			array(
                'name' => '商品规格',
                'act' => 'shop_spec',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '添加',
						'act' => 'shop_spec',
						'op' => 'add'
					),
					array(
						'name' => '编辑',
						'act' => 'shop_spec',
						'op' => 'edit'
					),
					array(
						'name' => '删除',
						'act' => 'shop_spec',
						'op' => 'del'
					),
					array(
						'name' => '属性值',
						'act' => 'shop_spec',
						'op' => 'ajax'
					),
					array(
						'name' => '产品属性',
						'act' => 'shop_spec',
						'op' => 'goods_spec'
					),
				),
            ) ,
			array(
                'name' => '商品设置',
                'act' => 'shop_goods',
                'op' => 'config',
            ),
			array(
                'name' => '套餐管理',
                'act' => 'shop_goods_taocan',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '保存',
						'act' => 'shop_goods_taocan',
						'op' => 'publish'
					),
					array(
						'name' => '删除',
						'act' => 'shop_goods_taocan',
						'op' => 'del'
					),
				),
            ) ,
        )
    ) ,
	'member' => array(
		'icon' => 'huiyuan',
		'size' => '16px',
        'name' => '会员',
        'child' => array(
			array(
                'name' => '会员级别',
                'act' => 'vip_level',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '添加',
						'act' => 'vip_level',
						'op' => 'add'
					),
					array(
						'name' => '编辑',
						'act' => 'vip_level',
						'op' => 'edit'
					),
					array(
						'name' => '删除',
						'act' => 'vip_level',
						'op' => 'del'
					),
				),
            ),
            array(
                'name' => '升级申请记录',
                'act' => 'vip_level',
                'op' => 'up_log',
				'child' => array(
					array(
						'name' => '审核',
						'act' => 'vip_level',
						'op' => 'shenhe'
					),
				),
            ),
            array(
                'name' => '会员管理',
                'act' => 'member',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '编辑用户',
						'act' => 'member',
						'op' => 'edit'
					),
					array(
						'name' => '删除',
						'act' => 'member',
						'op' => 'del'
					),
				),
            ),
             array(
                'name' => '充值订单',
                'act' => 'pd_log',
                'op' => 'order'
            ) ,
			array(
                'name' => '余额明细',
                'act' => 'pd_log',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '调节余额',
						'act' => 'pd_log',
						'op' => 'add'
					),
					array(
						'name' => '获取会员信息',
						'act' => 'pd_log',
						'op' => 'checkmember'
					)
				),
            ),
            /*array(
                'name' => '区域代理级别',
                'act' => 'area_level',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '添加',
						'act' => 'area_level',
						'op' => 'add'
					),
					array(
						'name' => '编辑',
						'act' => 'area_level',
						'op' => 'edit'
					),
					array(
						'name' => '删除',
						'act' => 'area_level',
						'op' => 'del'
					),
				),
            ),
			array(
                'name' => '区域代理',
                'act' => 'area_account',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '编辑代理',
						'act' => 'area_account',
						'op' => 'publish'
					),
					array(
						'name' => '删除',
						'act' => 'area_account',
						'op' => 'del'
					),
				),
            ),*/
			array(
				'name' => '发送提货券',
				'act' => 'tihuoquan',
				'op' => 'send'
			),
			array(
				'name' => '发放记录',
				'act' => 'tihuoquan',
				'op' => 'send_log'
			),
        )
    ) ,
	'orders' => array(
		'icon' => 'jilu',
		'size' => '16px',
        'name' => '订单系统',
        'child' => array(
			array(
                'name' => '提货订单',
                'act' => 'tihuo_order',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '详情',
						'act' => 'tihuo_order',
						'op' => 'show_order'
					),
					array(
						'name' => '取消订单',
						'act' => 'tihuo_order',
						'op' => 'order_cancel'
					),
					array(
						'name' => '修改运费',
						'act' => 'tihuo_order',
						'op' => 'shipping_price'
					),
					array(
						'name' => '发货',
						'act' => 'shop_deliver',
						'op' => 'send'
					),
					array(
						'name' => '编辑收货地址',
						'act' => 'shop_deliver',
						'op' => 'buyer_address_edit'
					),
					array(
						'name' => '选择发货地址',
						'act' => 'shop_deliver',
						'op' => 'send_address_select'
					),
					array(
						'name' => '延迟收货',
						'act' => 'shop_deliver',
						'op' => 'delay_receive'
					),
					array(
						'name' => '查看物流',
						'act' => 'shop_deliver',
						'op' => 'search_deliver'
					),
					array(
						'name' => '订单打印',
						'act' => 'tihuo_order',
						'op' => 'print_order'
					),
					array(
						'name' => '选择物流公司',
						'act' => 'shop_deliver',
						'op' => 'waybill_express'
					),
				),
            ) ,
			array(
                'name' => '零售订单',
                'act' => 'shop_order',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '详情',
						'act' => 'shop_order',
						'op' => 'show_order'
					),
					array(
						'name' => '取消订单',
						'act' => 'shop_order',
						'op' => 'order_cancel'
					),
					array(
						'name' => '修改运费',
						'act' => 'shop_order',
						'op' => 'shipping_price'
					),
					array(
						'name' => '修改价格',
						'act' => 'shop_order',
						'op' => 'order_price'
					),
					array(
						'name' => '发货',
						'act' => 'shop_deliver',
						'op' => 'send'
					),
					array(
						'name' => '编辑收货地址',
						'act' => 'shop_deliver',
						'op' => 'buyer_address_edit'
					),
					array(
						'name' => '选择发货地址',
						'act' => 'shop_deliver',
						'op' => 'send_address_select'
					),
					array(
						'name' => '延迟收货',
						'act' => 'shop_deliver',
						'op' => 'delay_receive'
					),
					array(
						'name' => '查看物流',
						'act' => 'shop_deliver',
						'op' => 'search_deliver'
					),
					array(
						'name' => '订单打印',
						'act' => 'shop_order',
						'op' => 'print_order'
					),
					array(
						'name' => '发放复购见单奖励',
						'act' => 'shop_order',
						'op' => 'grant_reward'
					),
					array(
						'name' => '收回复购见单奖励',
						'act' => 'shop_order',
						'op' => 'revoke_reward'
					),
					array(
						'name' => '选择物流公司',
						'act' => 'shop_deliver',
						'op' => 'waybill_express'
					),
				),
            ) ,
            /*array(
                'name' => '发货地址',
                'act' => 'shop_daddress',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '添加',
						'act' => 'shop_daddress',
						'op' => 'add'
					),
					array(
						'name' => '编辑',
						'act' => 'shop_daddress',
						'op' => 'edit'
					),
					array(
						'name' => '删除',
						'act' => 'shop_daddress',
						'op' => 'del'
					),
				),
            ),*/
			array(
                'name' => '运单模板',
                'act' => 'shop_waybill',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '添加',
						'act' => 'shop_waybill',
						'op' => 'add'
					),
					array(
						'name' => '编辑',
						'act' => 'shop_waybill',
						'op' => 'edit'
					),
					array(
						'name' => '删除',
						'act' => 'shop_waybill',
						'op' => 'del'
					),
					array(
						'name' => '设计',
						'act' => 'shop_waybill',
						'op' => 'design'
					),
				),
            ),
			array(
                'name' => '虚拟订单',
                'act' => 'shop_vr_order',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '详情',
						'act' => 'shop_vr_order',
						'op' => 'show_order'
					),
					array(
						'name' => '取消订单',
						'act' => 'shop_vr_order',
						'op' => 'order_cancel'
					),
					array(
						'name' => '消费兑换码',
						'act' => 'shop_vr_order',
						'op' => 'exchange',
					),
				),
            ) ,
			array(
                'name' => '实物退款',
                'act' => 'shop_return',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '退款',
						'act' => 'shop_return',
						'op' => 'pay_record'
					),
					array(
						'name' => '驳回',
						'act' => 'shop_return',
						'op' => 'reject'
					)
				),
            ),
			array(
                'name' => '虚拟退款',
                'act' => 'shop_vr_return',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '退款',
						'act' => 'shop_vr_return',
						'op' => 'pay_record'
					),
					array(
						'name' => '驳回',
						'act' => 'shop_vr_return',
						'op' => 'reject'
					)
				),
            ),
			array(
                'name' => '评论管理',
                'act' => 'shop_evaluate',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '编辑',
						'act' => 'shop_evaluate',
						'op' => 'edit'
					)
				),
            ),
        )
    ) ,
	'distribute' => array(
		'icon' => 'fenxiaoHT',
		'size' => '16px',
        'name' => '分销系统',
        'child' => array(
			array(
                'name' => '基本设置',
                'act' => 'config',
                'op' => 'distribute'
            ) ,
            array(
                'name' => '分红明细记录',
                'act' => 'distribute_award',
                'op' => 'fenhong_index'
            ) ,
			array(
                'name' => '复购见单奖励',
                'act' => 'distribute_award',
                'op' => 'fgjdjl_index'
            ) ,
			array(
                'name' => '复购见单奖励日志',
                'act' => 'distribute_award',
                'op' => 'fgjdjl_log'
            ) ,
			array(
                'name' => '商品分销记录',
                'act' => 'distribute_award',
                'op' => 'index'
            ) ,
			array(
                'name' => '平级奖明细',
                'act' => 'distribute_award',
                'op' => 'other_index'
            ) ,
			array(
                'name' => '发放绩效分红',
                'act' => 'distribute_award',
                'op' => 'yeji_fenhong_send'
            ) ,
            array(
                'name' => '发放加权分红',
                'act' => 'distribute_award',
                'op' => 'jiaquan_fenhong_send'
            ) ,
            			array(
                'name' => '发放零售区分红',
                'act' => 'distribute_award',
                'op' => 'lingshou_fenhong_send'
            ) ,
			array(
                'name' => '手动发放分红券',
                'act' => 'distribute_award',
                'op' => 'manual_fenhongquan'
            ) ,
			array(
                'name' => '提现方式管理',
                'act' => 'withdraw_method',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '添加',
						'act' => 'withdraw_method',
						'op' => 'add'
					),
					array(
						'name' => '编辑',
						'act' => 'withdraw_method',
						'op' => 'edit'
					),
					array(
						'name' => '删除',
						'act' => 'withdraw_method',
						'op' => 'del'
					),
				),
            ),
			array(
                'name' => '提现记录',
                'act' => 'withdraw_record',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '处理',
						'act' => 'withdraw_method',
						'op' => 'deal'
					),
					array(
						'name' => '转账',
						'act' => 'withdraw_method',
						'op' => 'pay_record'
					),
					array(
						'name' => '驳回',
						'act' => 'withdraw_method',
						'op' => 'reject'
					),
				),
            ),
			array(
                'name' => '推广海报',
                'act' => 'poster',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '添加',
						'act' => 'poster',
						'op' => 'add'
					),
					array(
						'name' => '编辑',
						'act' => 'poster',
						'op' => 'edit'
					),
					array(
						'name' => '删除',
						'act' => 'poster',
						'op' => 'del'
					),
					array(
						'name' => '设计',
						'act' => 'poster',
						'op' => 'design'
					),
				),
            ) ,
        )
    ) ,
	/*'dis_public' => array(
		'icon' => 'mofang',
		'size' => '19px',
        'name' => '公排系统',
        'child' => array(
			array(
                'name' => '基本设置',
                'act' => 'config',
                'op' => 'dis_public'
            ) ,
			array(
                'name' => '奖励设置',
                'act' => 'config',
                'op' => 'dis_public_commission'
            ) ,
		    array(
                'name' => '公排管理',
                'act' => 'dis_public',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '查看下属',
						'act' => 'dis_public',
						'op' => 'pubchilds'
					),
				),
            ) ,
			array(
                'name' => '奖励明细',
                'act' => 'dis_public',
                'op' => 'award_list'
            ) ,
        )
    ) ,*/
	'marketing' => array(
		'icon' => 'huodong',
		'size' => '16px',
        'name' => '营销',
		'child' => array(
			array(
                'name' => '限时折扣',
                'act' => 'promotion_xianshi',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '详情',
						'act' => 'promotion_xianshi',
						'op' => 'info'
					),
					array(
						'name' => '取消',
						'act' => 'promotion_xianshi',
						'op' => 'cancel'
					),
					array(
						'name' => '删除',
						'act' => 'promotion_xianshi',
						'op' => 'del'
					),
				),
            ) ,
			array(
                'name' => '满即送',
                'act' => 'promotion_mansong',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '详情',
						'act' => 'promotion_mansong',
						'op' => 'info'
					),
					array(
						'name' => '取消',
						'act' => 'promotion_mansong',
						'op' => 'cancel'
					),
					array(
						'name' => '删除',
						'act' => 'promotion_mansong',
						'op' => 'del'
					),
				),
            ) ,
			array(
                'name' => '代金券',
                'act' => 'voucher',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '编辑',
						'act' => 'voucher',
						'op' => 'edit'
					),
					array(
						'name' => '设置',
						'act' => 'voucher',
						'op' => 'config'
					),
					array(
						'name' => '面额管理',
						'act' => 'voucher',
						'op' => 'pricelist'
					),
					array(
						'name' => '添加面额',
						'act' => 'voucher',
						'op' => 'priceadd'
					),
					array(
						'name' => '编辑面额',
						'act' => 'voucher',
						'op' => 'priceedit'
					),
				),
            ) ,
		)
	),
	/*'turntable' => array(
		'icon' => 'dazhuanpan',
		'size' => '16px',
        'name' => '大转盘',
		'child' => array(
			array(
                'name' => '基本设置',
                'act' => 'turntable',
                'op' => 'config'
            ) ,
			array(
                'name' => '奖项设置',
                'act' => 'turntable',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '添加',
						'act' => 'turntable',
						'op' => 'add'
					),
					array(
						'name' => '编辑',
						'act' => 'turntable',
						'op' => 'edit'
					),
					array(
						'name' => '删除',
						'act' => 'turntable',
						'op' => 'del'
					),
				),
            ),
			array(
                'name' => '奖励明细',
                'act' => 'turntable',
                'op' => 'award_list'
            ) ,
		)
	),*/
	'article' => array(
		'icon' => 'wenzhang',
		'size' => '16px',
        'name' => '文章',
        'child' => array(
		    array(
                'name' => '文章分类',
                'act' => 'article_class',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '添加',
						'act' => 'article_class',
						'op' => 'add'
					),
					array(
						'name' => '编辑',
						'act' => 'article_class',
						'op' => 'edit'
					),
					array(
						'name' => '删除',
						'act' => 'article_class',
						'op' => 'del'
					),
				),
            ) ,
			array(
                'name' => '文章发布',
                'act' => 'article',
                'op' => 'publish'
            ) ,
			array(
                'name' => '文章列表',
                'act' => 'article',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '删除',
						'act' => 'article',
						'op' => 'del'
					),
				),
            )
        )
    ) ,
    /*'formguide' => array(
		'icon' => 'wenzhang',
		'size' => '16px',
        'name' => '表单系统',
        'child' => array(
		    array(
                'name' => '表单模型',
                'act' => 'formguide',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '操作',
						'act' => 'formguide',
						'op' => 'publish'
					),
					array(
						'name' => '删除',
						'act' => 'formguide',
						'op' => 'del'
					),
					array(
						'name' => '字段管理',
						'act' => 'formguide_field',
						'op' => 'index'
					),
					array(
						'name' => '操作管理',
						'act' => 'formguide_field',
						'op' => 'publish'
					),
					array(
						'name' => '字段删除',
						'act' => 'formguide_field',
						'op' => 'del'
					),
					array(
						'name' => '数据列表',
						'act' => 'formguide_info',
						'op' => 'index'
					),
					array(
						'name' => '备注',
						'act' => 'formguide_info',
						'op' => 'publish'
					),
					array(
						'name' => '删除',
						'act' => 'formguide_info',
						'op' => 'del'
					),
				),
            ) ,
        )
    ) ,*/
	'store' => array(
		'icon' => 'shangjia',
		'size' => '16px',
        'name' => '店铺',
        'child' => array(
			array(
                'name' => '店铺分类',
                'act' => 'store',
                'op' => 'store_class',
				'child' => array(
					array(
						'name' => '分类设置',
						'act' => 'store',
						'op' => 'class_publish'
					),
					array(
						'name' => '删除分类',
						'act' => 'store',
						'op' => 'class_del'
					),
				),
            ),
			array(
                'name' => '店铺管理',
                'act' => 'store',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '删除',
						'act' => 'store',
						'op' => 'del'
					),
					array(
						'name' => '商家分类',
						'act' => 'store_goods_class',
						'op' => 'index'
					),
					array(
						'name' => '添加',
						'act' => 'store_goods_class',
						'op' => 'add'
					),
					array(
						'name' => '编辑',
						'act' => 'store_goods_class',
						'op' => 'edit'
					),
					array(
						'name' => '删除',
						'act' => 'store_goods_class',
						'op' => 'del'
					),
				),
            ),
			array(
                'name' => '结算申请',
                'act' => 'store',
                'op' => 'tixianlist',
				'child' => array(
					array(
						'name' => '处理',
						'act' => 'store',
						'op' => 'deal_tixian'
					),
					array(
						'name' => '转账',
						'act' => 'store',
						'op' => 'pay_record'
					),
					array(
						'name' => '驳回',
						'act' => 'store',
						'op' => 'reject_tixian'
					),
				),
            ),
			array(
                'name' => '新增店铺',
                'act' => 'store',
                'op' => 'publish'
            ) ,
			/*array(
                'name' => '余额明细',
                'act' => 'seller_pd_log',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '调节余额',
						'act' => 'seller_pd_log',
						'op' => 'add'
					),
					array(
						'name' => '获取商家信息',
						'act' => 'seller_pd_log',
						'op' => 'checkmember'
					)
				),
            ),*/
        )
    ) ,
	'wechat' => array(
		'icon' => 'weixin',
		'size' => '16px',
        'name' => '微信',
        'child' => array(
            array(
                'name' => '基本设置',
                'act' => 'wechat',
                'op' => 'setting_manage',
            ),
			array(
				'name' => '接口配置',
				'act' => 'wechat',
				'op' => 'api_manage'
			),
			array(
				'name' => '首次关注设置',
				'act' => 'wechat',
				'op' => 'subcribe_manage'
			),
			array(
				'name' => '素材管理',
				'act' => 'wechat',
				'op' => 'material_manage',
				'child' => array(
					array(
						'name' => '添加',
						'act' => 'wechat',
						'op' => 'material_add'
					),
					array(
						'name' => '编辑',
						'act' => 'wechat',
						'op' => 'material_edit'
					),
					array(
						'name' => '删除',
						'act' => 'wechat',
						'op' => 'material_del'
					),
					array(
						'name' => '选择素材',
						'act' => 'wechat',
						'op' => 'material_list'
					),
				),
			),
			array(
				'name' => '关键词管理',
				'act' => 'wechat',
				'op' => 'keyword_manage',
				'child' => array(
					array(
						'name' => '添加',
						'act' => 'wechat',
						'op' => 'keyword_add'
					),
					array(
						'name' => '编辑',
						'act' => 'wechat',
						'op' => 'keyword_edit'
					),
					array(
						'name' => '删除',
						'act' => 'wechat',
						'op' => 'keyword_del'
					),
				),
			),
			/*array(
				'name' => 'URL管理',
				'act' => 'wechat',
				'op' => 'url_manage'
			),*/
			array(
				'name' => '自定义菜单',
				'act' => 'wechat',
				'op' => 'menu_manage',
				'child' => array(
					array(
						'name' => '添加',
						'act' => 'wechat',
						'op' => 'menu_add'
					),
					array(
						'name' => '编辑',
						'act' => 'wechat',
						'op' => 'menu_edit'
					),
					array(
						'name' => '删除',
						'act' => 'wechat',
						'op' => 'menu_del'
					),
				),
			),
        )
    ) ,
	'config' => array(
		'icon' => 'setting',
		'size' => '16px',
        'name' => '系统设置',
        'child' => array(
            array(
                'name' => '基本设置',
                'act' => 'config',
                'op' => 'base',
            ) ,
			/*array(
                'name' => '幻灯片',
                'act' => 'swiper',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '添加',
						'act' => 'swiper',
						'op' => 'add'
					),
					array(
						'name' => '编辑',
						'act' => 'swiper',
						'op' => 'edit'
					),
					array(
						'name' => '删除',
						'act' => 'swiper',
						'op' => 'del'
					),
				),
            ) ,*/
			array(
                'name' => '短信设置',
                'act' => 'config',
                'op' => 'sms'
            ) ,
			array(
                'name' => '支付设置',
                'act' => 'config',
                'op' => 'pay_index',
				'child' => array(
					array(
						'name' => '编辑',
						'act' => 'config',
						'op' => 'pay_edit'
					),
				),
            ) ,
			array(
                'name' => '客服设置',
                'act' => 'config',
                'op' => 'kefu'
            ) ,
			/*array(
                'name' => '积分设置',
                'act' => 'config',
                'op' => 'integral'
            ) ,*/
			array(
                'name' => '线下收款',
                'act' => 'config',
                'op' => 'offline'
            ) ,
            /*array(
                'name' => '地区管理',
                'act' => 'area',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '操作',
						'act' => 'area',
						'op' => 'publish'
					),
					array(
						'name' => '删除',
						'act' => 'area',
						'op' => 'del'
					),
				),
            ) ,*/
			array(
                'name' => '系统连接',
                'act' => 'config',
                'op' => 'index_module',
				'child' => array(
					array(
						'name' => '添加',
						'act' => 'config',
						'op' => 'module_add'
					),
					array(
						'name' => '编辑',
						'act' => 'config',
						'op' => 'module_edit'
					),
					array(
						'name' => '删除',
						'act' => 'config',
						'op' => 'module_del'
					)
				),
            ) ,
			array(
                'name' => '商城首页',
                'act' => 'mb_special',
                'op' => 'index_edit',
				'child' => array(
					array(
						'name' => '添加',
						'act' => 'mb_special',
						'op' => 'special_item_add'
					),
					array(
						'name' => '编辑',
						'act' => 'mb_special',
						'op' => 'special_item_edit'
					),
					array(
						'name' => '删除',
						'act' => 'mb_special',
						'op' => 'special_item_del'
					)
				),
            ) ,
            array(
                'name' => '专题管理',
                'act' => 'mb_special',
                'op' => 'special_list',
				'child' => array(
				    array(
						'name' => '保存',
						'act' => 'mb_special',
						'op' => 'special_save'
					),
					array(
						'name' => '编辑',
						'act' => 'mb_special',
						'op' => 'special_edit'
					),
					array(
						'name' => '删除',
						'act' => 'mb_special',
						'op' => 'special_del'
					)
				),
            ) ,
        )
    ) ,
	'attachment' => array(
		'icon' => 'fujian',
		'size' => '16px',
        'name' => '附件设置',
        'child' => array(
		    array(
                'name' => '附件分类',
                'act' => 'attachment',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '添加',
						'act' => 'attachment',
						'op' => 'add'
					),
					array(
						'name' => '编辑',
						'act' => 'attachment',
						'op' => 'edit'
					),
					array(
						'name' => '删除',
						'act' => 'attachment',
						'op' => 'del'
					),
				),
            ) ,
			array(
                'name' => '基本设置',
                'act' => 'attachment',
                'op' => 'config'
            ) ,
        )
    ) ,
	'account' => array(
		'icon' => 'quanxian',
		'size' => '16px',
        'name' => '管理员',
        'child' => array(
            array(
                'name' => '账号列表',
                'act' => 'account',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '添加',
						'act' => 'account',
						'op' => 'add'
					),
					array(
						'name' => '编辑',
						'act' => 'account',
						'op' => 'edit'
					),
					array(
						'name' => '删除',
						'act' => 'account',
						'op' => 'del'
					),
				),
            ),
			array(
                'name' => '账号组',
                'act' => 'account_group',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '操作',
						'act' => 'account_group',
						'op' => 'publish'
					),
					array(
						'name' => '删除',
						'act' => 'account_group',
						'op' => 'del'
					),
				),
            ),
			array(
                'name' => '账号日志',
                'act' => 'account_log',
                'op' => 'index'
            ),
        )
    ) ,
);