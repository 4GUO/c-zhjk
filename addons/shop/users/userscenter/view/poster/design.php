<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<style>
.waybill_area {
	margin: 10px auto;
	width: <?php echo $output['cert_info']['cert_width'];?>px;
	height: <?php echo $output['cert_info']['cert_height'];?>px;
	position: relative;
	z-index: 1;
}
.waybill_back {
	position: relative;
	width: <?php echo $output['cert_info']['cert_width'];?>px;
	height: <?php  echo $output ['cert_info'] ['cert_height'];?>px;
}
.waybill_back img {
	width: <?php echo$output['cert_info']['cert_width'];?>px;
	height: <?php  echo $output ['cert_info'] ['cert_height'];?>px;
}
.waybill_design {
	font-size: 18px;
	position: absolute;
	left: 0;
	top: 0;
	width: <?php echo$output['cert_info']['cert_width'];?>px;
	height: <?php  echo $output ['cert_info'] ['cert_height'];?>px;
	border: solid 3px #999999;
}

.ncsc-form-default {
	
}

.ncsc-form-default h3 {
	font-size: 12px;
	font-weight: 600;
	line-height: 22px;
	color: #555;
	clear: both;
	background-color: #F5F5F5;
	padding: 5px 0 5px 12px;
	border-bottom: solid 1px #E7E7E7;
}

.ncsc-form-default dl {
	font-size: 0;
	*word-spacing: -1px /*IE6、7*/;
	line-height: 20px;
	clear: both;
	padding: 0;
	margin: 0;
	border-bottom: dotted 1px #E6E6E6;
	overflow: hidden;
}

.ncsc-form-default dl:hover {
	background-color: #FCFCFC;
}

.ncsc-form-default dl:hover .hint {
	color: #666;
}

.ncsc-form-default dl.bottom {
	border-bottom-width: 0px;
}

.ncsc-form-default dl dt {
	font-size: 12px;
	line-height: 32px;
	vertical-align: top;
	letter-spacing: normal;
	word-spacing: normal;
	text-align: right;
	display: inline-block;
	width: 19%;
	padding: 10px 1% 10px 0;
	margin: 0;
}

.ncsc-form-default dl dt {
	*display: inline /*IE6,7*/;
}

.ncsc-form-default dl dt i.required {
	font: 12px/16px Tahoma;
	color: #F30;
	vertical-align: middle;
	margin-right: 4px;
}

.ncsc-form-default dl dd {
	font-size: 12px;
	line-height: 32px;
	vertical-align: top;
	letter-spacing: normal;
	word-spacing: normal;
	display: inline-block;
	width: 79%;
	padding: 10px 0 10px 0;
}

.ncsc-form-default dl dd {
	*display: inline /*IE6,7*/;
	zoom: 1;
}

.ncsc-form-default dl dd span {
	*line-height: 20px;
	*display: inline;
	*height: 20px;
	*margin-top: 6px;
	*zoom: 1;
}

.ncsc-form-default dl dd p {
	clear: both;
}

.ncsc-form-default div.bottom {
	text-align: center;
	margin:10px 0;
}

.ncsc-form-radio-list, .ncsc-form-checkbox-list {
	font-size: 0;
	*word-spacing: -1px /*IE6、7*/;
}

.ncsc-form-radio-list li, .ncsc-form-checkbox-list li {
	font-size: 12px;
	vertical-align: top;
	letter-spacing: normal;
	word-spacing: normal;
	display: inline-block;
	*display: inline /*IE6,7*/;
	margin-right: 30px;
	*zoom: 1 /*IE6,7*/;
}

.ncsc-form-checkbox-list li {
	width: 20%;
	margin: 0;
}
</style>
<div class='alert alert-block mt10'>
	<ul class='mt5'>
		<li>1、勾选需要显示的项目，勾选后可以用鼠标拖动确定项目的位置、宽度和高度</li>
		<li>2、设置完成后点击提交按钮完成设计</li>
	</ul>
</div>
<div class='ncsc-form-default'>
	<dl>
		<dt>选择显示项：</dt>
		<dd>
			<form id='design_form' action='<?=users_url('poster/design')?>' method='post'>
				<input name='form_submit' value='ok' type='hidden' />
				<input type='hidden' name='id' value='<?php echo $output['cert_info']['id'];?>'>
				<ul id='waybill_item_list' class='ncsc-form-checkbox-list'>
					<?php if(!empty($output['item_list']) && is_array($output['item_list'])) {?>
					<?php foreach($output['item_list'] as $key => $value) {?>
					<li>
						<input id='check_<?php echo $key;?>' class='checkbox' type='checkbox' name='waybill_data[<?php echo $key;?>][check]' data-waybill-name='<?php echo $key;?>' data-waybill-text='<?php echo $value['item_text'];?>' <?php echo $value['check'];?>>
						<label for='check_<?php echo $key;?>' class='label'><?php echo $value['item_text'];?></label>						
						<input id='left_<?php echo $key;?>' type='hidden' name='waybill_data[<?php echo $key;?>][left]' value='<?php echo $value['left'];?>'>
						<input id='top_<?php echo $key;?>' type='hidden' name='waybill_data[<?php echo $key;?>][top]' value='<?php echo $value['top'];?>'>
						<input id='width_<?php echo $key;?>' type='hidden' name='waybill_data[<?php echo $key;?>][width]' value='<?php echo $value['width'];?>'>
						<input id='height_<?php echo $key;?>' type='hidden' name='waybill_data[<?php echo $key;?>][height]' value='<?php echo $value['height'];?>'>
					</li>
					<?php } ?>
					<?php } ?>
				</ul>
			</form>
		</dd>
	</dl>
	<dl>
		<dt>打印项偏移校正：</dt>
	</dl>
	<div>
		<div class='waybill_area'>
			<div class='waybill_back'> <img src='<?php echo $output['cert_info']['cert_image'];?>' alt=''> </div>
			<div class='waybill_design'>
				<?php if(!empty($output['info_data']) && is_array($output['info_data'])) {?>
				<?php foreach($output['info_data'] as $key=>$waybill_data) {?>
				<?php if(!empty($waybill_data['check'])) { ?>
				<div id='div_<?php echo $key;?>' data-item-name='<?php echo $key;?>' class='waybill_item' style='position: absolute;width:<?php echo $waybill_data['width'];?>px;height:<?php echo $waybill_data['height'];?>px;left:<?php echo $waybill_data['left'];?>px;top:<?php echo $waybill_data['top'];?>px;'><?php echo $waybill_data['item_text'];?></div>
				<?php } ?>
				<?php } ?>
				<?php } ?>
			</div>
		</div>
	</div>
	<div class='bottom'>
		<label class='submit-border'>
			<input id='submit' type='submit' class='submit' value='确定提交'>
		</label>
	</div>
</div>
<script type='text/javascript' src='<?=STATIC_URL?>/js/jquery-ui/jquery-ui.min.js'></script>
<link type='text/css' rel='stylesheet' href='<?=STATIC_URL?>/js/jquery-ui/jquery-ui.min.css' />
<script type='text/javascript'>
$(document).ready(function() {
    var draggable_event = {
        stop: function(event, ui) {
            var item_name = ui.helper.attr('data-item-name');
            var position = ui.helper.position();
            $('#left_' + item_name).val(position.left);
            $('#top_' + item_name).val(position.top);
        }
    };

    var resizeable_event = {
        stop: function(event, ui) {
            var item_name = ui.helper.attr('data-item-name');
            $('#width_' + item_name).val(ui.size.width);
            $('#height_' + item_name).val(ui.size.height);
        }
    };

    $('.waybill_item').draggable(draggable_event);
    $('.waybill_item').resizable(resizeable_event);

    $('#waybill_item_list input:checkbox').on('click', function() {
        var item_name = $(this).attr('data-waybill-name');
        var div_name = 'div_' + item_name;
        if($(this).prop('checked')) {
            var item_text = $(this).attr('data-waybill-text');
            var waybill_item = '<div id=\'' + div_name + '\' data-item-name=\'' + item_name + '\' class=\'waybill_item\'>' + item_text + '</div>';
            $('.waybill_design').append(waybill_item);
            $('#' + div_name).draggable(draggable_event);
            $('#' + div_name).resizable(resizeable_event);
            $('#left_' + item_name).val('0');
            $('#top_' + item_name).val('0');
            $('#width_' + item_name).val('100');
            $('#height_' + item_name).val('20');
        } else {
            $('#' + div_name).remove();
        }
    });
	
    $('#submit').on('click', function() {
        ajax_form_post('design_form');
    });
});
</script>