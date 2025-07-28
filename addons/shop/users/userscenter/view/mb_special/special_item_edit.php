<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<style type='text/css'>
	.index_block {
		margin-top: 20px;
	}
	.index_block h3 {
		padding: 5px 0;
	}
	.btn-add {
		color: #333333;
	}
	.btn {
		display: inline-block !important;
		font: 14px/36px 'microsoft yahei';
		text-align: center;
		min-width: 100px;
		height: 36px !important;
		line-height: 36px !important;
		margin-top: 20px;
	}
</style>
<div class='page'> 
	<div class='fixed-empty'></div>
	<form id='form_item' action='<?php echo _url('mb_special/special_item_save');?>' method='post'>
		<input type='hidden' name='special_id' value='<?php echo $output['item_info']['special_id'];?>' />
		<input type='hidden' name='item_id' value='<?php echo $output['item_info']['item_id'];?>' />
		<table class='table tb-type2 nohover'>
			<tbody>
				<?php $item_data = $output['item_info']['item_data'];?>
				<?php $item_edit_flag = true;?>
				<tr class='noborder'>
					<td style='height: auto; padding: 0;'>
						<div id='item_edit_content' class='mb-item-edit-content'>
							<?php require(__DIR__ . '/module_' . $output['item_info']['item_type'] . '.php');?>
						</div>
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr class='tfoot'>
					<td colspan='2'>
						<a id='btn_save' class='submit btn' href='javascript:;'><span>保存编辑</span></a>
						<?php if($output['item_info']['special_id'] > 0) { ?>
						<a href='<?php echo _url('mb_special/special_edit', array('special_id' => $output['item_info']['special_id']));?>' class='submit btn'><span>返回上一级</span></a>
						<?php } else { ?>
						<a href='<?php echo _url('mb_special/index_edit');?>' class='submit btn'><span>返回上一级</span></a>
						<?php } ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</form>
</div>
<script type='text/javascript' src='<?=STATIC_URL?>/js/jquery-ui/jquery-ui.min.js'></script> 
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.iframe-transport.js' charset='utf-8'></script> 
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.ui.widget.js' charset='utf-8'></script> 
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.fileupload.js' charset='utf-8'></script> 
<script type='text/javascript' src='<?=STATIC_URL?>/js/template.js'></script>
<script type='text/javascript'>
    $(document).ready(function(){
        var special_id = <?php echo $output['item_info']['special_id'];?>;
        //保存
        $('#btn_save').on('click', function() {
            $('#form_item').submit();
        });
        //删除图片
        $('#item_edit_content').on('click', '[nctype=btn_del_item_image]', function() {
			
            $(this).parents('[nctype=item_image]').remove();
        });
    });
</script>