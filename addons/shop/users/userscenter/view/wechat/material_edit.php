<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<link type='text/css' href='<?=STATIC_URL?>/admin/js/weixin/material.css' rel='stylesheet' />
<style>
	.css-layout {
		background: #ffffff;
		padding-bottom: 100px;
	}
	#material {
		border: none;
	}
	#material .m_righter {
		min-height: 400px;
	}
	.bottom {
		width: 300px;
	}
	.control a {
		padding: 0 10px;
	}
</style>
<div class='item-publish'>
	<form method='post' id='add_form' action='<?=users_url('wechat/material_edit')?>'>
		<input name='form_submit' value='ok' type='hidden' />
		<input type='hidden' name='mid' value='<?=input('mid', 0, 'intval');?>' />
		<div class='css-form-goods' id='material'>
			<div class='m_lefter multi'>
				<div class='time'><?php echo date('Y-m-d',$output['material']['material_addtime']);?></div>
				<?php
					if (!empty($output['material']['items'])) {
						foreach($output['material']['items'] as $key=>$value) {
				?>
				<div class='<?php echo $key==0 ? 'first' : 'list';?>' id='multi_msg_<?php echo $key;?>'>
					<div class='info'>
						<div class='img'><img src='<?=$value['ImgPath'];?>' /></div>
						<div class='title'><?php echo $value['Title'];?></div>
					</div>
					<div class='control'><a href='#mod'><img src='<?=STATIC_URL?>/admin/js/weixin/mod.gif' /></a></div>
					<input type='hidden' name='Title[]' value='<?php echo $value['Title'];?>' />
					<input type='hidden' name='Url[]' value='<?php echo $value['Url'];?>' />
					<input type='hidden' name='ImgPath[]' value='<?=$value['ImgPath'];?>' />
					<textarea style='display:none' name='TextContents[]'><?php echo $value['TextContents'];?></textarea>
				</div>
				<?php } ?>
				<?php if (count($output['material']['items']) == 1) { ?>
				<div class='list' style='display:none'>
					<div class='info'>
						<div class='title'>AAA</div>
						<div class='img'>BBB</div>
					</div>
					<div class='control'> <a href='#mod'><img src='<?=STATIC_URL?>/admin/js/weixin/mod.gif' /></a> <a href='#del'><img src='<?=STATIC_URL?>/admin/js/weixin/del.gif' /></a> </div>
					<input type='hidden' name='Title[]' value='' />
					<input type='hidden' name='Url[]' value='' />
					<input type='hidden' name='ImgPath[]' value='' />
					<textarea style='display:none' name='TextContents[]'></textarea>
				</div>
				<?php }} else { ?>
				<div class='first' id='multi_msg_0'>
					<div class='info'>
						<div class='img'>封面图</div>
						<div class='title'>标题</div>
					</div>
					<div class='control'><a href='#mod'><img src='<?=STATIC_URL?>/admin/js/weixin/mod.gif' /></a></div>
					<input type='hidden' name='Title[]' value='' />
					<input type='hidden' name='Url[]' value='' />
					<input type='hidden' name='ImgPath[]' value='' />
					<textarea style='display:none' name='TextContents[]'></textarea>
				</div>
				<div class='list' style='display:none'>
					<div class='info'>
						<div class='title'>EEE</div>
						<div class='img'>FFF</div>
					</div>
					<div class='control'> <a href='#mod'><img src='<?=STATIC_URL?>/admin/js/weixin/mod.gif' /></a> <a href='#del'><img src='<?=STATIC_URL?>/admin/js/weixin/del.gif' /></a> </div>
					<input type='hidden' name='Title[]' value='' />
					<input type='hidden' name='Url[]' value='' />
					<input type='hidden' name='ImgPath[]' value='' />
					<textarea style='display:none' name='TextContents[]'></textarea>
				</div>
				<?php }?>
				<div class='add'><a href='#add'><img src='<?=STATIC_URL?>/admin/js/weixin/add.gif' align='absmiddle' />增加一条</a></div>
			</div>
			<div class='m_righter'>
				<div class='mod_form'>
					<div class='jt'><img src='<?=STATIC_URL?>/admin/js/weixin/jt.gif' /></div>
					<div class='m_form'> 
						<span class='fc_red'>*</span> 标题<br />
						<div class='input'>
							<input name='inputTitle' value='<?php echo !empty($output['material']['items']) ? $output['material']['items'][0]['Title'] : ''; ?>' type='text' />
						</div>
						<div class='blank9'></div>
						<span class='fc_red'>*</span>封面图&nbsp;&nbsp;<span class='tips'>建议尺寸<span class='big_img_size_tips'>640*360px</span></span><br />
						<div class='control_upload'>
							<div class='css-upload-btn'> 
								<a href='javascript:void(0);'>
									<span>
										<input hidefocus='true' size='1' class='input-file' name='coverimg' id='coverimg' type='file' />
									</span>
									<p><i class='icon-upload-alt'></i>图片上传</p>
								</a> 
							</div>
							<input type='hidden' name='imgpath' ret='multi_msg_0' value='<?php echo !empty($output['material']['items']) ? $output['material']['items'][0]['ImgPath'] : ''; ?>' class='type-file-text' />
						</div>
						<div class='blank12'></div>
						简短介绍<br />
						<div class='intro'>
							<textarea name='inputContent'><?php echo !empty($output['material']['items']) ? $output['material']['items'][0]['TextContents'] : ''; ?></textarea>
						</div>
						<div class='blank3'></div>
						<span class='fc_red'>*</span> 链接<br />
						<div class='input'>
							<input name='inputUrl' value='<?php echo !empty($output['material']['items']) ? $output['material']['items'][0]['Url'] : ''; ?>' type='text' />
						</div>
					</div>
				</div>
			</div>
			<div class='clear'></div>
			<div class='bottom tc hr32'>
				<label class='submit-border'>
					<input class='submit' value='提交' type='button' />
				</label>
			</div>
		</div>
	</form>
</div>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.ui.widget.js' charset='utf-8'></script>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.fileupload.js' charset='utf-8'></script>
<script>
//$('.submit').click(function(e){
//	ajax_form_post('add_form');
//});
$(function(){
	$('input[name=msgtype]').click(function(){
		if($(this).val()==0){
			$('.msgtype_1').hide();
			$('.msgtype_0').show();
		}else{
			$('.msgtype_0').hide();
			$('.msgtype_1').show();
		}
	});
	$('.submit').click(function(){
        $('#add_form').submit();
    });
	upload_file('coverimg', '<?=users_url('album/image_upload')?>', function(data) {
		$('input[name=imgpath]').val(data.file_url);
		$('#view_img').attr('src', data.file_url);
		$('#' + $('input[name=imgpath]').attr('ret') + ' input[name=ImgPath\\[\\]]').val(data.file_url);
		$('#' + $('input[name=imgpath]').attr('ret') + ' .img').html('<img src=\'' + data.file_url + '\' />');
	});
	
	var material_multi_list_even = function(){
		$('.multi .first, .multi .list').each(function(){
			var children = $(this).children('.control');
			$(this).mouseover(function(){children.css({display:'block'});});
			$(this).mouseout(function(){children.css({display:'none'});});
				
			children.children('a[href*=#del]').click(function(){
				$(this).parent().parent().remove();
				$('.multi .first a[href*=#mod]').click();
				$('.mod_form').css({top: 37});
			});
				
			children.children('a[href*=#mod]').click(function(){
				var position = $(this).parent().offset();
				var material_form_position = $('#add_form').offset();
				var cur_id = '#' + $(this).parent().parent().attr('id');
				$('.mod_form').css({top: position.top - material_form_position.top});
				$('.mod_form input[name=imgpath]').attr('ret', $(this).parent().parent().attr('id'));
				$('.mod_form input[name=inputUrl]').val($(cur_id + ' input[name=Url\\[\\]]').val());
				$('.mod_form input[name=inputTitle]').val($(cur_id + ' input[name=Title\\[\\]]').val());
				$('.mod_form textarea[name=inputContent]').val($(cur_id + ' textarea[name=TextContents\\[\\]]').val());
				$('.mod_form input[name=imgpath]').val($(cur_id + ' input[name=ImgPath\\[\\]]').val());
				if($(cur_id + ' input[name=ImgPath\\[\\]]').val() != ''){
					$('#view_img').attr('src',$(cur_id+' input[name=ImgPath\\[\\]]').val());
				}else{
					$('#view_img').removeAttr('src');
				}
				$('.big_img_size_tips').html(cur_id == '#multi_msg_0' ? '640*360px' : '300*300px');
				$('.multi').data('cur_id', cur_id);
				
			});
		});
	}
	
	$('.multi').data('cur_id', '#'+$('.multi .first').attr('id'));
		
	$('.mod_form input').filter('[name=inputTitle]').on('keyup paste blur', function(){
		var cur_id=$('.multi').data('cur_id');
		$(cur_id+' input[name=Title\\[\\]]').val($(this).val());
		$(cur_id+' .title').html($(this).val());
	})
	
	$('.mod_form textarea').filter('[name=inputContent]').on('keyup paste blur', function(){
		var cur_id=$('.multi').data('cur_id');
		$(cur_id+' textarea[name=TextContents\\[\\]]').html($(this).val());
	})
	
	$('.mod_form input').filter('[name=inputUrl]').on('keyup paste blur', function(){
		var cur_id=$('.multi').data('cur_id');
		$(cur_id+' input[name=Url\\[\\]]').val($(this).val());
	})
		
	$('.mod_form img').filter('.btn_select_url').on('click', function(){
		var id = $('.multi').data('cur_id').replace('#multi_msg_', '');
	})
		
	material_multi_list_even();
	$('a[href=#add]').click(function(){
		$(this).blur();
		if($('.multi .list').size() >= 7){
			showError('您最多可以加入8条图文消息！');
			return false;
		}
		$('.multi .list, a[href*=#mod], a[href*=#del]').off();
		$('<div class=\'list\' id=\'multi_msg_' + Math.floor(Math.random()*1000000) + '\'>' + $('.multi .list:last').html() + '</div>').insertAfter($('.multi .list:last'));
		$('.multi .list:last').children('.info').children('.title').html('标题').siblings('.img').html('缩略图');
		$('.multi .list:last input').filter('[name=Title\\[\\]]').val('').end().filter('[name=Url\\[\\]]').val('').end().filter('[name=ImgPath\\[\\]]').val('');
		material_multi_list_even();
	});
});
</script>