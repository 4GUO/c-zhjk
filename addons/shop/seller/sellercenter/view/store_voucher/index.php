<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='tabmenu'>
	<a class='css-btn css-btn-green' href='<?php echo _url('store_voucher/publish');?>'>新增代金券</a>
</div>
<div class='alert alert-block mt10 mb10'>
	<ul>
		<li>1、手工设置代金券失效后,用户将不能领取该代金券,但是已经领取的代金券仍然可以使用</li>
		<li>2、代金券模版和已发放的代金券过期后自动失效</li>
	</ul>
</div>
<form method='get' action='<?=users_url('store_voucher/index')?>'>
	<table class='search-form'>
		<tr>
			<td>&nbsp;</td>
			<th>有效期</th>
			<td class='w240'>
				<input type='text' class='text w70' readonly='readonly' value='<?=input('txt_startdate', '')?>' id='txt_startdate' name='txt_startdate' />
				<label class='add-on'> <i class='icon-calendar'></i> </label>
				&nbsp;&#8211;&nbsp;
				<input type='text' class='text w70' readonly='readonly' value='<?=input('txt_enddate', '')?>' id='txt_enddate' name='txt_enddate' />
				<label class='add-on'> <i class='icon-calendar'></i> </label>
			</td>
			<th>状态</th>
			<td class='w120'>
				<select class='w80' name='select_state'>
					<option value='0' <?php if (input('select_state', 0, 'intval')) { echo 'selected=true';}?>>请选择</option>
					<?php if (!empty($output['templatestate_arr'])) { ?>
					<?php foreach ($output['templatestate_arr'] as $k => $v) { ?>
					<option value='<?php echo $v[0]; ?>' <?php if (input('select_state', 0, 'intval') == $v[0]){echo 'selected=true';}?>><?php echo $v[1];?></option>
					<?php }?>
					<?php }?>
				</select>
			</td>
			<th>领取方式</th>
			<td class='w120'>
				<select class='w80' name='gettype_sel'>
					<option value='' <?php if (input('gettype_sel', '')) { echo 'selected=true';}?>>请选择</option>
					<?php if (!empty($output['gettype_arr'])) { ?>
					<?php foreach ($output['gettype_arr'] as $k => $v) { ?>
					<option value='<?php echo $k; ?>' <?php if (input('gettype_sel', '') == $k){echo 'selected=true';}?>><?php echo $v['name'];?></option>
					<?php }?>
					<?php }?>
				</select>
			</td>
			<th class='w60'>代金券名称</th>
			<td class='w160'><input type='text' class='text w150' value='' id='txt_keyword' name='txt_keyword' /></td>
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
			<th class='w50'></th>
			<th class='tl'>代金券名称</th>
			<th class='w100'>消费金额</th>
			<th class='w100'>会员级别</th>
			<th class='w60'>面额</th>
			<th class='w200'>有效期</th>
			<th class='w60'>领取方式</th>
			<th class='w60'>状态</th>
			<th class='w150'>操作</th>
		</tr>
	</thead>
	<tbody>
		<?php if (count($output['list']) > 0) { ?>
		<?php foreach($output['list'] as $val) { ?>
		<tr class='bd-line'>
			<td><div class='pic-thumb'> <img src='<?php echo $val['voucher_t_customimg'];?>' /> </div></td>
			<td class='tl'><?php echo $val['voucher_t_title'];?></td>
			<td>￥<?php echo $val['voucher_t_limit'];?></td>
			<td><?php echo $val['voucher_t_mgradelimittext'];?></td>
			<td class='goods-price'>￥<?php echo $val['voucher_t_price'];?></td>
			<td class='goods-time'><?php echo date('Y-m-d', $val['voucher_t_start_date']) . '~' . date('Y-m-d', $val['voucher_t_end_date']);?></td>
			<td><?php echo $val['voucher_t_gettype_text'];?></td>
			<td><?php echo $val['voucher_t_state_text']; ?></td>
			<td class='nscs-table-handle'>
				<?php if($val['voucher_t_state'] == $output['templatestate_arr']['usable'][0] && (!$val['voucher_t_giveout']) && (!$val['voucher_t_isbuild'])) {//代金券模板有效并且没有领取时可以编辑?>
				<span> 
					<a class='btn-blue' href='<?php echo _url('store_voucher/publish', array('tid' => $val['voucher_t_id']));?>'>
						<p>编辑</p>
					</a> 
				</span>
				<?php } ?>
				
				<!-- 代金券详细 --> 
				<span> 
					<a class='btn-blue' href='<?php echo _url('store_voucher/info', array('tid' => $val['voucher_t_id']));?>'>
						<p>详细</p>
					</a> 
				</span>
				<?php if ((!$val['voucher_t_giveout']) && (!$val['voucher_t_isbuild'])) {//该模板没有发放过代金券时可以删除?>
				<span> 
					<a href='javascript:void(0)' confirm='确定删除?' url='<?=users_url('store_voucher/del', array('tid' => $val['voucher_t_id']))?>' class='btn-red delete'>
						<p>删除</p>
					</a> 
				</span>
				<?php }?>
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
$('#txt_startdate').datetimepicker({
	lang: 'ch',
	datepicker: true,
	format: 'Y-m-d',
	formatDate: 'Y-m-d',
	step: 5
});

$('#txt_enddate').datetimepicker({
	lang: 'ch',
	datepicker: true,
	format: 'Y-m-d',
	formatDate: 'Y-m-d',
	step: 5
});
</script>