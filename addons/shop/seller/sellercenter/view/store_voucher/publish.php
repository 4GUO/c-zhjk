<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='css-form-default'>
	<form id='form' method='post' target='_parent' action='<?=_url('store_voucher/publish')?>'>
		<input type='hidden' id='form_submit' name='form_submit' value='ok'/>
		<input type='hidden' id='tid' name='tid' value='<?php echo isset($output['t_info']['voucher_t_id']) ? $output['t_info']['voucher_t_id'] : 0;?>'/>
		<dl>
			<dt><i class='required'>*</i>代金券名称：</dt>
			<dd>
				<input type='text' class='w300 text' name='txt_template_title' value='<?php echo isset($output['t_info']['voucher_t_title']) ? $output['t_info']['voucher_t_title'] : '';?>' maxlength=50 />
				<span></span> 
			</dd>
		</dl>

		<dl>
			<dt><i class='required'>*</i>领取方式：</dt>
			<dd>
				<select name='gettype_sel' id='gettype_sel'>
					<option value=''>请选择</option>
					<?php foreach($output['gettype_arr'] as $k => $v) { ?>
					<option value='<?php echo $k; ?>' <?php echo isset($output['t_info']['voucher_t_gettype']) && ($output['t_info']['voucher_t_gettype'] == $v['sign']) ? 'selected' : ''; ?>><?php echo $v['name']; ?></option>
					<?php } ?>
				</select>
				<span></span>
				<p class='hint'>“积分兑换”时会员可以在积分中心用积分进行兑换；“卡密兑换”时会员需要在“个人中心——我的代金券”中输入卡密获得代金券；<br>“免费领取”时会员可以点击店铺的代金券推广广告领取代金券。</p>
			</dd>
		</dl>
		<dl>
			<dt><em class='pngFix'></em>有效期：</dt>
			<dd>
				<input type='text' class='text w100' id='txt_template_enddate' name='txt_template_enddate' value='<?php echo !empty($output['t_info']['voucher_t_end_date']) ? date('Y-m-d H:i', $output['t_info']['voucher_t_end_date']) : '';?>' readonly />
				<em class='add-on'><i class='icon-calendar'></i></em> 
				<span></span>
				<p class='hint'>留空则默认30天之后到期</p>
			</dd>
		</dl>
		<dl>
			<dt>面额：</dt>
			<dd>
				<select id='select_template_price' name='select_template_price' class='vt'>
					<?php if(!empty($output['pricelist'])) { ?>
					<?php foreach($output['pricelist'] as $voucher_price) { ?>
					<option value='<?php echo $voucher_price['voucher_price'];?>' <?php echo isset($output['t_info']['voucher_t_price']) && ($output['t_info']['voucher_t_price'] == $voucher_price['voucher_price']) ? 'selected' : '';?>><?php echo $voucher_price['voucher_price'];?></option>
					<?php } ?>
					<?php } ?>
				</select>
				<em class='add-on'><i class='icon-renminbi'></i></em> <span></span> 
			</dd>
		</dl>
		<dl>
			<dt><i class='required'>*</i>可发放总数：</dt>
			<dd>
				<input type='text' class='w70 text' name='txt_template_total' id='txt_template_total' value='<?php echo isset($output['t_info']['voucher_t_total']) ? $output['t_info']['voucher_t_total'] : ''; ?>'>
				<span></span>
				<p class='hint'>如果代金券领取方式为卡密兑换，则发放总数应为1~1000之间的整数</p>
			</dd>
		</dl>
		<dl id='eachlimit_dl'>
			<dt><i class='required'>*</i>每人限领：</dt>
			<dd>
				<select name='eachlimit'>
					<option value='0'>不限</option>
					<?php for($i = 1; $i <= intval($output['config']['promotion_voucher_buyertimes_limit']); $i++) { ?>
					<option value='<?php echo $i;?>' <?php echo isset($output['t_info']['voucher_t_eachlimit']) && ($output['t_info']['voucher_t_eachlimit'] == $i) ? 'selected' : '';?>><?php echo $i;?>张</option>
					<?php } ?>
				</select>
			</dd>
		</dl>
		<dl>
			<dt><i class='required'>*</i>消费金额：</dt>
			<dd>
				<input type='text' name='txt_template_limit' class='text w70' value='<?php echo isset($output['t_info']['voucher_t_limit']) ? $output['t_info']['voucher_t_limit'] : '';?>'>
				<em class='add-on'><i class='icon-renminbi'></i></em> <span></span>
				<p class='hint'>如果消费金额设置为0，则表示不限制使用代金券的消费金额</p>
			</dd>
		</dl>
		<dl id='mgrade_dl'>
			<dt>会员级别：</dt>
			<dd>
				<select name='mgrade_limit'>
					<?php if ($output['member_grade']) { ?>
					<?php foreach ($output['member_grade'] as $k => $v) { ?>
					<option value='<?php echo $v['id'];?>' <?php echo isset($output['t_info']['voucher_t_mgradelimit']) && ($output['t_info']['voucher_t_mgradelimit'] == $v['id']) ? 'selected' : '';?>><?php echo $v['level_name'];?></option>
					<?php } ?>
					<?php } ?>
				</select>
				<p class='hint'>当会员兑换代金券时，需要达到该级别或者以上级别后才能兑换领取</p>
			</dd>
		</dl>
		<dl>
			<dt><i class='required'>*</i>代金券描述：</dt>
			<dd>
				<textarea  name='txt_template_describe' class='textarea w400 h600'><?php echo isset($output['t_info']['voucher_t_desc']) ? $output['t_info']['voucher_t_desc'] : '';?></textarea>
				<span></span> 
			</dd>
		</dl>
		<dl>
			<dt>代金券图片：</dt>
			<dd>
				<div class='css-goods-default-pic'>
					<div class='goodspic-uplaod'>
						<div class='upload-thumb'> <img nctype='voucher_t_customimg' src='<?=!empty($output['t_info']['voucher_t_customimg']) ? $output['t_info']['voucher_t_customimg'] : STATIC_URL . '/images/default_image.png'?>'> </div>
						<input name='voucher_t_customimg' nctype='voucher_t_customimg' value='<?=!empty($output['t_info']['voucher_t_customimg']) ? $output['t_info']['voucher_t_customimg'] : ''?>' type='hidden' />
						<span></span>
						<p class='hint'><font color='red'>大小不超过2M的图片</font>，上传后的图片将会自动保存在图片空间的默认分类中。</p>
						<div class='handle'>
							<div class='css-upload-btn'> 
							    <a href='javascript:void(0);'>
									<span>
										<input hidefocus='true' size='1' class='input-file' name='voucher_t_customimg' id='voucher_t_customimg' type='file' />
									</span>
									<p><i class='icon-upload-alt'></i>图片上传</p>
								</a> 
							</div>
							<a class='css-btn mt5' nc_type='dialog' dialog_title='选择图片' dialog_id='voucher_t_customimg' dialog_width='830' dialog_height='550' uri='<?=_url('album/pic_list_view', array('item' => 'voucher_t_customimg'))?>'><i class='icon-picture'></i>从图片空间选择</a> 
						</div>
					</div>
				</div>
				<div id='demo'></div>
			</dd>
		</dl>
		<dl>
			<dt><em class='pngFix'></em>状态：</dt>
			<dd>
				<label><input type='radio' value='<?php echo $output['templatestate_arr']['usable'][0];?>' name='tstate' <?php echo !empty($output['t_info']['voucher_t_state']) && ($output['t_info']['voucher_t_state'] == $output['templatestate_arr']['usable'][0]) ? 'checked' : '';?> />&nbsp;<?php echo $output['templatestate_arr']['usable'][1];?></label>
				<label><input type='radio' value='<?php echo $output['templatestate_arr']['disabled'][0];?>' name='tstate' <?php echo !empty($output['t_info']['voucher_t_state']) && ($output['t_info']['voucher_t_state'] == $output['templatestate_arr']['disabled'][0]) ? 'checked' : '';?> />&nbsp;<?php echo $output['templatestate_arr']['disabled'][1];?></label>
			</dd>
		</dl>
		<div class='bottom'>
			<label class='submit-border'> <a id='btn_add' class='submit' href='javascript:void(0);'>提交</a> </label>
		</div>
	</form>
</div>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.iframe-transport.js' charset='utf-8'></script>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.ui.widget.js' charset='utf-8'></script>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.fileupload.js' charset='utf-8'></script>
<link rel='stylesheet' type='text/css' href='<?=STATIC_URL?>/js/datetimepicker/jquery.datetimepicker.css'/>
<script type='text/javascript' src='<?=STATIC_URL?>/js/datetimepicker/jquery.datetimepicker.js'></script>
<script>
$('#txt_template_enddate').datetimepicker({
	lang: 'ch',
	datepicker: true,
	format: 'Y-m-d H:i',
	formatDate: 'Y-m-d H:i',
	step: 5
});

function showcontent(choose_gettype) {
	if (choose_gettype == 'pwd') {
		$('#eachlimit_dl').hide();
		$('#mgrade_dl').hide();
	} else {
		$('#eachlimit_dl').show();
		$('#mgrade_dl').show();
	}
}

$(function() {
	showcontent('<?php echo isset($output['t_info']['voucher_t_gettype_key']) ? $output['t_info']['voucher_t_gettype_key'] : ''; ?>');
	$('#gettype_sel').change(function() {
		var choose_gettype = $('#gettype_sel').val();
		showcontent(choose_gettype);
	});
	upload_file('voucher_t_customimg', '<?=_url('album/image_upload')?>');
	$('.submit').click(function(e) {
		var choose_gettype = $('#gettype_sel').val();
		if (choose_gettype == 'pwd') {
			var template_total = parseInt($('#txt_template_total').val());
			if (template_total > 1000) {
				showError('领取方式为卡密兑换的代金券，发放总数不能超过1000张');
				return false;
            }
		}
		ajax_form_post('form');
	});
});
</script>