<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<style type='text/css'>
.items_info img{
	width:60px;
	border-radius: 5px;
}
.items_info strong{
	padding-left: 8px
}
</style>
<div class='item-publish'>
	<div class='css-form-goods'>
		<dl>
			<dt>会员信息：</dt>
			<dd>
				<div class='items_info'>
					<?php echo !empty($output['member_info']['headimg']) ? '<img src=\''.$output['member_info']['headimg'].'\' width=\'60\' />' : ''?>
					<strong><?php echo !empty($output['member_info']['nickname']) ? $output['member_info']['nickname'] : ''?></strong>
				</div>
			</dd>
		</dl>
		<dl>
			<dt>姓名：</dt>
			<dd>
				<?=$output['member_info']['truename']?>				
			</dd>
		</dl>
		<dl>
			<dt>手机号：</dt>
			<dd>
				<?=$output['member_info']['mobile']?>				
			</dd>
		</dl>
		<dl>
			<dt>微信号：</dt>
			<dd>
				<?=$output['member_info']['wechat']?>				
			</dd>
		</dl>
		<dl>
			<dt>身份证号：</dt>
			<dd>
				<?=$output['member_info']['idcode']?>				
			</dd>
		</dl>
		<dl>
			<dt>用户状态：</dt>
			<dd>
				<?=$output['member_info']['status'] == 0 ? '待审核' : ''?>
				<?=$output['member_info']['status'] == 1 ? '正常' : ''?>
				<?=$output['member_info']['status'] == 2 ? '禁用' : ''?>
			</dd>
		</dl>
	</div>
</div>
<script>
	$('#member_form').validate({
        errorLabelContainer: $('#warning'),
        invalidHandler: function(form, validator) {
               $('#warning').show();
        },
    	onkeyup: false,
        rules : {
            truename : {
                required : true
            },
            mobile : {
                required: true,
                mobile: true
            },
			wechat : {
				required : true
			}
        },
        messages : {
            truename : {
                required : '<i class=\'icon-exclamation-sign\'></i>姓名不能为空'

            },
			mobile : {
                required: '手机号不能为空！',
                mobile: '手机号码不正确'
            },
            wechat  : {
                required   : '<i class=\'icon-exclamation-sign\'></i>微信号不能为空'
            }
        }
    });
	$('.submit').click(function(e){
		if($('#member_form').valid()){
			ajax_form_post('member_form');
		};
	});
</script>