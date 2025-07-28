<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
	<head>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
		<meta name='viewport' content='width=device-width, initial-scale=1.0'>
		<title><?=$output['title']?></title>
		<script>var STATICURL = '<?=STATIC_URL?>';</script>
		<script type='text/javascript' src='<?=STATIC_URL?>/js/jquery-1.11.1.min.js'></script>
		<script type='text/javascript' src='<?=STATIC_URL?>/js/dialog/dialog.js' id='dialog_js' charset='utf-8'></script>
		<script type='text/javascript' src='<?=STATIC_URL?>/js/global.js'></script>
		<script type='text/javascript' src='<?=STATIC_URL?>/admin/js/global.js'></script>
		<script type='text/javascript' src='<?=STATIC_URL?>/shop/js/base.js?t=<?=time()?>'></script>
		<style>
		    input {
		        display: inline-block;
		    }
		    #captcha {
		        display: inline-block;
		    }
		    #codeimage {
		        display: inline-block;
		    }
		    #submit {
		        display: block;
		        text-align: center;
		        margin-top: 20px;
		    }
		</style>
	</head>

	<body>
        <form id='form'>
        	<input type='hidden' name='<?=$output['token_name']?>' value='<?=$output['token_value']?>' />
        	<input type='hidden' name='formid' value='<?=$output['modelInfo']['id']?>' />
        	<?php foreach($output['fieldList'] as $n => $vo) { ?>
        	    <!--此处循环自定义表单元素-->
        	    <div class='input-box'>
        	        <?php if ($vo['isadd'] == 0 && !is_array($vo['options'])) { ?>
        	            <input type='hidden' name='<?=$vo['fieldArr']?>[<?=$vo['name']?>]' value='<?=$vo['value']?>'>
        	        <?php } elseif ($vo['type'] == 'hidden') { ?>
        	            <?php if ($vo['value']) { ?>
        	            <?=$vo['title']?>：
        	            <input type='hidden' name='<?=$vo['fieldArr']?>[<?=$vo['name']?>]' value='<?=$vo['value']?>'>
        	            <?php } ?>
        	        <?php } elseif ($vo['type'] == 'text') { ?>
        	            <?php if (isset($vo['ifrequire']) AND $vo['ifrequire']) { ?>*<?php } ?>
        	            <?=$vo['title']?>：
        	            <input type='text' name='<?=$vo['fieldArr']?>[<?=$vo['name']?>]' placeholder='请输入<?=$vo['title']?>' autocomplete='off' value='<?=$vo['value']?>'>
        	        <?php } elseif ($vo['type'] == 'password') { ?>
        	            <?php if (isset($vo['ifrequire']) AND $vo['ifrequire']) { ?>*<?php } ?>
        	            <?=$vo['title']?>：
        	            <input type='password' name='<?=$vo['fieldArr']?>[<?=$vo['name']?>]' placeholder='请输入<?=$vo['title']?>' autocomplete='off' value='<?=$vo['value']?>'>
        	        <?php } elseif ($vo['type'] == 'number') { ?>
        	            <?php if (isset($vo['ifrequire']) AND $vo['ifrequire']) { ?>*<?php } ?>
        	            <?=$vo['title']?>：
        	            <input type='number' name='<?=$vo['fieldArr']?>[<?=$vo['name']?>]' placeholder='请输入<?=$vo['title']?>' autocomplete='off' value='<?=$vo['value']?>'>
        	        <?php } elseif ($vo['type'] == 'array') { ?>
        	            <?php if (isset($vo['ifrequire']) AND $vo['ifrequire']) { ?>*<?php } ?>
        	            <?=$vo['title']?>：
        	            <textarea name='<?=$vo['fieldArr']?>[<?=$vo['name']?>]' placeholder='请输入<?=$vo['title']?>'><?=$vo['value']?></textarea>
        	        <?php } elseif ($vo['type'] == 'checkbox') { ?>
        	            <?php if (isset($vo['ifrequire']) AND $vo['ifrequire']) { ?>*<?php } ?>
        	            <?=$vo['title']?>：
        	            <?php foreach($vo['options'] as $key => $v) { ?>
        	            <label><input type='checkbox' name='<?=$vo['fieldArr']?>[<?=$vo['name']?>][]' title='<?=$v?>' value='<?=$key?>' <?php if (in_array($key, $vo['value'])) { ?> checked<?php } ?>><?=$v?></label>
        	            <?php } ?>
        	        <?php } elseif ($vo['type'] == 'radio') { ?>
        	            <?php if (isset($vo['ifrequire']) AND $vo['ifrequire']) { ?>*<?php } ?>
        	            <?=$vo['title']?>：
        	            <?php foreach($vo['options'] as $key => $v) { ?>
        	            <label><input type='radio' name='<?=$vo['fieldArr']?>[<?=$vo['name']?>]' title='<?=$v?>' value='<?=$key?>' <?php if ($key == $vo['value']) { ?> checked<?php } ?>><?=$v?></label>
        	            <?php } ?>
        	        <?php } elseif ($vo['type'] == 'select') { ?>
        	            <?php if (isset($vo['ifrequire']) AND $vo['ifrequire']) { ?>*<?php } ?>
        	            <?=$vo['title']?>：
        	            <select name='<?=$vo['fieldArr']?>[<?=$vo['name']?>]'>
                            <option value=''></option>
                            <?php foreach($vo['options'] as $key => $v) { ?>
                            <option value='<?=$key?>' <?php if ($key == $vo['value']) { ?> selected='selected' <?php } ?>><?=$v?></option>
                            <?php } ?>
                        </select>
        	        <?php } elseif ($vo['type'] == 'textarea') { ?>
        	            <?php if (isset($vo['ifrequire']) AND $vo['ifrequire']) { ?>*<?php } ?>
        	            <?=$vo['title']?>：
        	            <textarea name='<?=$vo['fieldArr']?>[<?=$vo['name']?>]' placeholder='请输入<?=$vo['title']?>'><?=$vo['value']?></textarea>
        	        <?php } elseif ($vo['type'] == 'image') { ?>
        	            <script src='http://res.wx.qq.com/open/js/jweixin-1.6.0.js'></script>
                        <link rel='stylesheet' type='text/css' href='<?=STATIC_URL?>/js/webuploader/webuploader.css'> 
                        <script src='<?=STATIC_URL?>/js/webuploader/webuploader.js'></script>
        	            <?php if (isset($vo['ifrequire']) AND $vo['ifrequire']) { ?>*<?php } ?>
        	            <?=$vo['title']?>：
        	            <div id='fileToUpload<?=$n?>' style='display: inline-block;'>点击上传</div>
        	            <div id='jq_photo<?=$n?>'></div>
        	            <script>
        	            var uploader = WebUploader.create({
                			auto: true,
                			swf: '<?=STATIC_URL?>/js/webuploader/Uploader.swf',
                			server: '<?=_url('formguide/s_up_img_updata', array('formid' => $output['modelInfo']['id'], 'is_api' => 1))?>',
                			pick: '#fileToUpload<?=$n?>',
                			resize: true,    
                			compress : {quality: 60, allowMagnify: false, crop: false}//裁剪
                        });
                        uploader.on('uploadSuccess', function(file, resporse) {
                            var str = '<img src=\'' + resporse.data.src + '\'><input type=\'hidden\' name=\'<?=$vo['fieldArr']?>[<?=$vo['name']?>]\' value=\'' + resporse.data.src  + '\' />';
                            $('#jq_photo<?=$n?>').show().html(str);
                        });
                        uploader.on('uploadError', function(file) {
                            console.log(file);
                        });
                    </script>
        	        <?php } else { ?>
        	            
        	        <?php } ?>
        	    </div>
        	<?php } ?>
        	<?php if ($output['modelInfo']['setting']['isverify']) { ?>
        	<input type='hidden' name='myhash' value='<?=$output['myhash']?>' />
        	<input type='hidden' name='seccode_key' value='' />
        	<input type='text' class='code' placeholder='请输入验证码' name='captcha' id='captcha' />
        	<img src='' name='codeimage' border='0' id='codeimage' />
        	<?php } ?>
        	<a id='submit'>提交</a>
        </form>
        <script language='JavaScript' type='text/javascript'>
            $(document).ready(function() {
            	change_seccode();
                //更换验证码
                function change_seccode() {
            		getAjax('<?=_url('seccode/makecodekey')?>', {myhash: $('input[name=myhash]').val()}, function(e) {
            			$('#codeimage').attr('src', '<?=_url('seccode/makecode')?>?seccode=' + e.data.seccode + '&t=' + Math.random());
            			$('input[name=seccode_key]').val(e.data.seccode_key);
            			$('#captcha').select();
            		});
                }
            
                $('#codeimage').on('click', function() {
                    change_seccode();
                });
            	//提交数据
            	$('#submit').click(function(e){
            		postAjax('<?=_url('formguide/do')?>', $('#form').serialize(), function(e){
            			if(e.state == 200){
            				window.top.location = '<?=_url('index/index')?>';
            			}else{
            				showError(e.msg);
            			};
            			change_seccode();
            		});
            	});
            	// 回车提交表单
            	$('#form').keydown(function(event){
            		if (event.keyCode == 13) {
            			$('#submit').click();
            		}
            	});
            });
        </script>
	</body>
</html>