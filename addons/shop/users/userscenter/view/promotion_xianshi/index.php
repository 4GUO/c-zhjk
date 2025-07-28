<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<form method='get' action='<?=users_url('promotion_xianshi/index')?>'>
	<table class='search-form'>
		<tr>
			<td>&nbsp;</td>
			<th>状态</th>
			<td class='w100'>
				<select name='state'>
					<?php if(!empty($output['xianshi_state_array']) && is_array($output['xianshi_state_array'])) { ?>
					<?php foreach($output['xianshi_state_array'] as $key => $val) { ?>
					<option value='<?php echo $key;?>' <?php if(input('state', 0, 'intval') && intval($key) === input('state', 0, 'intval')) echo 'selected';?>><?php echo $val;?></option>
					<?php } ?>
					<?php } ?>
				</select>
			</td>
			<th class='w110'>活动名称</th>
			<td class='w160'><input type='text' class='text w150' name='xianshi_name' value='<?php echo input('xianshi_name', '');?>'/></td>
			<th class='w110'>商家名称</th>
			<td class='w160'><input type='text' class='text w150' name='store_name' value='<?php echo input('store_name', '');?>'/></td>
			<td class='w70 tc'>
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
			<th class='w30'></th>
			<th class='tl'>活动名称</th>
			<th class='w180'>开始时间</th>
			<th class='w180'>结束时间</th>
			<th class='w80'>购买下限</th>
			<th class='tc w80'>状态</th>
			<th class='w150'>操作</th>
		</tr>
	</thead>
	<?php if (!empty($output['list']) && is_array($output['list'])) { ?>
	<?php foreach ($output['list'] as $key => $val) { ?>
	<tbody id='xianshi_list'>
		<tr class='bd-line'>
			<td></td>
			<td class='tl'>
				<dl class='goods-name'>
					<dt><?php echo $val['xianshi_name'];?></dt>
				</dl>
			</td>
			<td class='goods-time'><?php echo date('Y-m-d H:i', $val['start_time']);?></td>
			<td class='goods-time'><?php echo date('Y-m-d H:i', $val['end_time']);?></td>
			<td><?php echo $val['lower_limit'];?></td>
			<td><?php echo $val['xianshi_state_text'];?></td>
			<td class='nscs-table-handle tc'>
				<span> 
					<a href='<?=users_url('promotion_xianshi/info', array('xianshi_id' => $val['xianshi_id']))?>' class='btn-blue'>
						<p>详情</p>
					</a> 
				</span>
				<?php if ($val['state'] != 3) { ?>
				<span> 
					<a href='javascript:void(0)' confirm='确定取消活动?' url='<?=users_url('promotion_xianshi/cancel', array('xianshi_id' => $val['xianshi_id']))?>' class='btn-blue delete'>
						<p>取消</p>
					</a> 
				</span>
				<?php } ?>
				<span> 
					<a href='javascript:void(0)' confirm='确定删除?' url='<?=users_url('promotion_xianshi/del', array('xianshi_id' => $val['xianshi_id']))?>' class='btn-red delete'>
						<p>删除</p>
					</a> 
				</span>
			</td>
		</tr>
		<?php } ?>
		<?php } else { ?>
		<tr id='xianshi_list_norecord'>
			<td class='norecord' colspan='20'><div class='warning-option'><i class='icon-warning-sign'></i><span>暂无活动</span></div></td>
		</tr>
		<?php } ?>
	</tbody>
	<tfoot>
		<?php if(!empty($output['list']) && is_array($output['list'])){?>
		<tr>
			<td colspan='20'><div class='pagination'><?=$output['page']?></div></td>
		</tr>
		<?php } ?>
	</tfoot>
</table>