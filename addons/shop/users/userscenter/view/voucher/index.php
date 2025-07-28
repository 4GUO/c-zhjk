<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='tabmenu'>
	<ul class='tab pngFix'>
		<li class='active'>
			<a href='<?=users_url('voucher/index')?>'>商家代金券</a>
		</li>
		<li class='normal'>
			<a href='<?=users_url('voucher/pricelist')?>'>面额管理</a>
		</li>
		<li class='normal'>
			<a href='<?=users_url('voucher/priceadd')?>'>添加面额</a>
		</li>
		<li class='normal'>
			<a href='<?=users_url('voucher/config')?>'>设置</a>
		</li>
	</ul>
</div>
<form method='get' action='<?=users_url('voucher/index')?>'>
	<table class='search-form'>
		<tr>
			<td>&nbsp;</td>
			<th>添加时间</th>
			<td class='w240'>
				<input type='text' class='text w70' readonly='readonly' value='<?=input('sdate', '')?>' id='sdate' name='sdate' />
				<label class='add-on'> <i class='icon-calendar'></i> </label>
				&nbsp;&#8211;&nbsp;
				<input type='text' class='text w70' readonly='readonly' value='<?=input('edate', '')?>' id='edate' name='edate' />
				<label class='add-on'> <i class='icon-calendar'></i> </label>
			</td>
			<th>状态</th>
			<td class='w120'>
				<select class='w80' name='state'>
					<option value='0' <?php if (input('state', 0, 'intval')) { echo 'selected=true';}?>>请选择</option>
					<?php if (!empty($output['templatestate_arr'])) { ?>
					<?php foreach ($output['templatestate_arr'] as $k => $v) { ?>
					<option value='<?php echo $v[0]; ?>' <?php if (input('state', 0, 'intval') == $v[0]){echo 'selected=true';}?>><?php echo $v[1];?></option>
					<?php }?>
					<?php }?>
				</select>
			</td>
			<th class='w60'>店铺名称</th>
			<td class='w160'><input type='text' class='text w150' value='' id='store_name' name='store_name' /></td>
			<td class='tc w70'>
				<label class='submit-border'>
					<input type='submit' class='submit' value='搜索' />
				</label>
			</td>
		</tr>
	</table>
</form>
<table class='css-default-table'>
	<thead>
		<tr>
			<th class='tl'>店铺名称</th>
			<th class='tl'>代金券名称</th>
			<th class='w100'>消费金额</th>
			<th class='w60'>面额</th>
			<th class='w250'>有效期</th>
			<th class='w150'>添加时间</th>
			<th class='w60'>状态</th>
			<th class='w150'>操作</th>
		</tr>
	</thead>
	<tbody>
		<?php if (count($output['list']) > 0) { ?>
		<?php foreach($output['list'] as $val) { ?>
		<tr class='bd-line'>
			<td class='tl'><?php echo $output['store_list'][$val['voucher_t_store_id']]['name'];?></td>
			<td class='tl'><?php echo $val['voucher_t_title'];?></td>
			<td>￥<?php echo $val['voucher_t_limit'];?></td>
			<td class='goods-price'>￥<?php echo $val['voucher_t_price'];?></td>
			<td class='goods-time'><?php echo date('Y-m-d', $val['voucher_t_start_date']) . '~' . date('Y-m-d', $val['voucher_t_end_date']);?></td>
			<td><?php echo date('Y-m-d H:i:s', $val['voucher_t_add_date']);?></td>
			<td><?php echo $val['voucher_t_state_text']; ?></td>
			<td class='nscs-table-handle'>
				<span> 
					<a class='btn-blue' href='<?php echo _url('voucher/edit', array('tid' => $val['voucher_t_id']));?>'>
						<p>编辑</p>
					</a> 
				</span>
			</td>
		</tr>
		<?php }?>
		<?php } else { ?>
		<tr>
			<td colspan='20' class='norecord'><div class='warning-option'><i class='icon-warning-sign'></i><span>暂无符合条件的数据记录</span></div></td>
		</tr>
		<?php } ?>
	</tbody>
	<tfoot>
		<?php if (count($output['list']) > 0) { ?>
		<tr>
			<td colspan='20'><div class='pagination'><?php echo $output['page']; ?></div></td>
		</tr>
		<?php } ?>
	</tfoot>
</table>
<link rel='stylesheet' type='text/css' href='<?=STATIC_URL?>/js/datetimepicker/jquery.datetimepicker.css'/>
<script type='text/javascript' src='<?=STATIC_URL?>/js/datetimepicker/jquery.datetimepicker.js'></script>
<script>
$('#sdate').datetimepicker({
	lang: 'ch',
	datepicker: true,
	format: 'Y-m-d',
	formatDate: 'Y-m-d',
	step: 5
});

$('#edate').datetimepicker({
	lang: 'ch',
	datepicker: true,
	format: 'Y-m-d',
	formatDate: 'Y-m-d',
	step: 5
});
</script>