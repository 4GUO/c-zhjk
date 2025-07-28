<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv='content-type' content='text/html; charset=utf-8' />
<title></title>
<style>
	body {
		margin: 0;
	}
	.waybill_area {
		position: relative;
		 width: <?php echo $output['waybill_info']['waybill_pixel_width'];
		?> px;
		 height: <?php echo $output ['waybill_info'] ['waybill_pixel_height'];
		 ?> px;
	}
	.waybill_back {
		position: relative;
		width: <?php echo $output['waybill_info']['waybill_pixel_width'];
		?> px;
		height: <?php echo $output ['waybill_info'] ['waybill_pixel_height'];
		 ?> px;
	}
	.waybill_back img {
		width: <?php echo $output['waybill_info']['waybill_pixel_width'];
		?> px;
		height: <?php echo $output ['waybill_info'] ['waybill_pixel_height'];
		?> px;
	}
	.waybill_design {
		position: absolute;
		left: 0;
		top: 0;
		width: <?php echo $output['waybill_info']['waybill_pixel_width'];
		?> px;
		height: <?php echo $output ['waybill_info'] ['waybill_pixel_height'];
		?> px;
	}
	.waybill_item {
		font-size: 18px;
		position: absolute;
		left: 0;
		top: 0;
		width: 100px;
		height: 20px;
	}
</style>
</head>
<body>
<div class='waybill_back'> <img src='<?php echo $output['waybill_info']['waybill_image'];?>' alt=''> </div>
<div class='waybill_design'>
  <?php if(!empty($output['waybill_info']['waybill_data']) && is_array($output['waybill_info']['waybill_data'])) {?>
  <?php foreach($output['waybill_info']['waybill_data'] as $key=>$value) {?>
  <?php if((isset($value['check']) && $value['check'])) {?>
  <div class='waybill_item' style='left:<?php echo $value['left'];?>px; top:<?php echo $value['top'];?>px; width:<?php echo $value['width'];?>px; height:<?php echo $value['height'];?>px;'><?php echo $value['content'];?></div>
  <?php } ?>
  <?php } ?>
  <?php } ?>
</div>
<script>
	$(document).ready(function() {
		$('#btn_print').on('click', function() {
			pos();

			$('.waybill_back').hide();
			$('.control').hide();
			window.print();
		});

		var pos = function () {
			var top = <?php echo $output['waybill_info']['waybill_pixel_top'];?>;
			var left = <?php echo $output['waybill_info']['waybill_pixel_left'];?>;
			$('.waybill_design').each(function(index) {
				var offset = $(this).offset();
				var offset_top = offset.top + top;
				var offset_left = offset.left + left;
				$(this).offset({ top: offset_top, left: offset_left})
			});
		};

		//更换模板
		$('#btn_change').on('click', function() {
			var store_waybill_id = $('#waybill_list').val(); 
			var url = document.URL + '&store_waybill_id=' + store_waybill_id;
			window.location.href = url;
		});
	});
</script>
</body>
</html>
