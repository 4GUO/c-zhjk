<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='css-form-default'>
	<dl>
		<dt>代金券名称：</dt>
		<dd>
			<?php echo isset($output['t_info']['voucher_t_title']) ? $output['t_info']['voucher_t_title'] : '';?>
		</dd>
	</dl>

	<dl>
		<dt>领取方式：</dt>
		<dd>
			<?php echo isset($output['t_info']['voucher_t_gettype_text']) ? $output['t_info']['voucher_t_gettype_text'] : '';?>
		</dd>
	</dl>
	<dl>
		<dt><em class='pngFix'></em>有效期：</dt>
		<dd>
			<?php echo !empty($output['t_info']['voucher_t_end_date']) ? date('Y-m-d H:i', $output['t_info']['voucher_t_end_date']) : '';?>
		</dd>
	</dl>
	<dl>
		<dt>面额：</dt>
		<dd>
			<?php echo !empty($output['t_info']['voucher_t_price']) ? $output['t_info']['voucher_t_price'] : '';?>
		</dd>
	</dl>
	<dl>
		<dt>可发放总数：</dt>
		<dd>
			<?php echo isset($output['t_info']['voucher_t_total']) ? $output['t_info']['voucher_t_total'] : ''; ?>
		</dd>
	</dl>
	<dl id='eachlimit_dl'>
		<dt>每人限领：</dt>
		<dd>
			<?php echo isset($output['t_info']['voucher_t_eachlimit']) ? $output['t_info']['voucher_t_eachlimit'] : ''; ?>
		</dd>
	</dl>
	<dl>
		<dt>消费金额：</dt>
		<dd>
			<?php echo isset($output['t_info']['voucher_t_limit']) ? $output['t_info']['voucher_t_limit'] : '';?>
		</dd>
	</dl>
	<dl id='mgrade_dl'>
		<dt>会员级别：</dt>
		<dd>
			<?php echo isset($output['member_grade'][$output['t_info']['voucher_t_mgradelimit']]['level_name']) ? $output['member_grade'][$output['t_info']['voucher_t_mgradelimit']]['level_name'] : '';?>
		</dd>
	</dl>
	<dl>
		<dt>代金券描述：</dt>
		<dd>
			<?php echo isset($output['t_info']['voucher_t_desc']) ? $output['t_info']['voucher_t_desc'] : '';?>
		</dd>
	</dl>
	<dl>
		<dt>代金券图片：</dt>
		<dd>
			<div class='css-goods-default-pic'>
				<div class='goodspic-uplaod'>
					<div class='upload-thumb'> <img nctype='voucher_t_customimg' src='<?=!empty($output['t_info']['voucher_t_customimg']) ? $output['t_info']['voucher_t_customimg'] : STATIC_URL . '/images/default_image.png'?>'> </div>
				</div>
			</div>
		</dd>
	</dl>
	<dl>
		<dt><em class='pngFix'></em>状态：</dt>
		<dd>
			<?php echo isset($output['t_info']['voucher_t_state_text']) ? $output['t_info']['voucher_t_state_text'] : '';?>
		</dd>
	</dl>
	<dl>
		<dt><em class='pngFix'></em>已领取</dt>
		<dd><?php echo $output['t_info']['voucher_t_giveout'];?>&nbsp;张</dd>
	</dl>
	<dl>
		<dt><em class='pngFix'></em>已使用</dt>
		<dd><?php echo $output['t_info']['voucher_t_used'];?>&nbsp;张</dd>
	</dl>
</div>
<?php if(($output['t_info']['voucher_t_gettype'] == 2 && $output['t_info']['voucher_t_isbuild'] == 1) || $output['t_info']['voucher_t_giveout'] > 0) { ?>
	<div class='css-form-default'>
		<h3>已生成代金券 <a id='voucher_exportbtn' class='ncbtn-mini' href='javascript:void(0);' onclick='go_excel(this)' uri='<?php echo _url('store_voucher/voucher_export', array('tid' => $output['t_info']['voucher_t_id']));?>' title='导出Excel' style='float: right; margin-right: 10px;'>导出Excel</a> </h3>
	</div>
	<div id='voucher_list_div'></div>
	<script type='text/javascript'>
		$('#voucher_list_div').load('<?php echo _url('store_voucher/voucherlist', array('tid' => $output['t_info']['voucher_t_id']));?>');
		function go_excel(that) {
			self.location = $(that).attr('uri');
		}
	</script>
<?php } ?>