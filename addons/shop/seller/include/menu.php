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
                'act' => 'store_goods_class',
                'op' => 'index',
				'child' => array(
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
            ) ,
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
                'name' => '商品发布',
                'act' => 'shop_goods',
                'op' => 'publish'
            ) ,
			array(
                'name' => '商品列表',
                'act' => 'shop_goods',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '获取属性产品',
						'act' => 'shop_goods',
						'op' => 'get_goods_list_ajax'
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
            )
        )
    ) ,
	'orders' => array(
		'icon' => 'jilu',
		'size' => '16px',
        'name' => '订单系统',
        'child' => array(
            array(
                'name' => '实物订单',
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
        )
    ) ,
	'promotion' => array(
		'icon' => 'huodong',
		'size' => '16px',
        'name' => '促销',
		'child' => array(
			array(
                'name' => '限时折扣',
                'act' => 'promotion_xianshi',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '添加',
						'act' => 'promotion_xianshi',
						'op' => 'xianshi_add'
					),
					array(
						'name' => '编辑',
						'act' => 'promotion_xianshi',
						'op' => 'xianshi_edit'
					),
					array(
						'name' => '管理',
						'act' => 'promotion_xianshi',
						'op' => 'xianshi_manage'
					),
					array(
						'name' => '删除',
						'act' => 'promotion_xianshi',
						'op' => 'xianshi_del'
					),
				),
            ),
			array(
                'name' => '满即送',
                'act' => 'promotion_mansong',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '添加',
						'act' => 'promotion_mansong',
						'op' => 'mansong_add'
					),
					array(
						'name' => '详情',
						'act' => 'promotion_mansong',
						'op' => 'mansong_detail'
					),
					array(
						'name' => '删除',
						'act' => 'promotion_mansong',
						'op' => 'mansong_del'
					),
				),
            ),
			array(
                'name' => '代金券',
                'act' => 'store_voucher',
                'op' => 'index',
				'child' => array(
					array(
						'name' => '添加/编辑',
						'act' => 'store_voucher',
						'op' => 'publish'
					),
					array(
						'name' => '详情',
						'act' => 'store_voucher',
						'op' => 'info'
					),
					array(
						'name' => '删除',
						'act' => 'store_voucher',
						'op' => 'del'
					),
				),
            ),
        ) 
	),
	'seller' => array(
		'icon' => 'caiwu',
		'size' => '16px',
        'name' => '财务',
		'child' => array(
            array(
                'name' => '资金流水',
                'act' => 'seller',
                'op' => 'floworder',
            ),
			array(
                'name' => '提现管理',
                'act' => 'seller',
                'op' => 'tixianlist',
				'child' => array(
					array(
						'name' => '提现',
						'act' => 'seller',
						'op' => 'tixian_form'
					),
					array(
						'name' => '提现',
						'act' => 'seller',
						'op' => 'tixian_form_submit'
					),
				),
            ),
			/*array(
                'name' => '余额明细',
                'act' => 'seller_pd_log',
                'op' => 'index',
            ),*/
			
        ) 
	),
	'config' => array(
		'icon' => 'setting',
		'size' => '16px',
        'name' => '设置',
		'child' => array(
            array(
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
            ),
			array(
                'name' => '运费设置',
                'act' => 'config',
                'op' => 'shipping',
				'child' => array(
					array(
						'name' => '区域运费',
						'act' => 'config',
						'op' => 'shipping_transport'
					),
					array(
						'name' => '添加区域运费',
						'act' => 'config',
						'op' => 'shipping_transport_add'
					),
				),
            ) ,
        )    
	),
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
        )
    ) ,
	'account' => array(
		'icon' => 'quanxian',
		'size' => '16px',
        'name' => '账号',
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