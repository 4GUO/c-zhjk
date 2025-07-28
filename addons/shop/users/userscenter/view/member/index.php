<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<form method='get' action='<?=users_url('member/index')?>'>
	<table class='search-form'>	
		<tbody><tr>
			<td>&nbsp;</td>
			<th> 
				<select name='search_type'>
					<option value='nickname' <?php if(input('search_type', '') == 'nickname'){?> selected='selected'<?php }?>>昵称</option>
					<option value='truename' <?php if(input('search_type', '') == 'truename'){?> selected='selected'<?php }?>>姓名</option>
					<option value='mobile' <?php if(input('search_type', '') == 'mobile'){?> selected='selected'<?php }?>>手机号</option>
					<option value='t_nickname' <?php if(input('search_type', '') == 't_nickname'){?> selected='selected'<?php }?>>推荐人昵称</option>
					<option value='t_truename' <?php if(input('search_type', '') == 't_truename'){?> selected='selected'<?php }?>>推荐人姓名</option>
					<option value='t_mobile' <?php if(input('search_type', '') == 't_mobile'){?> selected='selected'<?php }?>>推荐人手机号</option>
				</select>
			</th>
			<td class='w160'><input class='text w150' name='keyword' value='<?=input('keyword', '')?>' type='text'></td>
			<th style='padding:0px 15px;'> 
				<select name='level_id'>
					<option value='0'>全部</option>
					<?php foreach($output['level_list'] as $level_id=>$level_name){?>
					<option value='<?php echo $level_id;?>' <?php if(input('level_id', 0, 'intval') == $level_id){?> selected='selected'<?php }?>><?php echo $level_name;?></option>
					<?php }?>
				</select>
			</th>			
			<th style='padding:0px 15px; display: none'> 
				<select name='state'>
					<option value='0'>全部</option>
					<option value='1' <?php if(input('state', 0, 'intval') == 1){?> selected='selected'<?php }?>>待审核</option>
					<option value='2' <?php if(input('state', 0, 'intval') == 2){?> selected='selected'<?php }?>>正常</option>
					<option value='3' <?php if(input('state', 0, 'intval') == 3){?> selected='selected'<?php }?>>已禁用</option>
				</select>
			</th>
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
			<th class='w60'>ID</th>
			<th colspan='2'>姓名</th>
			<th colspan='2'>推荐人</th>
			<!--<th class='w80'>姓名</th>-->
			<th class='w120'>手机号</th>
			<th class='w120'>级别</th>
			<th class='w150'>余额</th>
			<th class='w150'>加入时间</th>
			<th class='w120'>待合成</th>
			<th class='w120'>分红券</th>
			<th class='w150'>操作</th>
		</tr>
	</thead>
	<tbody>
		<?php if (!empty($output['list'])) { ?>
		<?php foreach ($output['list'] as $key => $val) { ?>
		<tr>
			<td><?=config('uid_pre') . padNumber($val['uid'])?></td>
			<td width='40'><div class='pic-thumb'><?php if(!empty($output['mapping_fans'][$val['uid']]['headimg'])) {?><img src='<?php echo $output['mapping_fans'][$val['uid']]['headimg'];?>' /><?php }?></div></td>
			<td style='text-align: left;width: 100px;'><?php if(!empty($output['mapping_fans'][$val['uid']]['nickname'])) {?><?php echo $output['mapping_fans'][$val['uid']]['nickname'];?><?php }?></td>
			<td width='40'><?php if(!empty($output['mapping_fans'][$val['inviter_id']]['headimg'])) {?><div class='pic-thumb'><img src='<?php echo $output['mapping_fans'][$val['inviter_id']]['headimg'];?>' /></div><?php }?></td>
			<td style='text-align: left;width: 100px;'><?php if(!empty($output['mapping_fans'][$val['inviter_id']]['nickname'])) {?><?php echo $output['mapping_fans'][$val['inviter_id']]['nickname'];?><?php }?></td>
			<!--<td><?php echo $val['truename'];?></td>-->
			<td><?php echo $val['mobile'];?></td>
			<td><?php echo empty($output['level_list'][$val['level_id']]) ? '暂无' : $output['level_list'][$val['level_id']]?></td>
			<td><span class='green'>可用：&yen;<?=$val['available_predeposit']?></span><br /><span class='red'>冻结：&yen;<?=$val['freeze_predeposit']?></span></td>
			<td class='goods-time'><?=date('Y-m-d H:i:s', $val['add_time'])?></td>
			<td><?php echo $val['can_tihuoquan_num'];?></td>
			<td><?php echo $val['fenhong_quan'];?></td>
			<!--<td><?php echo isset($output['account_list'][$val['uid']]) ? $output['account_list'][$val['uid']]['self_performance_num'] : 0;?></td>-->
			<td class='nscs-table-handle'>
				<span>
					<a href='<?=users_url('member/edit', array('uid' => $val['uid']))?>' class='btn-blue'><i class='icon-edit'></i>
						<p>编辑</p>
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