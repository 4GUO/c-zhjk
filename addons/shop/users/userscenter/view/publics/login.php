<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<style>
	body {
		margin: 0;
		overflow: hidden;
		background: linear-gradient(to bottom, #53e3a6, #50a3a2);
		background-size:1400% 300%;
		animation: dynamics 6s ease infinite;
		-webkit-animation: dynamics 6s ease infinite;
		-moz-animation: dynamics 6s ease infinite;
		font-size: 14px;
		color: #ffffff;
		min-height: 700px;
		/*background-image: url(<?=STATIC_URL?>/admin/images/login/bg20220717.jpg);*/
		background-image: url(<?=STATIC_URL?>/admin/images/login/video_bg.mp4);
		background-repeat: no-repeat;
		background-size: 100% 100%;
		background-position: center center;
	}
	.video_bg {
        height: 100%;
        margin: 0px;
        object-fit: cover;
        padding: 0px;
        position: absolute;
        z-index: 1;
        width: 100%;
    }
</style>
<video class='video_bg' autoplay loop muted src='<?=STATIC_URL?>/admin/images/login/video_bg.mp4'></video>
<script language='JavaScript' type='text/javascript'>
$(document).ready(function() {
	change_seccode();
    //更换验证码
    function change_seccode() {
		getAjax('<?=users_url('publics/makecodekey')?>', {myhash: $('input[name=myhash]').val()}, function(e){
			$('#codeimage').attr('src', '<?=users_url('publics/makecode')?>?seccode=' + e.data.seccode + '&t=' + Math.random());
			$('input[name=seccode_key]').val(e.data.seccode_key);
			$('#captcha').select();
		});
    }

    $('#codeimage').on('click', function() {
        change_seccode();
    });

    //登陆表单验证
    $('#form_login').validate({
        errorPlacement:function(error, element) {
			console.log(error);
            showError(error[0].textContent);
        },
        onkeyup: false,
        rules:{
            Account:{
                required:true
            },
            Password:{
                required:true
            },
            captcha:{
                required:true,
                /*remote:{
                    url:'<?=users_url('publics/makecode',['myhash'=>getUrlhash()])?>',
                    type:'get',
                    data:{
                        captcha:function() {
                            return $('#captcha').val();
                        }
                    },
                    complete: function(data) {
                        if(data.responseText == 'false') {
                            change_seccode();
                        }
                    }
                }*/
            }
        },
        messages:{
            Account:{
                required:'用户名不能为空'
            },
            Password:{
                required:'密码不能为空'
            },
            captcha:{
                required:'验证码不能为空',
                //remote:'<i class=\'icon-frown\'></i>验证码错误'
            }
        }
    });
	//提交登录
	$('#login-submit').click(function(e){
		if($('#form_login').valid()){
			loading('loading...');
			postAjax('<?=users_url('publics/login')?>', $('#form_login').serialize(), function(e){
				if(e.state == 200){
					window.top.location = '<?=users_url('index/index')?>';
				}else{
					showError(e.msg);
				};
				loading_hide();
				change_seccode();
			});
			loading_hide();
		};
	});
	// 回车提交表单
	$('#form_login').keydown(function(event){
		if (event.keyCode == 13) {
			$('#login-submit').click();
		}
	});
});
</script>
<form id='form_login'>
	<div class='login_main' style='z-index: 10'>
		<div class='login'>
			<div class='log-con'>
				<span>管理员登录</span>
				<input type='hidden' name='<?=$output['token_name']?>' value='<?=$output['token_value']?>' />
				<input type='hidden' name='form_submit' value='ok' />
				<input name='myhash' type='hidden' value='<?=getUrlhash()?>' />
				<input name='seccode_key' type='hidden' value='' />
				<input type='text' class='name' placeholder='请输入用户名' name='Account' />
				<input type='password' class='password' placeholder='请输入密码' name='Password' />
				<input type='text' class='code' placeholder='请输入验证码' name='captcha' id='captcha' />
				<img src='' name='codeimage' border='0' id='codeimage' />
				<a id='login-submit'>立即登录</a>
			</div>
		</div>
	</div>
</form>