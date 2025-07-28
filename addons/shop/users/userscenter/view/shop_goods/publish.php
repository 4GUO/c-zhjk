<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<style type='text/css'>
.g_commission{width: 300px; border: 1px solid #dfdfdf; border-top: none}
.g_commission thead {background: #f5f5f5}
.g_commission td{height: 30px; line-height: 30px; text-align: center; padding: 3px 0px; border-top: 1px #dfdfdf solid}
.g_commission .td_left{border-right: 1px #dfdfdf solid}
.dislevelcss{float: left; margin: 5px 0px 0px 8px; text-align: center; border:solid 1px #E6E6E6; padding: 5px;}
.dislevelcss th{border-bottom: dashed 1px #E6E6E6; font-size: 16px;}
.item_data_table tr td {
	padding: 6px !important;
	height: 30px;
}
</style>
<?php if ($output['edit_goods_sign']) {?>
<div class='tabmenu'>
	<ul class='tab pngFix'>
		<li class='active'>
			<a href='<?=users_url('shop_goods/publish', array('commonid' => (isset($output['goods']) ? $output['goods']['goods_commonid'] : 0)))?>'>编辑商品</a>
		</li>
		<li class='normal'>
			<a href='<?=users_url('shop_goods/edit_images', array('commonid' => (isset($output['goods']) ? $output['goods']['goods_commonid'] : 0), 'type' => 'edit'))?>'>编辑图片</a>
		</li>
	</ul>
</div>
<?php } else {?>
<ul class='add-goods-step'>
	<li class='current'><i class='icon icon-edit'></i>
		<h6>STEP.1</h6>
		<h2>填写商品详情</h2>
		<i class='arrow icon-angle-right'></i> </li>
	<li><i class='icon icon-camera-retro '></i>
		<h6>STEP.2</h6>
		<h2>上传商品图片</h2>
		<i class='arrow icon-angle-right'></i> </li>
	<li><i class='icon icon-ok-circle'></i>
		<h6>STEP.3</h6>
		<h2>商品发布成功</h2>
	</li>
</ul>
<?php }?>
<div class='item-publish'>
	<form method='post' id='form' action='<?=users_url('shop_goods/publish')?>'>
		<input name='form_submit' value='ok' type='hidden' />
		<input type='hidden' name='commonid' value='<?php echo isset($output['goods']) ? $output['goods']['goods_commonid'] : 0;?>' />
		<div class='css-form-goods'>
			<h3 id='demo1'>商品基本信息</h3>
			<dl>
				<dt><i class='required'>*</i>商品分类：</dt>
				<dd>
					<select name='cate_id'>
					    <?php foreach ($output['class_list'] as $k => $v) { ?>
						<?php if (!empty($v['child'])) { ?>
						<option value='<?=$v['gc_id']?>' disabled='disabled' gc_virtual='<?=$v['gc_virtual']?>'><?=$v['gc_name']?></option>
						<?php foreach ($v['child'] as $c_k => $c_v) { ?>
						<option <?php if(isset($output['goods']['gc_id']) && $output['goods']['gc_id'] == $c_v['gc_id']){?> selected='selected'<?php }?> value='<?=$c_v['gc_id']?>' gc_virtual='<?=$c_v['gc_virtual']?>'>&nbsp;&nbsp;└└ <?=$c_v['gc_name']?></option>
						<?php } ?>
						<?php } else { ?>
						<option <?php if(isset($output['goods']['gc_id']) && $output['goods']['gc_id'] == $v['gc_id']){?> selected='selected'<?php }?> value='<?=$v['gc_id']?>' gc_virtual='<?=$v['gc_virtual']?>'><?=$v['gc_name']?></option>
						<?php } ?>
						<?php } ?>
					</select>
				</dd>
			</dl>
			<dl>
				<dt><i class='required'>*</i>选择商家：</dt>
				<dd>
					<select name='store_id'>
					    <?php foreach($output['store_list'] as $k => $v) {?>
						<option <?php if(isset($output['goods']['store_id']) && $output['goods']['store_id'] == $v['id']){?> selected='selected'<?php }?> value='<?=$v['id']?>'><?=$v['name']?></option>
						<?php }?>
					</select>
				</dd>
			</dl>
			<dl>
				<dt><i class='required'>*</i>商品名称：</dt>
				<dd>
					<input name='g_name' class='text w400' value='<?=isset($output['goods']['goods_name']) ? $output['goods']['goods_name'] : ''?>' type='text' />
					<span></span>
					<p class='hint'>商品标题名称长度至少3个字符，最长50个汉字</p>
				</dd>
			</dl>
			<dl>
				<dt>商品简介：</dt>
				<dd>
					<textarea name='g_jingle' class='textarea h60 w400'><?=isset($output['goods']['goods_jingle']) ? $output['goods']['goods_jingle'] : ''?></textarea>
					<span></span>
					<p class='hint'>商品卖点最长不能超过140个汉字，前台搜索会检索此项和商品名称</p>
				</dd>
			</dl>
            <dl>
				<dt nc_type='no_spec'><i class='required'>*</i>出售价格：</dt>
				<dd nc_type='no_spec'>
					<?php if (!empty($output['config']['vip_price_verify'])) { ?>
                	<?php foreach($output['member_levels'] as $level_info) { ?>
                	<div style='padding-bottom:8px;'>
                        <i style='line-height: 28px; background-color: #E6E6E6; vertical-align: top; display: block; width: 80px; text-align: center; padding:0px 8px; height: 28px; border: 1px solid #CCC; border-right:none; margin:0 0 0 -5px; float:left'><?php echo $level_info['level_name'];?></i><input id='g_price_<?php echo $level_info['id'];?>' name='g_price[<?php echo $level_info['id'];?>]' value='<?php echo isset($output['goods']) ? (empty($output['goods']['goods_price_vip'][$level_info['id']]) ? '' : $output['goods']['goods_price_vip'][$level_info['id']]) : ''; ?>' type='text'  class='text w60 g_price' style='display: block; float:left'/><em class='add-on'><i class='icon-renminbi'></i></em><span></span>
                    </div>
                    <?php } ?>
					<p class='hint'>不同会员级别不同价格。价格必须是0.01~9999999之间的数字</p>
					<?php } else { ?>
					
					<?php } ?>
				</dd>
			</dl>
			<dl>
				<dt>结算金额：</dt>
				<dd>
					<input name='yeji_price' value='<?=isset($output['goods']['yeji_price']) ? $output['goods']['yeji_price'] : 0?>' class='text w60' type='text' />&nbsp;元<span></span>
					<p class='hint'>奖金结算金额</p>
				</dd>
			</dl>
			<dl>
				<dt>成本价：</dt>
				<dd>
					<input name='g_costprice' value='<?=isset($output['goods']['goods_costprice']) ? $output['goods']['goods_costprice'] : 0?>' class='text w60' type='text' />
					<em class='add-on'><i class='icon-renminbi'></i></em> <span></span>
					<p class='hint'>价格必须是0~9999999之间的数字，此价格不会在前台显示，<font color='#ff6600;'>用于商家结算</font>，请根据该实际情况认真填写。</p>
				</dd>
			</dl>
			<dl>
				<dt>市场价：</dt>
				<dd>
					<input name='g_marketprice' value='<?=isset($output['goods']['goods_marketprice']) ? $output['goods']['goods_marketprice'] : 0?>' class='text w60' type='text' />
					<em class='add-on'><i class='icon-renminbi'></i></em> <span></span>
					<p class='hint'>价格必须是0~9999999之间的数字，此价格仅为市场参考售价，请根据该实际情况认真填写。</p>
				</dd>
			</dl>
			<dl>
				<dt nc_type='no_spec'><i class='required'>*</i>商品库存：</dt>
				<dd nc_type='no_spec'>
					<input name='g_storage' value='<?php echo isset($output['goods']) ? $output['goods']['g_storage'] : ''; ?>' type='text' class='text w60' />
					<span></span>
					<p class='hint'>商品库存数量必须为0~999999999之间的整数</p>
				</dd>
			</dl>
			<dl style='display: none;'>
				<dt nc_type='no_spec'>商品销量：</dt>
				<dd nc_type='no_spec'>
					<input name='g_salenum' value='<?php echo isset($output['goods']) ? $output['goods']['g_salenum'] : ''; ?>' type='text' class='text w60' />
					<span></span>
				</dd>
			</dl>
			<dl style='display: none;'>
				<dt nc_type='no_spec'>商品重量：</dt>
				<dd nc_type='no_spec'>
					<input name='g_weight' value='<?=isset($output['goods']['goods_weight']) ? $output['goods']['goods_weight'] : 0?>' class='text w60' type='text'>
					<em class='add-on' style='font-weight: bold'>kg</em> <span></span>
				</dd>
			</dl>
			<dl>
				<dt nc_type='no_spec'>运费：</dt>
				<dd nc_type='no_spec'>
					<input name='g_freight' value='<?=isset($output['goods_freight']) ? $output['goods_freight'] : 0?>' class='text w60' type='text'>
					<em class='add-on'><i class='icon-renminbi'></i></em> <span></span>
					<p class='hint'>如果地区运费未设置，则默认选择此设置。</p>
				</dd>
			</dl>
			<dl>
				<dt nc_type='no_spec'>商家货号：</dt>
				<dd nc_type='no_spec'>
					<p>
						<input name='g_serial' value='<?=isset($output['goods']['goods_serial']) ? $output['goods']['goods_serial'] : ''?>' class='text' type='text' />
					</p>
					<p class='hint'>商家货号是指商家管理商品的编号<br>最多可输入20个字符，支持输入中文、字母、数字、_、/、-和小数点</p>
				</dd>
			</dl>
			<div class='goods_specs_html' style='display:none'></div>			
			<dl>
				<dt>商品图片：</dt>
				<dd>
					<div class='css-goods-default-pic'>
						<div class='goodspic-uplaod'>
							<div class='upload-thumb'> <img nctype='goods_image' src='<?=isset($output['goods']['goods_image']) ? $output['goods']['goods_image'] : STATIC_URL . '/images/default_image.png'?>'> </div>
							<input name='image_path' nctype='goods_image' value='<?=isset($output['goods']['goods_image']) ? $output['goods']['goods_image'] : ''?>' type='hidden' />
							<span></span>
							<p class='hint'>上传商品默认主图，如多规格值时将默认使用该图或分规格上传各规格主图；支持jpg、gif、png格式上传或从图片空间中选择，建议使用<font color='red'>尺寸800x800像素以上、大小不超过1M的正方形图片</font>，上传后的图片将会自动保存在图片空间的默认分类中。</p>
							<div class='handle'>
								<a class='css-btn mt5' nc_type='dialog' dialog_title='选择图片' dialog_id='goods_image' dialog_width='830' dialog_height='550' uri='<?=users_url('album/pic_list_view', array('item' => 'goods_image'))?>'><i class='icon-picture'></i>从图片空间选择</a> 
							</div>
						</div>
					</div>
					<div id='demo'></div>
				</dd>
			</dl>
			<h3 id='demo2'>商品视频</h3>
			<dl>
				<dt>视频封面：</dt>
				<dd>
					<div class='css-goods-default-pic'>
						<div class='goodspic-uplaod'>
							<div class='upload-thumb'> <img nctype='goods_video_poster' src='<?=!empty($output['goods']['goods_video_poster']) ? $output['goods']['goods_video_poster'] : STATIC_URL . '/images/default_image.png'?>'> </div>
							<input name='goods_video_poster' nctype='goods_video_poster' value='<?=!empty($output['goods']['goods_video_poster']) ? $output['goods']['goods_video_poster'] : ''?>' type='hidden' />
							<span></span>
							<p class='hint'><font color='red'>尺寸跟商品缩略图一致</font>，上传后的图片将会自动保存在图片空间的默认分类中。</p>
							<div class='handle'>
								<a class='css-btn mt5' nc_type='dialog' dialog_title='选择图片' dialog_id='goods_video_poster' dialog_width='830' dialog_height='550' uri='<?=users_url('album/pic_list_view', array('item' => 'goods_video_poster'))?>'><i class='icon-picture'></i>从图片空间选择</a> 
							</div>
						</div>
					</div>
					<div id='demo'></div>
				</dd>
			</dl>
			<dl>
				<dt>视频</dt>
				<dd>
					<div class='css-goods-default-pic'>
						<div class='goodspic-uplaod'>
							<div class='upload-thumb'> 
								<video rel='goods_video' src='<?=!empty($output['goods']['goods_video']) ? $output['goods']['goods_video'] : STATIC_URL . '/images/default_image.png'?>' controls='controls' height='150'>
									您的浏览器不支持 video 标签，建议使用谷歌浏览器。
								</video>
							</div>
							<input name='goods_video' nctype='goods_video' value='<?=!empty($output['goods']['goods_video']) ? $output['goods']['goods_video'] : ''?>' type='hidden' />
							<span></span>
							<p class='hint'>大小不超过10M的MP4视频。</p>
							<div class='handle'>
								<div class='css-upload-btn'> 
								    <a href='javascript:void(0);'>
										<span>
											<input hidefocus='true' size='1' class='input-file' name='goods_video' id='goods_video' type='file' accept='video/*' />
										</span>
										<p><i class='icon-upload-alt'></i>视频上传</p>
									</a> 
								</div>
							</div>
						</div>
					</div>
				</dd>
			</dl>
			<h3 id='demo3'>商品详情描述</h3>
			<dl>
				<dt>商品描述：</dt>
				<dd>
					<?php showEditor('mobile_body', isset($output['goods']['mobile_body']) ? htmlspecialchars_decode($output['goods']['mobile_body']) : '');?>
					<div class='handle'>
						<a class='css-btn mt5' nc_type='dialog' dialog_title='选择图片' dialog_id='mobile_body_fileupload' dialog_width='830' dialog_height='550' uri='<?=users_url('album/pic_list_view', array('item' => 'editor', 'input_name' => 'mobile_body'))?>'><i class='icon-picture'></i>从图片空间选择</a> 
					</div>
				</dd>
			</dl>
			<h3 id='demo4' class='is_virtual'>特殊商品</h3>
			<dl class='is_virtual'>
				<dt>虚拟商品：</dt>
				<dd>
					<ul class='css-form-radio-list'>
						<li>
							<label><input name='is_gv' value='1' <?php if(isset($output['goods']['is_virtual']) && $output['goods']['is_virtual']){?> checked='checked'<?php }?> type='radio' />是</label>
						</li>
						<li>
							<label><input name='is_gv' value='0' <?php if(empty($output['goods']['is_virtual'])) {?> checked='checked'<?php }?> type='radio' />否</label>
						</li>
					</ul>
					<p class='hint'>虚拟商品不能参加限时折扣</p>
				</dd>
			</dl>
			<dl class='is_virtual' nctype='virtual_valid' <?php if (isset($output['goods']) && $output['goods']['is_virtual'] == 0) { ?>style='display:none;'<?php }?>>
				<dt><i class='required'>*</i>虚拟商品有效期至：</dt>
				<dd>
					<input type='text' name='g_vindate' id='g_vindate' class='w80 text' value='<?php if(isset($output['goods']) && $output['goods']['is_virtual'] == 1 && !empty($output['goods']['virtual_indate'])) { echo date('Y-m-d', $output['goods']['virtual_indate']);}?>'>
					<em class='add-on'><i class='icon-calendar'></i></em> <span></span>
					<p class='hint'>虚拟商品可兑换的有效期，过期后商品不能购买，电子兑换码不能使用。</p>
				</dd>
			</dl>
			<dl class='is_virtual' nctype='virtual_valid' <?php if (isset($output['goods']) && $output['goods']['is_virtual'] == 0) {?>style='display:none;'<?php }?>>
				<dt><i class='required'>*</i>虚拟商品购买上限：</dt>
				<dd>
					<input type='text' name='g_vlimit' id='g_vlimit' class='w80 text' value='<?php if (isset($output['goods']) && $output['goods']['is_virtual'] == 1) {echo $output['goods']['virtual_limit'];}?>'>
					<span></span>
					<p class='hint'>请填写1~10之间的数字，虚拟商品最高购买数量不能超过10个。</p>
				</dd>
			</dl>
			<dl class='is_virtual' nctype='virtual_valid' <?php if (isset($output['goods']) && $output['goods']['is_virtual'] == 0) {?>style='display:none;'<?php }?>>
				<dt>支持过期退款：</dt>
				<dd>
					<ul class='ncsc-form-radio-list'>
						<li>
							<label><input type='radio' name='g_vinvalidrefund' value='1' <?php if (isset($output['goods']) && $output['goods']['virtual_invalid_refund'] ==1) {?>checked<?php }?> /> 是</label>
						</li>
						<li>
							<label><input type='radio' name='g_vinvalidrefund' value='0' <?php if (empty($output['goods']) || isset($output['goods']) && $output['goods']['virtual_invalid_refund'] == 0) {?>checked<?php }?> /> 否</label>
						</li>
					</ul>
					<p class='hint'>兑换码过期后是否可以申请退款。</p>
				</dd>
			</dl>
			<?php if (!empty($output['setting']['distributor_open_goods'])) { ?>
			<h3 id='demo5'>奖励设置</h3>
			<dl style='display: none;'>
				<dt>总佣金占比：</dt>
				<dd>
					<input name='g_profit' value='<?=isset($output['goods']['good_profit']) ? $output['goods']['good_profit'] : 100?>' class='text w60' type='text'>
					<em class='add-on'><i>%</i></em> <span></span>
					<p class='hint'>分销商品发放佣金占商品利润百分比，设置此比例，防止佣金发放溢出；商品利润=出售价格-成本价。</p>
				</dd>
			</dl>
			<dl>
				<dt><?php echo $output['setting']['bonus_name_goods'];?>设置：</dt>
				<dd>
					<?php
					foreach($output['member_levels'] as $key => $disinfo){
					?>
					<div class='dislevelcss'>
						<table id='11' class='item_data_table' border='0' cellpadding='3' cellspacing='0'>
							<tr><th><?php echo $disinfo['level_name']?></th></tr>
							<?php
							$level = $output['setting']['distributor_self_goods'] ? $output['setting']['distributor_level_goods'] + 1 : $output['setting']['distributor_level_goods'];						
							for ($i = 0; $i < $level; $i++) {
							?>                        
							<tr>
								<td>
								<?php if($output['setting']['distributor_self_goods'] == 1 && $i == $output['setting']['distributor_level_goods']) { ?>
								自销
								<?php } else { ?>                            
								<?php echo $i+1;?>&nbsp;级
								<?php } ?>&nbsp;&nbsp; %
									<input id='dischange<?=$disinfo['id'] . $i?>' name='discommission[<?=$disinfo['id']?>][<?php echo $i;?>]' value='<?php echo !empty($output['goods']['goods_commission'][$disinfo['id']][$i]) ? $output['goods']['goods_commission'][$disinfo['id']][$i] : 0; ?>' class='form_input' size='5' maxlength='10' type='text' />
									(总佣金的百分比)
								</td>
							</tr>
							<?php }?>
						</table>
					</div>
					<?php } ?>
					<div style='clear:both'></div>
				</dd>
			</dl>
			<?php } ?>
			<dl>
				<dt>报单奖励比例：</dt>
				<dd>
					<input name='baodan_reward_bili' class='text w60' value='<?=$output['goods']['baodan_reward_bili'] ?? ''?>' type='text' />&nbsp;%
					<span></span>
				</dd>
			</dl>
			<h3 id='demo6'>其他信息</h3>
			<dl>
				<dt>本店分类：</dt>
				<dd id='stc_id'>
				
				</dd>
			</dl>
			<dl>
				<dt>是否新品：</dt>
				<dd>
					<ul class='css-form-radio-list'>
						<li>
							<label><input name='g_is_new' value='1' <?php if(isset($output['goods']['is_new']) && $output['goods']['is_new']){?> checked='checked'<?php }?> type='radio' />是</label>
						</li>
						<li>
							<label><input name='g_is_new' value='0' <?php if((isset($output['goods']['is_new']) && $output['goods']['is_new'] == 0) || empty($output['goods']) == 1){?> checked='checked'<?php }?> type='radio' />否</label>
						</li>
					</ul>
				</dd>
			</dl>
			<dl>
				<dt>是否推荐：</dt>
				<dd>
					<ul class='css-form-radio-list'>
						<li>
							<label><input name='g_commend' value='1' <?php if(isset($output['goods']['goods_commend']) && $output['goods']['goods_commend']){?> checked='checked'<?php }?> type='radio' />是</label>
						</li>
						<li>
							<label><input name='g_commend' value='0' <?php if((isset($output['goods']['goods_commend']) && $output['goods']['goods_commend'] == 0) || empty($output['goods']) == 1){?> checked='checked'<?php }?> type='radio' />否</label>
						</li>
					</ul>
				</dd>
			</dl>
			<dl>
				<dt>排序：</dt>
				<dd>
					<input name='g_sort' value='<?=isset($output['goods']['goods_sort']) ? $output['goods']['goods_sort'] : 9999?>' class='text w60' type='text'>
					<span></span>
					<p class='hint'>数字越小越靠前</p>
				</dd>
			</dl>
			<dl>
				<dt>商品状态：</dt>
				<dd>
					<ul class='css-form-radio-list'>
						<li>
							<label><input name='g_status' value='1' <?php if((isset($output['goods']['goods_state']) && $output['goods']['goods_state']) || empty($output['goods']) == 1){?> checked='checked'<?php }?> type='radio' />正常</label>
						</li>
						<li>
							<label><input name='g_status' value='0' <?php if(isset($output['goods']['goods_state']) && $output['goods']['goods_state'] == 0){?> checked='checked'<?php }?> type='radio' />禁用</label>
						</li>
					</ul>
					<p class='hint'>被禁用的商品不会在前台显示</p>
				</dd>
			</dl>
			<dl>
				<dt>体验产品：</dt>
				<dd>
					<ul class='css-form-radio-list'>
						<li>
							<label><input name='is_experience_goods' value='1' <?php if((isset($output['goods']['is_experience_goods']) && $output['goods']['is_experience_goods']) || empty($output['goods']) == 1){?> checked='checked'<?php }?> type='radio' />是</label>
						</li>
						<li>
							<label><input name='is_experience_goods' value='0' <?php if(isset($output['goods']['is_experience_goods']) && $output['goods']['is_experience_goods'] == 0){?> checked='checked'<?php }?> type='radio' />不是</label>
						</li>
					</ul>
					<p class='hint'>是否为体验区产品，为体验馆升级使用</p>
				</dd>
			</dl>
			<dl style='display: none;'>
				<dt>限购：</dt>
				<dd>
					<input name='buy_xiangou' value='<?php echo isset($output['goods']) ? $output['goods']['buy_xiangou'] : 0; ?>' type='text' class='text w60' />
					<span></span>
					<p class='hint'>本产品必须一次性购买数量；0表示不限制</p>
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
<link rel='stylesheet' type='text/css' href='<?=STATIC_URL?>/js/datetimepicker/jquery.datetimepicker.css'/>
<script type='text/javascript' src='<?=STATIC_URL?>/js/datetimepicker/jquery.datetimepicker.js'></script>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.iframe-transport.js' charset='utf-8'></script>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.ui.widget.js' charset='utf-8'></script>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.fileupload.js' charset='utf-8'></script>
<script type='text/javascript' src='<?=STATIC_URL?>/admin/js/goods_add.js?t=<?=time()?>' charset='utf-8'></script>
<script>
upload_file('goods_image', '<?=users_url('album/image_upload')?>');
upload_file2('goods_video', '<?=users_url('qiniu_file/upload_file')?>');
editor_upload_file('mobile_body', '<?=users_url('album/image_upload')?>', function(e){
	mobile_body.appendHtml('mobile_body', '<img src=\''+ e.file_url + '\' alt=\''+ e.file_url + '\'>');
});
$('.submit').click(function(e){
	ajax_form_post('form');
});

$(function(){
    $('select[name=cate_id]').change(function() {
		if ($(this).val() == '' && $(this).val() == 0) {
			return false;
		}
		var gcid = $(this).val();
		spec_init(gcid);
		var gc_virtual = $(this).find('option:selected').attr('gc_virtual');
		if (gc_virtual == 1) {
			$('.is_virtual').show();
		} else {
			$('.is_virtual').hide();
		}
    });
	spec_init($('select[name=cate_id]').val());
	$('select[name=store_id]').change(function() {
		if ($(this).val() == '' && $(this).val() == 0) {
			return false;
		}
		var storeid = $(this).val();
		class_init(storeid);
    });
	class_init($('select[name=store_id]').val());
	var gc_virtual = $('select[name=cate_id]').find('option:selected').attr('gc_virtual');
	if (gc_virtual == 1) {
		$('.is_virtual').show();
	} else {
		$('.is_virtual').hide();
	}
	$('input[name=is_gv]').change(function() {
		if ($(this).val() == '' && $(this).val() == 0) {
			return false;
		}
		var is_gv = $(this).val();
		if (is_gv == 1) {
			$('[nctype=virtual_valid]').show();
		} else {
			$('[nctype=virtual_valid]').hide();
		}
    });
	var is_gv = $('input[name=is_gv][checked]').val();
	if (is_gv == 1) {
		$('[nctype=virtual_valid]').show();
	} else {
		$('[nctype=virtual_valid]').hide();
	}
});

function spec_init(gcid){
	getAjax('<?=users_url('shop_spec/goods_spec')?>', {gc_id : gcid, commonid: <?php echo isset($output['goods']) ? $output['goods']['goods_commonid'] : 0;?>}, function(data) {
		if (data) {
			$('.goods_specs_html').html(data).show();
		} else {
			$('.goods_specs_html').html('').hide();
		}			
	}, 'html');
}
function class_init(store_id){
	getAjax('<?=users_url('store_goods_class/ajax_list')?>', {store_id : store_id, stc_id: <?php echo isset($output['goods']) ? $output['goods']['stc_id'] : 0;?>}, function(data) {
		if (data) {
			$('#stc_id').html(data);
		} else {
			$('#stc_id').html('');
		}			
	}, 'html');
}
$('#g_vindate').datetimepicker({
	lang: 'ch',
	datepicker: true,
	format: 'Y-m-d',
	formatDate: 'Y-m-d',
	step: 60
});
</script>