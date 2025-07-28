<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<style>
	.btn-primary, .btn-primary:hover {
		background-color: #009688;
		color: #ffffff;
		height: 30px;
		line-height: 30px;
		padding: 0 10px;
		font-size: 12px;
		border: none;
		border-radius: 2px;
	}
</style>
<div class='item-publish'>
	<form method='post' id='form' action='<?=users_url('store/publish')?>'>
		<input name='form_submit' value='ok' type='hidden' />
		<input type='hidden' name='id' value='<?php echo isset($output['info']['id']) ? $output['info']['id'] : 0;?>' />
		
		<input type='hidden' value='<?=isset($output['info']['province_id']) ? $output['info']['province_id'] : 0?>' name='province_id' id='_area_1'>
		<input type='hidden' value='<?=isset($output['info']['city_id']) ? $output['info']['city_id'] : 0?>' name='city_id' id='_area_2'>
		<input type='hidden' value='<?=isset($output['info']['area_id']) ? $output['info']['area_id'] : 0?>' name='area_id' id='_area_3'>
		<div class='css-form-goods'>
			<h3 id='demo1'>基本信息</h3>
			<dl>
				<dt>店铺分类：</dt>
				<dd>
					<select name='sc_id'>
					    <?php foreach($output['class_list'] as $k => $v){?>
						<option <?php if(isset($output['info']['sc_id']) && $output['info']['sc_id'] == $v['sc_id']){?> selected='selected'<?php }?> value='<?=$v['sc_id']?>'><?=$v['sc_name']?></option>
						<?php }?>
					</select>
				</dd>
			</dl>
			<dl>
				<dt><i class='required'>*</i>店铺名称</dt>
				<dd>
					<input name='name' class='text w400' value='<?=isset($output['info']['name']) ? $output['info']['name'] : ''?>' type='text' />
					<span></span>
					<p class='hint'></p>
				</dd>
			</dl>
			<dl>
				<dt><i class='required'>*</i>商家手机号</dt>
				<dd>
					<input name='mobile' class='text w400' value='<?=isset($output['info']['mobile']) ? $output['info']['mobile'] : ''?>' type='text' />
					<span></span>
					<p class='hint'></p>
				</dd>
			</dl>
			<dl>
				<dt>关联会员</dt>
				<dd>
					<div class='css-goods-default-pic'>
						<div class='goodspic-uplaod'>
							<div class='upload-thumb' style='position: relative;'> 
								<img nctype='member_id0' src='<?=!empty($output['member_info']['headimg']) ? $output['member_info']['headimg'] : STATIC_URL . '/images/default_image.png'?>'>
								<span nctype='member_id0' style='position: absolute;bottom: 0;left: 0;background: rgba(0,0,0,0.3);color: #ffffff;z-index: 2;width: 100%;height: 25px;line-height: 25px;font-size: 12px;'><?=!empty($output['member_info']['nickname']) ? $output['member_info']['nickname'] : ''?></span>
							</div>
							<input name='member_id' nctype='member_id0' value='<?=!empty($output['member_info']['uid']) ? $output['member_info']['uid'] : ''?>' type='hidden' />
							<span></span>
							<div class='handle'>
								<a class='css-btn mt5' nc_type='dialog' dialog_title='选择会员' dialog_id='member' dialog_width='830' dialog_height='550' uri='<?=users_url('member/selectView', array('input_name' => 'member_id'))?>'><i class='icon-user'></i>选择会员</a> 
							</div>
						</div>
					</div>
				</dd>
			</dl>
			<dl>
				<dt>LOGO</dt>
				<dd>
					<div class='css-goods-default-pic'>
						<div class='goodspic-uplaod'>
							<div class='upload-thumb'> <img nctype='logo' src='<?=!empty($output['info']['logo']) ? $output['info']['logo'] : STATIC_URL . '/images/default_image.png'?>'> </div>
							<input name='logo' nctype='logo' value='<?=!empty($output['info']['logo']) ? $output['info']['logo'] : ''?>' type='hidden' />
							<span></span>
							<p class='hint'>建议使用<font color='red'>尺寸200x200像素、大小不超过2M的正方形图片</font>，上传后的图片将会自动保存在图片空间的默认分类中。</p>
							<div class='handle'>
								<div class='css-upload-btn'> 
								    <a href='javascript:void(0);'>
										<span>
											<input hidefocus='true' size='1' class='input-file' name='logo' id='logo' type='file' />
										</span>
										<p><i class='icon-upload-alt'></i>图片上传</p>
									</a> 
								</div>
								<a class='css-btn mt5' nc_type='dialog' dialog_title='选择图片' dialog_id='logo' dialog_width='830' dialog_height='550' uri='<?=users_url('album/pic_list_view', array('item' => 'logo'))?>'><i class='icon-picture'></i>从图片空间选择</a> 
							</div>
						</div>
					</div>
				</dd>
			</dl>
			<dl>
				<dt>店铺顶部图</dt>
				<dd>
					<div class='css-goods-default-pic'>
						<div class='goodspic-uplaod'>
							<div class='upload-thumb'> <img nctype='banner' src='<?=!empty($output['info']['banner']) ? $output['info']['banner'] : STATIC_URL . '/images/default_image.png'?>'> </div>
							<input name='banner' nctype='banner' value='<?=!empty($output['info']['banner']) ? $output['info']['banner'] : ''?>' type='hidden' />
							<span></span>
							<p class='hint'>建议使用<font color='red'>尺寸640x100像素、大小不超过2M的图片</font>，上传后的图片将会自动保存在图片空间的默认分类中。</p>
							<div class='handle'>
								<div class='css-upload-btn'> 
								    <a href='javascript:void(0);'>
										<span>
											<input hidefocus='true' size='1' class='input-file' name='banner' id='banner' type='file' />
										</span>
										<p><i class='icon-upload-alt'></i>图片上传</p>
									</a> 
								</div>
								<a class='css-btn mt5' nc_type='dialog' dialog_title='选择图片' dialog_id='banner' dialog_width='830' dialog_height='550' uri='<?=users_url('album/pic_list_view', array('item' => 'banner'))?>'><i class='icon-picture'></i>从图片空间选择</a> 
							</div>
						</div>
					</div>
				</dd>
			</dl>
			<dl>
				<dt>所在地区：</dt>
				<dd>
					<div>
						<input type='hidden' name='region' id='region' value='<?php echo isset($output['info']['region']) ? $output['info']['region'] : '';?>'/>
					</div>
				</dd>
			</dl>
			<dl>
				<dt>详细地址</dt>
				<dd>
					<input id='address' name='address' placeholder='带省市区的详细地址' class='text w400' value='<?=isset($output['info']['address']) ? $output['info']['address'] : ''?>' type='text' />
					<a href='javascript:;' class='css-btn' style='margin-top: -3px;' onclick='getResult()'>搜索地图</a>
				</dd>
			</dl>
			<dl>
				<dt>经纬度</dt>
				<dd>
					<input id='lon' name='lon' class='text w150' readonly='readonly' style='background: none rgb(231, 231, 231);' value='<?=isset($output['info']['lon']) ? $output['info']['lon'] : ''?>' type='text' />&nbsp;&nbsp;&nbsp;
					<input id='lat' name='lat' class='text w150' readonly='readonly' style='background: none rgb(231, 231, 231);' value='<?=isset($output['info']['lat']) ? $output['info']['lat'] : ''?>' type='text' />
				</dd>
			</dl>
			<dl>
				<dt>地图定位</dt>
				<dd>
					<div id='container' style='width: 90%; height: 300px;'></div>
				</dd>
			</dl>
			<dl>
				<dt>状态</dt>
				<dd>
					<label><input type='radio' name='state' value='0' <?php if(empty($output['info']['state'])){?> checked<?php }?> />&nbsp;&nbsp;禁用</label><br />
					<label><input type='radio' name='state' value='1' <?php if(!empty($output['info']['state']) && $output['info']['state'] == 1){?> checked<?php }?> />&nbsp;&nbsp;正常</label><br />
				</dd>
			</dl>
			<h3 id='demo2'>登录信息</h3>
			<dl>
				<dt>登录账号</dt>
				<dd>
					<input name='login_name' class='text w200' value='<?=isset($output['info']['login_name']) ? $output['info']['login_name'] : ''?>' type='text' />
					<span></span>
					<p class='hint'></p>
				</dd>
			</dl>
			<dl>
				<dt>登录密码</dt>
				<dd>
					<input name='login_password' class='text w200' value='' type='password' />
					<span></span>
					<p class='hint'>留空则不修改密码</p>
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
upload_file('logo', '<?=users_url('album/image_upload')?>');
upload_file('banner', '<?=users_url('album/image_upload')?>');
$('.submit').click(function(e){
	ajax_form_post('form');
});
$('#region').fxy_region();
</script>
<script type='text/javascript' src='<?=STATIC_URL?>/js/dist/area/cascade.js'></script>
<script charset='utf-8' src='https://map.qq.com/api/js?v=2.exp&key=ZI4BZ-2PWEQ-P6R5R-GYMJL-LQF6H-34FUU'></script>
<script>
	var lat = <?=!empty($output['info']['lat']) ? $output['info']['lat'] : 39.916527?>;
	var lon = <?=!empty($output['info']['lon']) ? $output['info']['lon'] : 116.397128?>;
    var map,geocoder,markersArray = [];
    function init() {
        var center = new qq.maps.LatLng(lat, lon);
        map = new qq.maps.Map(document.getElementById('container'), {
            zoom: 12,
            // 地图的中心地理坐标。
            center: center
        });
        if (lat && lon) {
            var marker = new qq.maps.Marker({
                position: center,
                map: map
            });
            markersArray.push(marker);
        }
        
        // 逆地址解析(经纬度到地名转换过程)
        geocoder = new qq.maps.Geocoder({
            complete: function(res) {
                console.log(res)
                document.getElementById('address').value = res.detail.address;
                var center3 = new qq.maps.LatLng(res.detail.location.lat, res.detail.location.lng);
                map.setCenter(center3);
                deleteOverlays();
                var marker3 = new qq.maps.Marker({
                    position: center3,
                    map: map
                });
                markersArray.push(marker3);
                //document.getElementById('lat').value = '';
                //document.getElementById('lon').value = '';
            }
        });
        //标记覆盖物
        qq.maps.event.addListener(map, 'click', function(event) {
            var center2 = new qq.maps.LatLng(event.latLng.getLat(), event.latLng.getLng());
            deleteOverlays();
            var marker2 = new qq.maps.Marker({
                position: center2,
                map: map
            });
            markersArray.push(marker2);
            geocoder.getAddress(center2);
            document.getElementById('lat').value = event.latLng.getLat();
            document.getElementById('lon').value = event.latLng.getLng();
			// 将给定的坐标位置转换为地址
			/*geocoder.getAddress({location: center2}).then((result) => {
				document.getElementById('address').value = result.result.address;
				// 显示搜索到的地址
			});*/
        });
    }
    //删除覆盖物
    function deleteOverlays() {
        if (markersArray) {
            for (i in markersArray) {
                markersArray[i].setMap(null);
            }
            markersArray.length = 0;
        }
    }
    $(function() {
        init();
    })
    document.getElementById('address').onkeydown = function(e) {
        var that = this;
        var e = e || event;
		var currentKey = e.keyCode || e.which || e.charCode;
		if (currentKey == 13 ) {
			geocoder.getLocation($(that).val());
		}
    };
	function getResult() {
		geocoder.getLocation($('#address').val());
	}
</script>