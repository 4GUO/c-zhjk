<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<style>
	.css-form-default h3 {
		font-size: 12px;
		font-weight: 600;
		line-height: 22px;
		color: #555;
		clear: both;
		background-color: #F5F5F5;
		padding: 5px 0 5px 12px;
		border-bottom: solid 1px #E7E7E7;
	}
	a.ncbtn-mini, a.ncbtn {
		font: normal 12px/20px 'microsoft yahei', arial;
		color: #FFF;
		background-color: #CCD0D9;
		text-align: center;
		vertical-align: middle;
		display: inline-block;
		*display: inline;
		height: 20px;
		padding: 5px 10px;
		border-radius: 3px;
		cursor: pointer;
		*zoom: 1;
	}
	a.ncbtn-mini {
		line-height: 16px;
		height: 16px;
		padding: 3px 7px;
		border-radius: 2px;
	}
</style>
<table class='css-default-table'>
	<thead>
		<tr>
			<th class='w250'>代金券编码</th>
			<th class='w250'>卡密</th>
			<th class=''>使用状态</th>
			<th class=''>所属会员</th>
			<th class=''>领取时间</th>
		</tr>
	</thead>
	<tbody id='list'>
		
	</tbody>
	<tfoot>
		<tr>
			<td colspan='20'><div class='pagination'></div></td>
		</tr>
	</tfoot>
</table>
<script>
var tid = <?= input('tid', 0, 'intval');?>;
function select_page(url, data){
	getAjax(url, data, function(e) {
		if (e.state == 200) {
			var t_info = e.data['t_info'];
			var list_data = e.data['list'];
			if (list_data.length > 0) {
				var $html = '';
				$.each(list_data, function(i){
					v = list_data[i];
					$html += '<tr class=\'bd-line\'>'+
						'<td>'+v['voucher_code']+'</td>'+
						'<td>'+v['voucher_pwd']+'</td>'+
						'<td>'+v['voucher_state_text']+'</td>'+
						'<td>'+(v['voucher_owner_name'] ? v['voucher_owner_name'] : '<font style=\'color: #5BB75B;\'>未领取</font>')+'</td>'+
						'<td>'+v['voucher_active_date']+'</td>'+
					'</tr>';
				})
			} else {
				var $html = '<tr><td colspan=\'20\' class=\'norecord\'><div class=\'warning-option\'><i class=\'icon-warning-sign\'></i><span>暂无记录</span></div></td></tr>';
			}
			$('#list').html($html);
			$('.pagination').html(e.data.page_html);
		}
	});
}
	
$(function(){
	select_page('<?=_url('store_voucher/voucherlist')?>', {page: 1, tid: tid, form_submit: 'ok'});
});
</script>