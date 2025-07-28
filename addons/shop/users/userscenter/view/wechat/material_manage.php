<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<link type='text/css' href='<?=STATIC_URL?>/admin/js/weixin/material.css' rel='stylesheet' />
<div class='tabmenu'>
	<a href='<?=users_url('wechat/material_edit')?>' class='css-btn css-btn-green'>新增</a>
</div>
<form method='get' action='<?=users_url('wechat/material_manage')?>'>
	<table class='search-form'>	
		<tbody>
			<tr>
				<td>&nbsp;</td>
				<th>图文类型</th>
				<td class='w160'>
					<select name='material_type' class='w150'>
						<option value='0'>请选择...</option>
						<option <?php if(input('get.material_type', 0) == 1){?> selected='selected'<?php }?> value='1'>单图文</option>
						<option <?php if(input('get.material_type', 0) == 2){?> selected='selected'<?php }?> value='2'>多图文</option>
					</select>
				</td>
				<td class='tc w70'>
					<label class='submit-border'>
						<input class='submit' value='搜索' type='submit' />
					</label>
				</td>
			</tr>
		</tbody>
	</table>
</form>
<table class='css-default-table' id='my_category'>
	<div id='material_list'>
		<div class='list'>
			<?php if (!empty($output['material_list'])) { ?>
			<?php foreach ($output['material_list'] as $key => $value) {?>
			<?php if ($value['material_type'] == 2) { ?>
			<div class='item multi'>
				<div class='time'><?php echo date('Y-m-d', $value['material_addtime']);?></div>
				<?php foreach($value['material_content'] as $k => $v) { ?>
				<div class='<?php echo $k>0 ? 'other' : 'first' ?>'>
					<div class='info'>
						<div class='img'><img src='<?=$v['ImgPath'] ?>' /></div>
						<div class='title'><?php echo $v['Title'] ?></div>
					</div>
				</div>
				<?php }?>
				<div class='mod_del'>
					<div class='mod'><a href='<?=users_url('wechat/material_edit', array('mid' => $value['material_id']))?>'><img src='<?=STATIC_URL?>/admin/js/weixin/mod.gif' /></a></div>
					<div class='del'><a href='javascript:void(0);' class='delete' confirm='您确定要删除吗?' url='<?=users_url('wechat/material_del', array('mid' => $value['material_id']))?>'><img src='<?=STATIC_URL?>/admin/js/weixin/del.gif' /></a></div>
				</div>
			</div>
			<?php }else{?>
			<div class='item one'>
				<?php foreach($value['material_content'] as $k => $v) { ?>
				<div class='title'><?php echo $v['Title'] ?></div>
				<div><?php echo date('Y-m-d', $value['material_addtime']) ?></div>
				<div class='img'><img src='<?=$v['ImgPath'] ?>' /></div>
				<div class='txt'><?php echo str_replace(PHP_EOL, '<br />', $v['TextContents']);?></div>
				<?php }?>
				<div class='mod_del'>
					<div class='mod'><a href='<?=users_url('wechat/material_edit', array('mid' => $value['material_id']))?>'><img src='<?=STATIC_URL?>/admin/js/weixin/mod.gif' /></a></div>
					<div class='del'><a href='javascript:void(0);' class='delete' confirm='您确定要删除吗?' url='<?=users_url('wechat/material_del', array('mid' => $value['material_id']))?>'><img src='<?=STATIC_URL?>/admin/js/weixin/del.gif' /></a></div>
				</div>
			</div>
			<?php }?>
			<?php }?>
			<?php }?>
		</div>
	</div>
	<?php if (!empty($output['material_list'])) { ?>
	<tfoot>
		<tr>
			<td colspan='20'><div class='pagination'><?=$output['page']?></div></td>
		</tr>
	</tfoot>
	<?php } ?>
</table>