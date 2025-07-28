<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<style type='text/css'>
#my_agent .member_info{
	height:20px;
	line-height:20px;
	color:#333;
}
#my_agent .member_info span{
	color:#999
}
.send_btn{padding: 5px 15px; border-radius: 8px; background: #51A351; color:#FFF}
</style>
<div class='tabmenu'>
	<ul class='tab pngFix'>
		<li class='normal'>
			<a href='<?=users_url('shop_vr_order/index')?>'>所有订单</a>
		</li>
		<li class='normal'>
			<a href='<?=users_url('shop_vr_order/index', array('state_type' => 'state_new'))?>'>待付款</a>
		</li>
		<li class='normal'>
			<a href='<?=users_url('shop_vr_order/index', array('state_type' => 'state_pay'))?>'>已付款</a>
		</li>
		<li class='normal'>
			<a href='<?=users_url('shop_vr_order/index', array('state_type' => 'state_success'))?>'>已完成</a>
		</li>
		<li class='normal'>
			<a href='<?=users_url('shop_vr_order/index', array('state_type' => 'state_cancel'))?>'>已取消</a>
		</li>
		<li class='active'>
			<a href='<?=users_url('shop_vr_order/exchange')?>'>兑换码兑换</a>
		</li>
	</ul>
</div>
<div class='css-vr-order-exchange'>
	<dl>
		<dt>
			<h3>电子兑换码</h3>
		</dt>
		<dd>
			<input class='vr-code' name='vr_code' type='text' id='vr_code' placeholder='请输入买家提供的电子兑换码' maxlength='18'  />
			<span></span>
			<div class='css-keyboard'>
				<button onclick='demo(this,1)'>1</button>
				<button onclick='demo(this,1)'>2</button>
				<button onclick='demo(this,1)'>3</button>
				<button onclick='demo(this,1)'>4</button>
				<button onclick='demo(this,1)'>5</button>
				<button onclick='demo(this,1)'>6</button>
				<button onclick='demo(this,1)'>7</button>
				<button onclick='demo(this,1)'>8</button>
				<button onclick='demo(this,1)'>9</button>
				<button onclick='demo(this,1)'>0</button>
				<button class='cn' onclick='demo(this,2)'>清除</button>
				<button class='cn' onclick='demo(this,3)'>后退</button>
				<label class='enter-border'>
					<input type='button' id='_submit' class='enter' value='提交验证'>
				</label>
			</div>
			<p class='hint'>请输入买家提供的兑换码，核对无误后提交，每个兑换码抵消单笔消费。</p>
		</dd>
	</dl>
	<div class='bottom'> </div>
</div>
<script>
    function demo(obj, tip) {
        if (tip == 1) {
            var con = document.getElementById('vr_code').value;
            document.getElementById('vr_code').value = con + obj.innerHTML;
        } else if(tip == 2) {
            document.getElementById('vr_code').value = '';
        } else if (tip == 3) {
            var con = document.getElementById('vr_code').value;
            document.getElementById('vr_code').value = con.slice(0, -1);
        }
    }
	$(document).ready(function() {
		function exPost() {
			getAjax('<?=users_url('shop_vr_order/exchange')?>', {form_submit: 'ok', vr_code: $('#vr_code').val()}, function(e) {
				if (e.state == 200) {
					$('#vr_code').val('').focus();
					showSucc('兑换成功');
				} else {
					showError(e.msg);
					return false;
				}
			});
		}
		$('#_submit').on('click', function() {
			exPost();
		});
		$(document).keydown(function(e) {
			if (e.keyCode == 13) {
				exPost();
			}
		});
	});
</script> 