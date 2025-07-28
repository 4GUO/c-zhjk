<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='item-publish'>
	<form method='post' id='article_form' action='<?=users_url('article/publish')?>'>
		<input name='form_submit' value='ok' type='hidden' />
		<input type='hidden' name='id' value='<?php echo isset($output['article_info']['article_id']) ? $output['article_info']['article_id'] : 0;?>' />
		<div class='css-form-goods'>
			<h3 id='demo1'>文章基本信息</h3>
			<dl>
				<dt>文章分类：</dt>
				<dd>
					<select name='ac_id'>
					    <?php foreach($output['class_list'] as $k => $v){?>
						<option <?php if(isset($output['article_info']['ac_id']) && $output['article_info']['ac_id'] == $v['ac_id']){?> selected='selected'<?php }?> value='<?=$v['ac_id']?>'><?=$v['ac_name']?></option>
						<?php }?>
					</select>
				</dd>
			</dl>
			<dl>
				<dt><i class='required'>*</i>文章标题：</dt>
				<dd>
					<input name='article_title' class='text w400' value='<?=isset($output['article_info']['article_title']) ? $output['article_info']['article_title'] : ''?>' type='text' />
					<span></span>
					<p class='hint'>文章标题名称长度至少3个字符，最长50个汉字</p>
				</dd>
			</dl>
			<dl>
				<dt>缩略图：</dt>
				<dd>
					<div class='css-goods-default-pic'>
						<div class='goodspic-uplaod'>
							<div class='upload-thumb'> <img nctype='thumb' src='<?=!empty($output['article_info']['article_thumb']) ? $output['article_info']['article_thumb'] : STATIC_URL . '/images/default_image.png'?>'> </div>
							<input name='thumb' nctype='thumb' value='<?=!empty($output['article_info']['article_thumb']) ? $output['article_info']['article_thumb'] : ''?>' type='hidden' />
							<span></span>
							<p class='hint'>建议使用<font color='red'>尺寸200x200像素、大小不超过2M的正方形图片</font>，上传后的图片将会自动保存在图片空间的默认分类中。</p>
							<div class='handle'>
								<div class='css-upload-btn'> 
								    <a href='javascript:void(0);'>
										<span>
											<input hidefocus='true' size='1' class='input-file' name='thumb' id='thumb' type='file' />
										</span>
										<p><i class='icon-upload-alt'></i>图片上传</p>
									</a> 
								</div>
								<a class='css-btn mt5' nc_type='dialog' dialog_title='选择图片' dialog_id='thumb' dialog_width='830' dialog_height='550' uri='<?=users_url('album/pic_list_view', array('item' => 'thumb'))?>'><i class='icon-picture'></i>从图片空间选择</a> 
							</div>
						</div>
					</div>
					<div id='demo'></div>
				</dd>
			</dl>
			<dl>
				<dt>作者：</dt>
				<dd>
					<input name='article_author' class='text w100' value='<?=isset($output['article_info']['article_author']) ? $output['article_info']['article_author'] : ''?>' type='text' />
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>排序：</dt>
				<dd>
					<input name='article_sort' class='text w60' value='<?=isset($output['article_info']['article_sort']) ? $output['article_info']['article_sort'] : 1?>' type='text' />
					<span></span>
				</dd>
			</dl>
			<h3 id='demo2'>文章详情描述</h3>
			<dl style='display: none'>
				<dt>关键词：</dt>
				<dd>
					<input name='article_keyworlds' class='text w400' value='<?=isset($output['article_info']['article_keyworlds']) ? $output['article_info']['article_keyworlds'] : ''?>' type='text' />
					<span></span>
					<p class='hint'>SEO关键词</p>
				</dd>
			</dl>
			<dl>
				<dt>文章简介：</dt>
				<dd>
					<textarea name='article_desc' class='textarea h60 w400'><?=isset($output['article_info']['article_desc']) ? htmlspecialchars_decode($output['article_info']['article_desc']) : ''?></textarea>
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>文章正文：</dt>
				<dd>
					<?php showEditor('article_content', isset($output['article_info']['article_content']) ? htmlspecialchars_decode($output['article_info']['article_content']) : '');?>
					<div class='handle'>
						<div class='css-upload-btn'> 
							<a href='javascript:void(0);'>
								<span><input hidefocus='true' size='1' class='input-file' name='article_content_fileupload' id='article_content_fileupload' type='file' /></span>
								<p><i class='icon-upload-alt'></i>图片上传</p>
							</a> 
						</div>
						<a class='css-btn mt5' nc_type='dialog' dialog_title='选择图片' dialog_id='article_content_fileupload' dialog_width='830' dialog_height='550' uri='<?=users_url('album/pic_list_view', array('item' => 'editor', 'input_name' => 'article_content'))?>'><i class='icon-picture'></i>从图片空间选择</a> 
					</div>
				</dd>
			</dl>
			<h3 id='demo3'>其他信息</h3>
			<dl>
				<dt>文章类型：</dt>
				<dd>
					<ul class='css-form-radio-list'>
						<li>
							<label><input name='article_type' value='1' <?php if(isset($output['article_info']['article_type']) && $output['article_info']['article_type'] == 1){?> checked='checked'<?php }?> type='radio' />帮助</label>
						</li>
						<li>
							<label><input name='article_type' value='2' <?php if(isset($output['article_info']['article_type']) && $output['article_info']['article_type'] == 2){?> checked='checked'<?php }?> type='radio' />公告</label>
						</li>
					</ul>
					<p class='hint'></p>
				</dd>
			</dl>
			<dl>
				<dt>文章状态：</dt>
				<dd>
					<ul class='css-form-radio-list'>
						<li>
							<label><input name='article_show' value='1' <?php if((isset($output['article_info']['article_show']) && $output['article_info']['article_show'] == 1) || !isset($output['article_info']['article_show'])){?> checked='checked'<?php }?> type='radio' />正常</label>
						</li>
						<li>
							<label><input name='article_show' value='0' <?php if(isset($output['article_info']['article_show']) && $output['article_info']['article_show'] == 0){?> checked='checked'<?php }?> type='radio' />禁用</label>
						</li>
					</ul>
					<p class='hint'>被禁用的文章不会在前台显示</p>
				</dd>
			</dl>
		</div>
		<div class='bottom tc hr32'>
			<label class='submit-border'>
				<input class='submit' value='提交' type='button'>
			</label>
		</div>
	</form>
</div>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.iframe-transport.js' charset='utf-8'></script>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.ui.widget.js' charset='utf-8'></script>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.fileupload.js' charset='utf-8'></script>
<script>
upload_file('thumb', '<?=users_url('album/image_upload')?>');
editor_upload_file('article_content', '<?=users_url('album/image_upload')?>', function(e){
	article_content.appendHtml('article_content', '<img src=\''+ e.file_url + '\' alt=\''+ e.file_url + '\'>');
});
$('.submit').click(function(e){
	ajax_form_post('article_form');
});
</script>