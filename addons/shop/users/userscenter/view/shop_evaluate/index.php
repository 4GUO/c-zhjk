<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<style type='text/css'>
#my_agent .member_info{
	height:20px;
	line-height:20px;
	color:#333;
}
#my_agent .member_info span{
	color:#999
}
</style>
<form method='get' action='<?=users_url('shop_evaluate/index')?>'>
	<table class='search-form'>
		<tbody><tr>
			<td>&nbsp;</td>
			<th> 
				<select name='search_type'>
					<option value='geval_frommembername' <?php if(input('get.search_type', '') == 'geval_frommembername'){?> selected='selected'<?php }?>>买家昵称</option>
					<option value='geval_goodsname' <?php if(input('get.search_type', '') == 'geval_goodsname'){?> selected='selected'<?php }?>>商品名称</option>
				</select>
			</th>
			<td class='w160'><input class='text w150' name='keyword' value='<?php echo input('get.keyword', '');?>' type='text'></td>
			<th>状态</th>
			<th> 
				<select name='status'>
					<option value='0'>全部</option>
                    <option value='1'<?php echo input('get.status', 0, 'intval') == 1 ? ' selected' : '';?>>不显示</option>
                    <option value='2'<?php echo input('get.status', 0, 'intval') == 2 ? ' selected' : '';?>>显示</option>
                </select>
			</th>
			<th>时间</th>
			<td class='w240'>
				<input class='text w70' name='query_start_date' id='query_start_date' value='<?=input('query_start_date', '')?>' readonly='readonly' type='text' />
				<label class='add-on'><i class='icon-calendar'></i></label>
				&nbsp;–&nbsp;
				<input id='query_end_date' class='text w70' name='query_end_date' value='<?=input('query_end_date', '')?>' readonly='readonly' type='text' />
				<label class='add-on'><i class='icon-calendar'></i></label>
			</td>
			<td class='tc w70'>
				<label class='submit-border'>
					<input class='submit' value='搜索' type='submit'>
				</label>
			</td>
		</tr>
	</tbody></table>
</form>
<table class='css-default-table' id='my_agent'>
	<thead>
		<tr>
			<th class='w40'>序号</th>
			<th colspan='2'>商品信息</th>
			<th colspan='2'>买家信息</th>			
			<th class='w60'>评分</th>
			<th>描述</th>
			<th class='w80'>状态</th>			
			<th class='w150'>时间</th>
			<th class='w150'>操作</th>
		</tr>
	</thead>
	<tbody>
		<?php if (!empty($output['list'])) { ?>
		<?php foreach ($output['list'] as $key => $val) {?>
		<tr>
			<td height='90'><?php echo $val['geval_id'] ?></td>
			<td width='70'><?php if(!empty($val['geval_goodsimage'])) {?><img src='<?php echo $val['geval_goodsimage'];?>' width='60' style='width: 60px;' /><?php }?></td>
			<td style='text-align: left; width: 180px;'><?php echo $val['geval_goodsname']?></td>
			<td width='70'><?php if(!empty($val['geval_frommemberheadimg'])) {?><img src='<?php echo $val['geval_frommemberheadimg'];?>' width='60' style='width: 60px; border-radius: 50%' /><?php }?></td>
			<td style='text-align: left; width: 150px;'><?php echo $val['geval_frommembername']?></td>			
			<td><span class='orange'><?=$val['geval_scores']?></span></td>
			<td><?=$val['geval_content']?></td>
			<td>
				<?php if($val['geval_state'] == 0){?>
				<font style='padding:8px 15px; color:#FFF; background:#999; border-radius:5px;'>不显示</font>
				<?php } else {?>
                <font style='padding:8px 15px; color:#FFF; background:#44b549; border-radius:5px;'>显示</font>
                <?php }?>
			</td>			
			<td class='goods-time'><?=date('Y-m-d H:i:s', $val['geval_addtime'])?></td>			
			<td class='nscs-table-handle'>
				<span>
				    <a href='javascript:void(0)' nc_type='dialog' dialog_width='800' dialog_title='编辑' dialog_id='my_edit' uri='<?=users_url('shop_evaluate/edit', array('geval_id' => $val['geval_id']))?>' class='btn-blue'><i class='icon-edit'></i>
				        <p>编辑</p>
				    </a>
				</span>
				<span>
					<a href='<?=users_url('shop_order/show_order', array('order_id' => $val['geval_orderid']))?>' class='btn-blue'><i class='icon-eye-open'></i>
						<p>订单</p>
					</a>
				</span>
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
<link rel='stylesheet' type='text/css' href='<?=STATIC_URL?>/js/datetimepicker/jquery.datetimepicker.css'/>
<script type='text/javascript' src='<?=STATIC_URL?>/js/datetimepicker/jquery.datetimepicker.js'></script>
<script>
$('#query_start_date').datetimepicker({
	lang:'ch',
	datepicker:true,
	format:'Y-m-d',
	formatDate:'Y-m-d',
	step:5
});

$('#query_end_date').datetimepicker({
	lang:'ch',
	datepicker:true,
	format:'Y-m-d',
	formatDate:'Y-m-d',
	step:5
});
</script>