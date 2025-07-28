<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<form method='get' action='<?=users_url('dis_public/pubchilds')?>'>
	<input type='hidden' name='aid' value='<?= input('aid', 0, 'intval') ?>'/>
	<table class='search-form'>	
		<tbody><tr>
			<td>&nbsp;</td>
			<th>昵称</th>
			<td class='w160'><input class='text w150' name='membername' value='<?=input('membername', '')?>' type='text'></td>
			<th style='padding:0px 15px;'> 
				<select name='status'>
					<option value='0'>状态</option>
					<option value='1' <?php if(input('status', 0, 'intval') == 1){?> selected='selected'<?php }?>>已出局</option>
					<option value='2' <?php if(input('status', 0, 'intval') == 2){?> selected='selected'<?php }?>>正常</option>
				</select>
			</th>
			<th style='padding:0px 15px;'>下级级别</th>
			<td class='w160'>
				<select name='level'>
					<option value='0'>全部</option>
					<?php if ($output['my_child_level'] > 0) { ?>
					<?php for ($i = 1; $i <= $output['my_child_level']; $i++) { ?>
					<option value='<?php echo $i;?>'<?php echo input('level', 0, 'intval') == $i ? ' selected' : '';?>><?php echo $i;?>级</option>
					<?php } ?>
					<?php } ?>
				</select>
			</td>
			<td class='tc w70'>
				<label class='submit-border'>
					<input class='submit' value='搜索' type='submit'>
				</label>
			</td>
		</tr>
	</tbody></table>
</form>
<table class='css-default-table' id='my_member'>
	<thead>
		<tr>
			<th class='w160'>卡位位置</th>
			<th colspan='2'>会员信息</th>
			<th colspan='2'>上级信息</th>
			<th class='w80'>状态</th>
			<th class='w150'>加入时间</th>
			<th class='w150'>操作</th>
		</tr>
	</thead>
	<tbody>
		<?php if (!empty($output['list'])) { ?>
		<?php foreach ($output['list'] as $key => $val) { ?>
		<tr>
			<td>第 <?php echo $val['distributor_y'];?> 级<br />第 <?php echo $val['distributor_x'];?> 个</td>
			<td width='40'><div class='pic-thumb'><img src='<?php echo $val['member_avatar'];?>' /></div></td>
			<td style='text-align: left'><?php echo $val['member_name'];?></td>
			<td width='40'><div class='pic-thumb'><img src='<?php echo $val['parent_avatar'];?>' /></div></td>
			<td style='text-align: left'><?php echo $val['parent_name'];?></td>
			<td><?php echo $val['status'] == 0 ? '<font style=\'padding:8px 15px; color:#FFF; background:#D9534F; border-radius:5px;\'>已出局</font>' : '<font style=\'padding:8px 15px; color:#FFF; background:#5CB85C; border-radius:5px;\'>正常</font>'?></td>
			<td class='goods-time'><?=date('Y-m-d H:i:s', $val['addtime'])?></td>
			<td class='nscs-table-handle'>
				<span>
					<a href='<?=users_url('dis_public/pubchilds', array('aid' => $val['ralate_id']))?>' class='btn-blue'>
						<p>下级会员</p>
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