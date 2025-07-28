<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='tabmenu'>
	<a href='<?=_url('area_account/publish')?>' class='css-btn css-btn-green'>添加代理</a>
</div>
<form method='get' action='<?=users_url('area_account/index')?>'>
	<table class='search-form'>	
		<tbody><tr>
			<td>&nbsp;</td>
			<th> 
				<select name='status'>
				    <option value='0' <?php if(input('status', '') == 0){?> selected='selected'<?php }?>>全部</option>
					<option value='1' <?php if(input('status', '') == 1){?> selected='selected'<?php }?>>待审</option>
					<option value='2' <?php if(input('status', '') == 2){?> selected='selected'<?php }?>>正常</option>
				</select>
			</th>
			<th> 
				<select name='search_type'>
					<option value='nickname' <?php if(input('search_type', '') == 'nickname'){?> selected='selected'<?php }?>>昵称</option>
					<option value='truename' <?php if(input('search_type', '') == 'truename'){?> selected='selected'<?php }?>>姓名</option>
					<option value='mobile' <?php if(input('search_type', '') == 'mobile'){?> selected='selected'<?php }?>>手机号</option>
				</select>
			</th>
			<td class='w160'><input class='text w150' name='keyword' value='<?=input('keyword', '')?>' type='text'></td>
			<th style='padding:0px 15px;'> 
				<select name='level_id'>
					<option value='0'>全部</option>
					<?php foreach($output['level_list'] as $level_id => $level_name){?>
					<option value='<?php echo $level_id;?>' <?php if(input('level_id', 0, 'intval') == $level_id){?> selected='selected'<?php }?>><?php echo $level_name;?></option>
					<?php }?>
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
			<th colspan='2'>会员信息</th>
			<th class='w120'>级别</th>
			<th class='w150'>加入时间</th>
			<th class='w120'>状态</th>
			<th class='w150'>操作</th>
		</tr>
	</thead>
	<tbody>
		<?php if (!empty($output['list'])) { ?>
		<?php foreach ($output['list'] as $key => $val) { ?>
		<tr>
			<td><?=$val['uid']?></td>
			<td width='40'><div class='pic-thumb'><?php if(!empty($output['mapping_fans'][$val['uid']]['headimg'])) {?><img src='<?php echo $output['mapping_fans'][$val['uid']]['headimg'];?>' /><?php }?></div></td>
			<td style='text-align: left'><?php if(!empty($output['mapping_fans'][$val['uid']]['nickname'])) {?><?php echo $output['mapping_fans'][$val['uid']]['nickname'];?><?php }?></td>
			<td><?php echo empty($output['level_list'][$val['level_id']]) ? '暂无' : $output['level_list'][$val['level_id']]?></td>
			<td><?=date('Y-m-d H:i:s', $val['add_time'])?></td>
			<td><?=$val['status'] ? '正常' : '待审核'?></td>
			<td class='nscs-table-handle'>
				<span>
					<a href='<?=users_url('area_account/publish', array('id' => $val['id']))?>' class='btn-blue'><i class='icon-edit'></i>
						<p>编辑</p>
					</a>
				</span>
				<span>
				    <a href='javascript:void(0)' confirm='确定删除?' url='<?=users_url('area_account/del', array('id' => $val['id']))?>' class='btn-red delete'><i class='icon-trash'></i>
				        <p>删除</p>
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