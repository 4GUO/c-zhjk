<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<table class='css-default-table' id='my_category'>
	<thead>
		<tr nc_type='table_header'>
			<th class='w30'>&nbsp;</th>
			<th colspan='2'>会员信息</th>
			<th class='w100'>要升级别</th>
			<th class='w100'>状态</th>
			<th class='w100'>申请时间</th>
			<th class='w200'>操作</th>
		</tr>
	</thead>
	<tbody>
		<?php if (!empty($output['list'])) { ?>
		<?php foreach ($output['list'] as $key => $val) { ?>
		<tr>
			<td><input class='checkitem tc' value='<?=$val['id']?>' type='checkbox'></td>
			<td width='70' height='90'><?php if(!empty($output['member_list'][$val['uid']]['headimg'])) {?><img src='<?php echo $output['member_list'][$val['uid']]['headimg'];?>' width='60' style='width: 60px; border-radius: 50%' /><?php }?></td>
			<td style='text-align: left; width: 200px;'>
				<p class='member_info'><span>姓名：</span><?php if(!empty($output['member_list'][$val['uid']]['nickname'])) {?><?php echo $output['member_list'][$val['uid']]['nickname'];?><?php }?></p>
				<p class='member_info'><span>手机号：</span><?php if(!empty($output['member_list'][$val['uid']]['mobile'])) {?><?php echo $output['member_list'][$val['uid']]['mobile'];?><?php }?></p>
			</td>
			
			<td><?=isset($output['level_list'][$val['level_id']]) ? $output['level_list'][$val['level_id']]['level_name'] : '暂无';?></td>
			<td>
			    <?php if($val['status'] == 0){?>待审核<?php }?>
			    <?php if($val['status'] == 1){?>已审核<?php }?>
			    <?php if($val['status'] == 2){?>已驳回<?php }?>
			</td>
			<td class='goods-time'><?=date('Y-m-d H:i:s', $val['add_time'])?></td>
			<td class='nscs-table-handle'>
			    <?php if($val['status'] == 0){?>
				<span>
					<a href='javascript:void(0);' confirm='您确定要审核吗?' url='<?=users_url('vip_level/shenhe', array('id' => $val['id']))?>' class='btn-red delete'>
						<p>审核</p>
					</a>
				</span>
				<span>
					<a href='javascript:void(0);' confirm='您确定要驳回吗?' url='<?=users_url('vip_level/bohui', array('id' => $val['id']))?>' class='btn-red delete'>
						<p>驳回</p>
					</a>
				</span>
				
				<?php }?>
			</td>
		</tr>
		<tr style='display: none;'>
			<td colspan='20'>
				<div class='css-goods-sku ps-container'></div>
			</td>
		</tr>
		<?php } ?>
		<?php } else { ?>
		<tr>
			<td colspan='20' class='norecord'><div class='warning-option'><i class='icon-warning-sign'></i><span>暂无记录</span> </div></td>
		</tr>
		<?php } ?>
	</tbody>
	<?php if (!empty($output['list'])) { ?>
	<tfoot>
		<tr>
			<td colspan='20'><div class='pagination'><?=$output['page']?></div></td>
		</tr>
	</tfoot>
	<?php } ?>
</table>