<?php defined('SAFE_CONST') or exit('Access Invalid!');?>

<div class='tabmenu'>
	<ul class='tab pngFix'>
		<li class='<?php if (input('goods_state', 2) == 2) { ?>active<?php } else { ?>normal<?php } ?>'><a href='<?=_url('shop_goods/index', array('goods_state' => 2))?>'>出售商品</a></li>
		<li class='<?php if (input('goods_state', 2) == 1) { ?>active<?php } else { ?>normal<?php } ?>'><a href='<?=_url('shop_goods/index', array('goods_state' => 1))?>'>下架商品</a></li>
	</ul>
	<!--<a href='<?=users_url('shop_goods/clear_goods_card')?>' class='css-btn css-btn-green' style='right: 100px;'>一键清除海报</a>-->
	<a href='<?=users_url('shop_goods/publish')?>' class='css-btn css-btn-green'>新增产品</a>
</div>
<form method='get' action='<?=users_url('shop_goods/index')?>'>
	<table class='search-form'>	
		<tbody><tr>
			<td>&nbsp;</td>
			<th>商品分类</th>
			<td class='w160'>
				<select name='gc_id' class='w150'>
					<option value='0'>请选择...</option>
					<?php foreach($output['goods_class'] as $val){?>
					<option <?php if(input('gc_id', 0) == $val['gc_id']){?> selected='selected'<?php }?> value='<?=$val['gc_id']?>'><?=$val['gc_name']?></option>
					<?php if(!empty($val['child'])){?>
					<?php foreach($val['child'] as $c_k => $c_v){?>
					<option <?php if(input('gc_id', 0) == $c_v['gc_id']){?> selected='selected'<?php }?> value='<?=$c_v['gc_id']?>'>&nbsp;&nbsp;└└ <?=$c_v['gc_name']?></option>
					<?php }?>
					<?php }?>
					<?php }?>
				</select>
			</td>
			<th> 
				<select name='search_type'>
					<option value='0' <?php if(input('search_type', 0) == 0){?> selected='selected'<?php }?>>商品名称</option>
					<option value='1' <?php if(input('search_type', 0) == 1){?> selected='selected'<?php }?>>商品货号</option>
				</select>
			</th>
			<td class='w160'><input class='text w150' name='keyword' value='<?=input('keyword', '')?>' type='text'></td>
			<td class='tc w70'>
				<label class='submit-border'>
					<input class='submit' value='搜索' type='submit'>
				</label>
			</td>
		</tr>
	</tbody></table>
</form>
<table class='css-default-table' id='my_category'>
	<thead>
		<tr nc_type='table_header'>
			<th class='w30'>&nbsp;</th>
			<th class='w50'>&nbsp;</th>
			<th coltype='editable' column='goods_name' checker='check_required' inputwidth='230px'>商品名称</th>
			<th class='w120'>所属分类</th>
			<th class='w100'>库存</th>
			<th class='w100'>发布时间</th>
			<th class='w150'>操作</th>
		</tr>
		<?php if (!empty($output['list'])) { ?>
		<tr>
			<td class='tc'><input id='all' type='checkbox' class='checkall' /></td>
			<td colspan='20'>
			    <label for='all'>全选</label>
				<a href='javascript:void(0)' class='css-btn-mini' nc_type='batchbutton' uri='<?=users_url('shop_goods/del')?>' name='id' confirm='确定删除?'><i class='icon-trash'></i>删除</a>
			</td>
		</tr>
		<?php } ?>
	</thead>
	<tbody>
		<?php if (!empty($output['list'])) { ?>
		<?php foreach ($output['list'] as $key => $val) { ?>
		<tr>
			<th class='tc'><input class='checkitem tc' value='<?=$val['goods_commonid']?>' type='checkbox'></th>
			<th colspan='20'>商品货号：<?=$val['goods_serial']?></th>
		</tr>
		<tr>
			<td class='trigger'><i class='tip icon-plus-sign' nctype='ajaxGoodsList' data-comminid='<?=$val['goods_commonid']?>' title='点击展开查看此商品全部规格；规格值过多时请横向拖动区域内的滚动条进行浏览。'></i></td>
			<td><div class='pic-thumb'><img src='<?=$val['goods_image']?>' /></div></td>
			<td class='tl'>
				<dl class='goods-name'>
					<dt style='max-width: 450px !important;'><?=$val['goods_name']?></dt>
					<dd>商家货号：</dd>
					<dd class='serve'> 
					    <span class='open' title='商品状态'><i class='commend' style='width:30px;'><?php if($val['goods_state'] == 0){?>禁用<?php }else{?>正常<?php }?></i></span> 
						<span class='' title='商品手机端连接'>
							<i class='icon-link'></i>
							<div style='display:none;'>
								<?=front_url('goods/goods_info', array(), false, true)?>?i=<?=$output['config']['uniacid']?>&goods_id=<?=$val['goods_id']?>
							</div>
						</span>
					</dd>
				</dl>
			</td>
			<td><?php echo empty($output['gc_name_arr'][$val['gc_id']]) ? '暂无' : $output['gc_name_arr'][$val['gc_id']]?></td>
			<td><span><?php echo (isset($output['storage_array'][$val['goods_commonid']]['sum']) ? $output['storage_array'][$val['goods_commonid']]['sum'] : 0);?>件</span></td>
			<td class='goods-time'><?=date('Y-m-d', $val['goods_addtime'])?></td>
			<td class='nscs-table-handle'>
				<span>
					<a href='<?=users_url('shop_goods/publish', array('commonid' => $val['goods_commonid']))?>' class='btn-blue'><i class='icon-edit'></i>
						<p>编辑</p>
					</a>
				</span> 
				<span>
					<a href='javascript:void(0);' confirm='您确定要删除吗?' url='<?=users_url('shop_goods/del', array('id' => $val['goods_commonid']))?>' class='btn-red delete'><i class='icon-trash'></i>
						<p>删除</p>
					</a>
				</span>
			</td>
		</tr>
		<tr style='display: none;'>
			<td colspan='20'>
				<div class='css-goods-sku ps-container'></div>
			</td>
		</tr>
		<?php } ?>
		<?php } else { ?>
		<tr>
			<td colspan='20' class='norecord'><div class='warning-option'><i class='icon-warning-sign'></i><span>暂无记录</span> </div></td>
		</tr>
		<?php } ?>
	</tbody>
	<?php if (!empty($output['list'])) { ?>
	<tfoot>
		<tr>
			<td colspan='20'><div class='pagination'><?=$output['page']?></div></td>
		</tr>
	</tfoot>
	<?php } ?>
</table>
<script src='<?php echo STATIC_URL;?>/js/jquery.zclip/jquery.zclip.min.js'></script>
<script>
    $('.icon-link').zclip({
        path: '<?php echo STATIC_URL;?>/js/jquery.zclip/ZeroClipboard.swf',
        copy: function(){
            return $(this).next('div').html();
  　　　},
        afterCopy:function(){
            showSucc('连接复制成功');
        }
    });
</script> 
<script>
$(function(){
    // ajax获取商品列表
    $('i[nctype=ajaxGoodsList]').toggle(
        function(){
            $(this).removeClass('icon-plus-sign').addClass('icon-minus-sign');
            var _parenttr = $(this).parents('tr');
            var _commonid = $(this).attr('data-comminid');
            var _div = _parenttr.next().find('.css-goods-sku');
            if (_div.html() == '') {
                getAjax('<?=users_url('shop_goods/get_goods_list_ajax')?>' , {commonid : _commonid}, function(e){
                    if (e.state == 200) {
                        var _ul = $('<ul class=\'css-goods-sku-list\'></ul>');
						var list = e.data.goods_list;
                        $.each(list, function(i, o){
                            $('<li><div class=\'goods-thumb\' title=\'商家货号：' + o.goods_serial + '\'><image style=\'width:60px;\' src=\'' + o.goods_image + '\' ></div>' + o.goods_spec + '<p>售价：' + o.goods_price + '</p><p>供货价：' + o.goods_costprice + '</p></li>').appendTo(_ul);
                        });
                        _ul.appendTo(_div);
                        _parenttr.next().show();
                        _div.perfectScrollbar();
                    }
                });
            } else {
            	_parenttr.next().show()
            }
        },
        function(){
            $(this).removeClass('icon-minus-sign').addClass('icon-plus-sign');
            $(this).parents('tr').next().hide();
        }
    );
});
</script>