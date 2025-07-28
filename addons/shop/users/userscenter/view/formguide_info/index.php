<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<form method='get' action='<?=_url('formguide_info/index')?>'>
    <input type='hidden' name='modelid' value='<?php echo input('modelid', 0, 'intval');?>' />
	<table class='search-form'>	
		<tbody>
			<tr>
				<td>&nbsp;</td>
				<?php foreach($output['fieldArr'] as $vo) { ?>
				<?php if ($vo['isindex'] == 1) { ?>
    				<?php if ($vo['type'] == 'text' || $vo['type'] == 'number') { ?>
    				<th><?=$vo['title']?></th>
    				<td class='w160'><input class='text w150' name='<?=$vo['name']?>' value='<?=input($vo['name'], '')?>' type='text'></td>
    				<?php } ?>
    				<?php if ($vo['type'] == 'select') { ?>
    				<th><?=$vo['title']?></th>
    				<td class='w160'>
    					<select name='<?=$vo['name']?>' class='w150'>
        					<option value='-1'>全部</option>
        					<?php foreach($vo['options'] as $key => $v) { ?>
                            <option value='<?=$key?>' <?php if (input($vo['name'], '-1') == $key) { ?> selected='selected' <?php } ?>><?=$v?></option>
                            <?php } ?>
        				</select>
    				</td>
    				<?php } ?>
    			<?php } ?>
				<?php } ?>
				<th>时间</th>
    			<td class='w240'>
    				<input class='text w70' name='query_start_date' id='query_start_date' value='<?=input('query_start_date', '')?>' readonly='readonly' type='text' />
    				<label class='add-on'><i class='icon-calendar'></i></label>
    				&nbsp;–&nbsp;
    				<input id='query_end_date' class='text w70' name='query_end_date' value='<?=input('query_end_date', '')?>' readonly='readonly' type='text' />
    				<label class='add-on'><i class='icon-calendar'></i></label>
    			</td>
    			<th>状态</th>
    			<th> 
    				<select name='status'>
    					<option value='-1'>全部</option>
                        <option value='1' <?php echo input('status', -1) == 1 ? ' selected' : '';?>>正常</option>
                        <option value='0'<?php echo input('status', -1) == 0 ? ' selected' : '';?>>不正常</option>
                    </select>
    			</th>
				<td class='tc w70'>
					<label class='submit-border'>
						<input class='submit' value='搜索' type='submit'>
					</label>
				</td>
			</tr>
		</tbody>
	</table>
</form>
<table class='css-default-table' id='my_category'>
	<thead>
		<tr nc_type='table_header'>
			<th class='w30'>&nbsp;</th>
			<?php foreach ($output['fieldList'] as $v) { ?>
			<th><?=$v['title']?></th>
			<?php } ?>
			<th class='w150'>添加时间</th>
			<th class='w100'>IP</th>
			<th class='w50'>状态</th>
			<th>备注</th>
			<th class='w120'>操作</th>
		</tr>
		<?php if (!empty($output['list'])) { ?>
		<tr>
			<td class='tc'><input id='all' type='checkbox' class='checkall' /></td>
			<td colspan='20'>
			    <label for='all'>全选</label>
				<a href='javascript:void(0)' class='css-btn-mini' nc_type='batchbutton' uri='<?=_url('formguide_info/del')?>' name='id' confirm='确定删除?'><i class='icon-trash'></i>删除</a>
			</td>
		</tr>
		<?php } ?>
	</thead>
	<tbody>
		<?php if (!empty($output['list'])) { ?>
		<?php foreach ($output['list'] as $key => $val) { ?>
		<tr>
			<td><input class='checkitem tc' value='<?=$val['id']?>' type='checkbox'></td>
			<?php foreach ($output['fieldList'] as $v) { ?>
    			<?php if (!empty($v['options']) && in_array($v['type'], array('select', 'radio'))) { ?>
    			<td><?=isset($v['options'][$val[$v['name']]]) ? $v['options'][$val[$v['name']]] : ''?></td>
    			<?php } else if(!empty($v['options']) && in_array($v['type'], array('checkbox'))) {?>
    			<?php 
    			    $value_arr = $val[$v['name']] ? explode(',', $val[$v['name']]) : array();
    			    $value_str = '';
    			    foreach ($value_arr as $vv) {
    			        $value_str .= isset($v['options'][$vv]) ? $v['options'][$vv] . '，' : '';
    			    }
    			?>
    			<td><?=$value_str?></td>
    			<?php } else { ?>
    			<td><?=$val[$v['name']]?></td>
    			<?php } ?>
			<?php } ?>
			<td><?=date('Y-m-d H:i:s', $val['inputtime'])?></td>
			<td><?=$val['ip']?></td>
			<td><?php if($val['status'] == 0) { ?><font style='color: red;'>无效</font><?php } else if ($val['status'] == 1) { ?><font style='color: green;'>有效</font><?php } else { ?>未处理<?php }?></td>
			<td><?=$val['remark']?></td>
			<td class='nscs-table-handle'>
				<span>
					<a href='javascript:void(0)' nc_type='dialog' dialog_width='480' dialog_title='编辑信息' dialog_id='data_edit' uri='<?=_url('formguide_info/publish', array('id' => $val['id'], 'modelid' => input('modelid', 0, 'intval')))?>' class='btn-blue'><i class='icon-edit'></i>
				        <p>备注</p>
				    </a>
				</span> 
				<span>
					<a href='javascript:void(0);' confirm='您确定要删除吗?' url='<?=_url('formguide_info/del', array('id' => $val['id'], 'modelid' => input('modelid', 0, 'intval')))?>' class='btn-red delete'>
						<i class='icon-trash'></i>
						<p>删除</p>
					</a>
				</span>
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
<link rel='stylesheet' type='text/css' href='<?=STATIC_URL?>/js/datetimepicker/jquery.datetimepicker.css'/>
<script type='text/javascript' src='<?=STATIC_URL?>/js/datetimepicker/jquery.datetimepicker.js'></script>
<script>
$('#query_start_date').datetimepicker({
	lang: 'ch',
	datepicker: true,
	format: 'Y-m-d',
	formatDate: 'Y-m-d',
	step: 5
});

$('#query_end_date').datetimepicker({
	lang: 'ch',
	datepicker: true,
	format: 'Y-m-d',
	formatDate: 'Y-m-d',
	step: 5
});
</script>