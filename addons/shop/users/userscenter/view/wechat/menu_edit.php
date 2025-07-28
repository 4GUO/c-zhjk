<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<link type='text/css' href='<?=STATIC_URL?>/admin/js/weixin/material.css' rel='stylesheet' />
<link type='text/css' href='<?=STATIC_URL?>/admin/js/weixin/menu.css' rel='stylesheet' />
<style>
	.css-form-goods {
		padding: 10px;
	}
</style>
<div class='item-publish'>
	<form method='post' id='add_form' action='<?=users_url('wechat/menu_edit')?>'>
		<input name='form_submit' value='ok' type='hidden' />
		<input type='hidden' name='mid' value='<?=$output['mid']?>' />
		<input type='hidden' id='firstmenu' value='1' />
		<input type='hidden' id='childmenu' value='0' />
		<input name='materialid' id='materialid' value='0' type='hidden' />
		<div class='css-form-goods'>
			<div class='diy_menu_designer'>
				<div class='diy_menu_designer_top'></div>
				<div class='diy_menu_designer_content'>
					<div class='diy_menu_designer_content_top'></div>
					<ul id='menu_items'>
						<?php if($output['firstnum']>0){?>
						<?php foreach($output['first_info'] as $key=>$value){?>
						<li class='<?php echo $output['firstnum']==1 ? 'w162' : 'w108'?><?php echo $key==1 ? ' current' : ''?>' id='first_<?php echo $key;?>'>
							<input type='hidden' name='Title[<?php echo $key;?>][0]' value='<?php echo $value['detail_name'];?>' />
							<input type='hidden' name='MsgType[<?php echo $key;?>][0]' value='<?php echo $value['detail_msgtype'];?>' />
							<textarea style='display:none' name='TextContents[<?php echo $key;?>][0]'><?php echo $value['detail_textcontents'];?></textarea>
							<input type='hidden' name='MaterialID[<?php echo $key;?>][0]' value='<?php echo $value['detail_materialid'];?>' />
							<input type='hidden' name='Url[<?php echo $key;?>][0]' value='<?php echo $value['detail_url'];?>' />
							<a href='Javascript:editfirstmenu(<?php echo $key;?>);'><?php echo $value['detail_name'];?></a>
							<p<?php if($output['child_info'][$value['detail_id']]<5){?> style='height:<?php echo $output['child_info'][$value['detail_id']]*45+45;?>px'<?php }else{?> style='height:<?php echo $output['child_info'][$value['detail_id']]*45;?><?php }?>px'>
								<?php if($output['child_info'][$value['detail_id']]>0){?>
									<?php $output['second_info'][$value['detail_id']]['child'] = array_reverse($output['second_info'][$value['detail_id']]['child']);foreach($output['second_info'][$value['detail_id']]['child'] as $k=>$v){?>
									<span onclick='editchildmenu(<?php echo $key;?>,<?php echo ($output['child_info'][$value['detail_id']]-$k);?>)' id='child_<?php echo ($output['child_info'][$value['detail_id']]-$k);?>'><input type='hidden' name='Title[<?php echo $key;?>][<?php echo ($output['child_info'][$value['detail_id']]-$k);?>]' value='<?php echo $v['detail_name'];?>' /><input type='hidden' name='MsgType[<?php echo $key;?>][<?php echo ($output['child_info'][$value['detail_id']]-$k);?>]' value='<?php echo $v['detail_msgtype'];?>' /><textarea style='display:none' name='TextContents[<?php echo $key;?>][<?php echo ($output['child_info'][$value['detail_id']]-$k);?>]'><?php echo $v['detail_textcontents'];?></textarea><input type='hidden' name='MaterialID[<?php echo $key;?>][<?php echo ($output['child_info'][$value['detail_id']]-$k);?>]' value='<?php echo $v['detail_materialid'];?>' /><input type='hidden' name='Url[<?php echo $key;?>][<?php echo ($output['child_info'][$value['detail_id']]-$k);?>]' value='<?php echo $v['detail_url'];?>' /><i><?php echo $v['detail_name'];?></i></span>
									<?php }?>
								<?php }?>
								<?php if($output['child_info'][$value['detail_id']]<5){?>
								<span class='child_add_btn' onclick='addchildmenu(<?php echo $key;?>);'><font style='font-size:18px; font-weight:bold'>＋</font></span>
								<?php }?>
							</p>
							<em></em>
						</li>
						<?php }?>
						<?php }else{?>
						<li class='w162 current' id='first_1'>
							<input type='hidden' name='Title[1][0]' value='菜单1' />
							<input type='hidden' name='MsgType[1][0]' value='0' />
							<textarea style='display:none' name='TextContents[1][0]'></textarea>
							<input type='hidden' name='MaterialID[1][0]' />
							<input type='hidden' name='Url[1][0]' />
							<a href='Javascript:editfirstmenu(1);'>菜单1</a>
							<p>
								<span class='child_add_btn' onclick='addchildmenu(1);'><font style='font-size:18px; font-weight:bold'>＋</font></span>
							</p>
							<em></em>
						</li>
						<?php }?>
						<?php if($output['firstnum']<3){?>
						<li class='<?php echo $output['firstnum']==1 ? 'w162' : 'w108'?> btn'>
							<a href='Javascript:addfirstmenu();'><font style='font-size:14px; font-weight:bold'>＋</font>添加菜单</a>
						</li>
						<?php }?>
						<div class='clear'></div>
					</ul>
				</div>
				<div class='diy_menu_designer_footer'></div>
			</div>
			<div class='diy_menu_right'>
				<em></em>
				<div class='diy_menu_form'>
					<div class='diy_table'>
						<h2>标题</h2>
						<div class='input_rows p20'>
							<label>标题</label>
							<span><input type='text' name='MenuTitle' value='<?php echo $output['menu_info']['menu_name'];?>' id='MenuTitle' /></span>
							<div class='clear'></div>
						</div>
					</div>
					
					<div class='diy_table for_form m15'>
						<h2><s onclick='deletemenu();'></s>菜单设置</h2>
						<div class='input_rows pt20'>
							<label>菜单名称</label>
							<span><input type='text' name='inputtitle' value='' id='inputtitle' /></span>
							<div class='clear'></div>
						</div>
						<div class='input_rows other_detail pt20' id='menu_act'>
							<label>菜单动作</label>
							<span><input type='radio' name='inputtype' value='0' id='inputtype_0' checked />&nbsp;回复内容&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='inputtype' value='1' id='inputtype_1' />&nbsp;回复图文&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='inputtype' value='2' id='inputtype_2' />&nbsp;链接网址&nbsp;&nbsp;&nbsp;&nbsp;</span>
							<div class='clear'></div>
						</div>
						<div class='input_rows other_detail pt20' id='detail_msgtype_0'>
							<label>回复内容</label>
							<span>
								<textarea name='inputtextcontents'></textarea>
							</span>
							<div class='clear'></div>
						</div>
						<div class='input_rows other_detail pt20' id='detail_msgtype_1' style='display:none'>
							<label>回复素材</label>
							<span>
								<a href='javascript:;' nc_type='dialog' dialog_title='选择素材' dialog_id='sucai' dialog_width='675' dialog_height='550' uri='<?=users_url('wechat/material_list')?>'>【选择图文】</a>
								<div id='material_confirm' class='material_dialog' style='display:none'>
									<div class='list'>
										<div class='item'></div>
									</div>
								</div>
							</span>
							<div class='clear'></div>
						</div>
						<div class='input_rows other_detail pt20' id='detail_msgtype_2' style='display:none'>
							<label>链接网址</label>
							<span style='position:relative'>
								<input type='text' name='inputlink' class='urllink' value='' id='inputlink' />
							</span>
							<div class='clear'></div>
						</div>
						<div class='height20'></div>
					</div>
				</div>
			</div>
			<div class='clear'></div>
		</div>
		<div class='bottom tc hr32'>
			<label class='submit-border'>
				<input class='submit' value='提交' type='button' />
			</label>
		</div>
	</form>
</div>
<script>
var first_id = <?php echo $output['firstnum']+1;?>;
var child_id = <?php echo $output['j']+1;?>;
$('.submit').click(function(e) {
	if ($('#MenuTitle').val() == '') {
		alert('菜单名称不能为空');
		$('#MenuTitle').focus();
		return false;
	}
	if ($('#inputtitle').val()=='') {
		alert('请设置菜单');
		$('#inputtitle').focus();
		return false;
	}
	$('#add_form').submit();
});
$(function(){
	$('input[name=inputtype]').click(function(){
		select_msgtype($(this).val());
	});
	$('.for_form input').filter('[name=inputtitle]').on('keyup paste blur', function(){
		setmenuvalue('input', 'Title', $(this).val());
	})
	
	$('.for_form textarea').filter('[name=inputtextcontents]').on('keyup paste blur', function(){
		setmenuvalue('textarea', 'TextContents', $(this).val());
	})
		
	$('.for_form input').filter('[name=inputlink]').on('keyup paste blur', function(){
		setmenuvalue('input', 'Url', $(this).val());
	})
	
	setformvalue();
});
//set value to menu
function setmenuvalue(input_type,input_name,input_value){
	var fid=$('#firstmenu').attr('value');
	var cid=$('#childmenu').attr('value');
	if(input_type=='textarea'){
		$(input_type+'[name='+input_name+'\\['+fid+'\\]\\['+cid+'\\]]').html(input_value);
	}else{
		$(input_type+'[name='+input_name+'\\['+fid+'\\]\\['+cid+'\\]]').val(input_value);
	}
	
	if(input_name=='Title'){
		if(cid==0){
			$('#first_'+fid+' a').html(input_value);
		}else{
			$('#first_'+fid+' #child_'+cid+' i').html(input_value);
		}
	}
}

//set form value
function setformvalue(){
	var fid=$('#firstmenu').attr('value');
	var cid=$('#childmenu').attr('value');
	$('input[name=inputtitle]').val($('input[name=Title\\['+fid+'\\]\\['+cid+'\\]]').val());
	$('textarea[name=inputtextcontents]').val($('textarea[name=TextContents\\['+fid+'\\]\\['+cid+'\\]]').val());
	$('input[name=inputlink]').val($('input[name=Url\\['+fid+'\\]\\['+cid+'\\]]').val());
	var mtype = $('input[name=MsgType\\['+fid+'\\]\\['+cid+'\\]]').val();
	$('input[name=inputtype]:eq('+mtype+')').attr('checked',true);
	select_msgtype(mtype);
	
	if (mtype==1) {//display material
	    var material_id = $('input[name=MaterialID\\['+fid+'\\]\\['+cid+'\\]]').val();
		if (material_id==0) {
		    $('#material_confirm').hide();
			$('#material_confirm .list').html('<div class=\'item\'></div>');
		} else {
		    $('#material_confirm').show();
			$.getJSON('<?=users_url('wechat/ajax');?>?branch=get_material&mid='+material_id, '', function(data){
				$('#material_confirm .list').html(data.msg);
			});
		}
	}
}

//mstype click
function select_msgtype(msgtype){
	$('#detail_msgtype_0').hide();
	$('#detail_msgtype_1').hide();
	$('#detail_msgtype_2').hide();
	if(msgtype<3){
		$('#detail_msgtype_'+msgtype).show();
	}
		
	var fid=$('#firstmenu').attr('value');
	var cid=$('#childmenu').attr('value');
	$('input[name=MsgType\\['+fid+'\\]\\['+cid+'\\]]').val(msgtype);
	if(msgtype==1){//display material
	    var material_id = $('input[name=MaterialID\\['+fid+'\\]\\['+cid+'\\]]').val();
		if(material_id==0){
			$('#material_confirm').hide();
			$('#material_confirm .list').html('<div class=\'item\'></div>');
		}else{
			$.getJSON('<?=users_url('wechat/ajax')?>?branch=get_material&mid='+material_id, '', function(data){
				$('#material_confirm').show();
				$('#material_confirm .list').html(data.msg);
			});
		}
	}
}

//add first menu
function addfirstmenu(){
	var li_count = $('#menu_items li').length;
	$('#menu_items span').removeClass('curr');
	if(li_count>=3){
		$('#menu_items li').removeClass('current');
		$('#menu_items li.btn').html('<input type=\'hidden\' value=\'菜单'+first_id+'\' name=\'Title['+first_id+'][0]\' /><input type=\'hidden\' name=\'MsgType['+first_id+'][0]\' value=\'0\' /><textarea style=\'display:none\' name=\'TextContents['+first_id+'][0]\'></textarea><input type=\'hidden\' name=\'MaterialID['+first_id+'][0]\' /><input type=\'hidden\' name=\'Url['+first_id+'][0]\' /><a href=\'Javascript:editfirstmenu('+first_id+');\'>菜单'+first_id+'</a><p><span class=\'child_add_btn\' onclick=\'addchildmenu('+first_id+');\'><font style=\'font-size:18px; font-weight:bold\'>＋</font></span></p><em></em>');
		$('#menu_items li.btn').attr('id','first_'+first_id);
		$('#menu_items li.btn').removeClass('btn').addClass('current');
	}else{
		$('#menu_items li').removeClass('w162').addClass('w108');
		$('#menu_items li').removeClass('current');
		$('#menu_items li').eq(0).after('<li class=\'w108 current\' id=\'first_'+first_id+'\'><input type=\'hidden\' value=\'菜单'+first_id+'\' name=\'Title['+first_id+'][0]\' /><input type=\'hidden\' name=\'MsgType['+first_id+'][0]\' value=\'0\' /><textarea style=\'display:none\' name=\'TextContents['+first_id+'][0]\'></textarea><input type=\'hidden\' name=\'MaterialID['+first_id+'][0]\' /><input type=\'hidden\' name=\'Url['+first_id+'][0]\' /><a href=\'Javascript:editfirstmenu('+first_id+');\'>菜单'+first_id+'</a><p><span class=\'child_add_btn\' onclick=\'addchildmenu('+first_id+');\'><font style=\'font-size:18px; font-weight:bold\'>＋</font></span></p><em></em></li>');
	}
	
	$('#firstmenu').attr('value',first_id);
	$('#childmenu').attr('value','0');
	setformvalue();
	first_id++;
}

//edit child menu
function editfirstmenu(id){
	$('#menu_items li').removeClass('current');
	$('#menu_items li').eq(id-1).addClass('current');
	$('#menu_items span').removeClass('curr');
	$('#firstmenu').attr('value',id);
	$('#childmenu').attr('value','0');
	var span_count = $('#first_'+id+' p span').length;
	setformvalue();
	if(span_count>1){
		$('.for_form .other_detail').hide();
	}
}

//add child menu
function addchildmenu(id){
	var span_count = $('#first_' + id + ' p span').length;
	if(span_count>=5){
		$('#first_' + id + ' p span.child_add_btn').remove();
	}else{
		var p_height = 45 * (span_count+1);
		$('#first_'+id+' p').css('height',p_height);
	}
	$('#first_'+id+' p').prepend('<span onclick=\'editchildmenu('+id+','+child_id+')\' id=\'child_'+child_id+'\'><input type=\'hidden\' name=\'Title['+id+']['+child_id+']\' value=\'子菜单'+child_id+'\' /><input type=\'hidden\' name=\'MsgType['+id+']['+child_id+']\' value=\'0\' /><textarea style=\'display:none\' name=\'TextContents['+id+']['+child_id+']\'></textarea><input type=\'hidden\' name=\'MaterialID['+id+']['+child_id+']\' /><input type=\'hidden\' name=\'Url['+id+']['+child_id+']\' /><i>子菜单'+child_id+'</i></span>');
	$('#firstmenu').attr('value',id);
	$('#childmenu').attr('value',child_id);
	$('#menu_items li').removeClass('current');
	$('#menu_items span').removeClass('curr');
	$('#first_'+id+' #child_'+child_id).addClass('curr');
	
	setformvalue();
	child_id++;
}

//edit child menu
function editchildmenu(ffid,ccid){
	$('#menu_items li').removeClass('current');
	$('#firstmenu').attr('value',ffid);
	$('#childmenu').attr('value',ccid);
	$('#menu_items span').removeClass('curr');
	$('#first_'+ffid+' #child_'+ccid).addClass('curr');
	setformvalue();
}

function deletemenu(){
	var fid=$('#firstmenu').attr('value');
	var cid=$('#childmenu').attr('value');
	if(cid>0){
		var span_count = $('#first_'+fid+' p span').length;
		$('#first_'+fid+' p span').removeClass('curr');
		if(span_count==2){
			$('#child_'+cid).remove();
			$('#childmenu').attr('value','0');
			var p_height = 45 * (span_count-1);
			$('#first_'+fid).addClass('current');
		}else if(span_count==5 && $('#first_'+fid+' p span.child_add_btn').length==0){
			$('#child_'+cid).remove();
			$('#childmenu').attr('value',$('#first_'+fid+' p span').eq(0).attr('id').replace('child_', ''));
			$('#first_'+fid+' p span').eq(0).addClass('curr');
			var p_height = 45 * span_count;
			$('#first_'+fid+' p').eq(0).append('<span class=\'child_add_btn\' onclick=\'addchildmenu('+fid+');\'><font style=\'font-size:18px; font-weight:bold\'>＋</font>');
		}else{
			$('#child_'+cid).remove();
			$('#childmenu').attr('value',$('#first_'+fid+' p span').eq(0).attr('id').replace('child_', ''));
			$('#first_'+fid+' p span').eq(0).addClass('curr');
			var p_height = 45 * (span_count-1);
		}
		$('#first_'+fid+' p').css('height',p_height);
		
	}else{
		if(confirm('是否要删除该菜单及其下子菜单？')){
			var li_count = $('#menu_items li').length;
			if(li_count==2){
				alert('至少要设置一个菜单');
			}else{
				$('#first_'+fid).remove();
				if($('#menu_items li.btn').length>0){
					$('#menu_items li').removeClass('w108').addClass('w162');
					$('#menu_items li').removeClass('current');
					$('#menu_items li').eq(0).addClass('current');
					$('#firstmenu').attr('value',$('#menu_items li').eq(0).attr('id').replace('first_', ''));
					$('#childmenu').attr('value','0');
				}else{
					$('#menu_items li').removeClass('current');
					$('#menu_items li').eq(0).addClass('current');
					$('#firstmenu').attr('value',$('#menu_items li').eq(0).attr('id').replace('first_', ''));
					$('#childmenu').attr('value','0');
					$('#menu_items li').eq(1).after('<li class=\'w108 btn\'><a href=\'Javascript:addfirstmenu();\'><font style=\'font-size:14px; font-weight:bold\'>＋</font>添加菜单</a></li>');
				}
			}
		}
	}
	setformvalue();
}
</script>