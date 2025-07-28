<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='tabmenu'>
	<a class='css-btn css-btn-green' href='<?php echo _url('promotion_mansong/mansong_add');?>'>添加活动</a>
</div>
<div class='alert alert-block mt10'>
	<ul>
		<li>1、已参加限时折扣的商品，可同时参加满即送活动</li>
	</ul>
</div>
<form method='get' action='<?=_url('promotion_mansong/index')?>'>
	<table class='search-form'>
		<tr>
			<td>&nbsp;</td>
			<th>状态</th>
			<td class='w100'>
				<select name='state'>
					<?php if(!empty($output['mansong_state_array']) && is_array($output['mansong_state_array'])) { ?>
					<?php foreach($output['mansong_state_array'] as $key => $val) { ?>
					<option value='<?php echo $key;?>' <?php if(input('state', 0, 'intval') && intval($key) === input('state', 0, 'intval')) echo 'selected';?>><?php echo $val;?></option>
					<?php } ?>
					<?php } ?>
				</select>
			</td>
			<th class='w110'>活动名称</th>
			<td class='w160'><input type='text' class='text w150' name='mansong_name' value='<?php echo input('mansong_name', '');?>'/></td>
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
			<th class='w80'>状态</th>
			<th class='w150'>操作</th>
		</tr>
	</thead>
	<?php if (!empty($output['list']) && is_array($output['list'])) { ?>
	<?php foreach ($output['list'] as $key => $val) { ?>
	<tbody id='mansong_list'>
		<tr class='bd-line'>
			<td></td>
			<td class='tl'>
				<dl class='goods-name'>
					<dt><?php echo $val['mansong_name'];?></dt>
				</dl>
			</td>
			<td class='goods-time'><?php echo date('Y-m-d H:i', $val['start_time']);?></td>
			<td class='goods-time'><?php echo date('Y-m-d H:i', $val['end_time']);?></td>
			<td><?php echo $val['mansong_state_text'];?></td>
			<td class='nscs-table-handle tc'>
				<span> 
					<a href='<?=users_url('promotion_mansong/mansong_detail', array('mansong_id' => $val['mansong_id']))?>' class='btn-blue'>
						<p>详情</p>
					</a> 
				</span>
				<span> 
					<a href='javascript:void(0)' confirm='确定删除?' url='<?=users_url('promotion_mansong/mansong_del', array('mansong_id' => $val['mansong_id']))?>' class='btn-red delete'>
						<p>删除</p>
					</a> 
				</span>
			</td>
		</tr>
		<?php } ?>
		<?php } else { ?>
		<tr id='mansong_list_norecord'>
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