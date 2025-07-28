<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<style>
	.css-layout-right {
		background: none;
	}
	.css-layout-right .main-content {
		padding: 0;
	}
	.tab-content>.tab-pane {
		margin-top: 10px;
	}
	.tab-content>.tab-pane>.fui-list-group {
		border-bottom: 0;
	}
	.userNum, .goodsNum, .orderNum {
		height: 4.5rem;
		width: 4.5rem;
		margin: 0 auto;
		cursor: pointer;
		color: #000;
	}
	.nav {
		display: -webkit-box;
		display: -webkit-flex;
		display: -ms-flexbox;
		display: flex;
		padding-left: 0;
		margin-bottom: 0;
		list-style: none;
	}
	.panel-1 .nav-left {
		height: 4.8rem;
		line-height: 4.8rem;
		margin-left: 0.2rem;
	}
	.panel-1 .nav-left span { 
		font-size: 14px;
	}
	.nav select {
		width: 50%;
		margin: 1rem 1rem;
	}
	.panel-1 {
		height: 13rem;
	}
	.panel {
		padding: 0 1rem;
		background: #fff;
	}
	.no-border {
		border:none;
	}
	.flex-items {
		display: flex;
	}
	.flex-items .flex-item {
		flex: 1;
	}
	.todayboxs {
		margin: 0 0 25px 0;
	}
	.todayboxs .flex-item {
		background-color: #fff;
		width: 100%;
		height: 104px;
		padding: 10px;
		display: flex;
		align-items: center;
		margin-right: 10px;
		color: #333;
		box-shadow: 0px 4px 8px 0px rgba(0, 0, 0, 0.05);
	}

	.todayboxs .flex-item:last-child {
		margin-right: 0;
	}
	.todayboxs .icon {
		width: 56px;
		margin-right: 16px;
	}
	.todayboxs .num {
		font-size: 24px;
	}
	.todayboxs .title {
		color: #747474;
	}
	.row-panel {
		margin-bottom: 24px;
		display: flex;
	}

	.row-panel .row-panel-7 {
		width: 50%;
		flex-shrink: 1;
	}

	.row-panel .row-panel-5 {
		width: 50%;
		margin-left: 24px;
		flex-shrink: 0;
	}

	.mypanel {
		position: relative;
		background-color: #fff;
		padding: 0 20px;
		box-shadow: 0px 4px 8px 0px rgba(0, 0, 0, 0.05);
	}

	.mypanel .mypanel-heading {
		font-size: 15px;
		font-weight: 600;
		border-bottom: 1px solid #f0f0f0;
		height: 48px;
		line-height: 48px;
	}
	.tasks .flex-item {
		height: 75px;
		display: flex;
		align-items: center;
		justify-content: space-between;
		margin-right: 20px;
		border-bottom: 1px solid #f0f0f0;
		color: #747474;
	}
	.tasks .flex-item:last-child {
		margin-right: 0;
	}
	.tasks .num {
		font-size: 15px;
		color: #9e9e9e;
		font-weight: 600;
	}
	.tasks .num.hasnum {
		color: #436be6;
	}
	.chart-box {
		width: 100%;
		height: 192px;
	}

	.tasks-panel .tasks:last-child .flex-item {
		border-bottom: none;
	}

	.quick-panel .mypanel-body, .total-panel .mypanel-body {
		padding: 20px 0;
	}

	.quick-nav .flex-item, .total-mes .flex-item {
		display: flex;
		align-items: center;
		justify-content: center;
		flex-direction: column;
		height: 68px;
		margin-bottom: 20px;
		margin-right: 20px;
		color: #747474;
	}

	.total-mes .flex-item {
		margin-bottom: 20px;
		height: 68px;
	}

	.quick-nav .flex-item:last-child, .total-mes .flex-item:last-child {
		margin-right: 0;
	}

	.total-mes .num {
		height: 48px;
		line-height: 48px;
		font-size: 15px;
		color: #333;
		font-weight: 600;
	}

	.quick-nav .flex-item:hover {
		color: #436be6;
	}

	.quick-nav .iconfont {
		height: 48px;
		line-height: 48px;
		font-size: 24px;
		display: block;
		transition: all 0.3s;
	}

	.quick-nav .flex-item:hover i {
		font-size: 48px;
	}
</style>
<div class='layui-row'>
	<div class='layui-row layui-col-space15'>
		<div class='flex-items todayboxs'>
			<a class='flex-item' href='javascript:;'>
				<img class='icon' src='<?=STATIC_URL?>/admin/images/Block-3.png'>
				<div class='text'>
					<div class='num'><?=$output['today_pay_order_count']?></div>
					<div class='title'>今日付款订单</div>
				</div>
			</a>
			<a class='flex-item' href='javascript:;'> 
				<img class='icon' src='<?=STATIC_URL?>/admin/images/Block-2.png'>
				<div class='text'>
					<div class='num'><?=$output['today_pay_order_money']?></div>
					<div class='title'>今日付款金额</div>
				</div>
			</a>
			<a class='flex-item' href='javascript:;'> 
				<img class='icon' src='<?=STATIC_URL?>/admin/images/Block-5.png'>
				<div class='text'>
					<div class='num'><?=$output['pay_order_money']?></div>
					<div class='title'>订单金额</div>
				</div>
			</a>
		</div>
		
		<div class='row-panel'>
			<div class='row-panel-7'>
				<div class='mypanel tasks-panel'>
					<div class='mypanel-heading'>待处理事务</div>
					<div class='mypanel-body'>
						<div class='flex-items tasks'>
							<div class='flex-item'> 
								<div>待发货</div>
								<a class='num' href='<?=_url('shop_order/index', array('state_type' => 'state_pay'))?>'><strong id='state_pay'></strong></a>
							</div>
							
							<div class='flex-item'>
								<div>待退款</div>
								<a class='num hasnum' href='javascript:;'><strong id='state_refund'></strong></a>
							</div>
						</div>
						<div class='flex-items tasks'>
							<div class='flex-item'>
								<div>待审核商品</div>
								<a class='num hasnum' href='<?php echo _url('goods/index', array('type' => 'warehouse')); ?>'><strong id='stock_goods_count'></strong></a>
							</div> 
						</div>
					</div>
				</div>
			</div>
			<div class='row-panel-5'>
				<div class='mypanel'>
					<div class='mypanel-heading'>交易统计</div>
					<div class='mypanel-body'>
						<div class='ibox-loading' id='echarts-line-chart-loading'></div>
						<div class='chart-box' id='main'  style='-webkit-tap-highlight-color: transparent; user-select: none; position: relative;height: 225px;'>
							<div class='layui-carousel layadmin-carousel layadmin-dataview' data-anim='fade' lay-filter='LAY-index-normline'>
							  <div carousel-item id='LAY-index-normline'>
								<div><i class='layui-icon layui-icon-loading1 layadmin-loading'></i></div>
							  </div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class='row-panel'>
			<div class='row-panel-7'>
				<div class='mypanel total-panel'>
					<div class='mypanel-heading'>本月商品销售排行（Top10）</div>
					<div class='mypanel-body'>
						<div id='echat_month_goods_sales' style='height:460px;'></div>
					</div>
				</div>
			</div>
			<div class='row-panel-5'>
				<div class='mypanel total-panel'>
					<div class='mypanel-heading'>上月商品销售排行（Top10）</div>
					<div class='mypanel-body'>
						<div id='echat_lastmonth_goods_sales' style='height:460px;'></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script src='<?=STATIC_URL?>/layuiadmin/lib/extend/echarts.min.js'></script>
<script>
$(function(){
	var timestamp = Math.round(new Date().getTime()/1000/60);//异步URL一分钟变化一次
	getAjax('<?=_url('index/statistics')?>?rand=' + timestamp, {}, function(e) {
        if (e.state == 400) return false;
		var data = e.data;
        for(var a in data) {
            if(data[a] != 'undefined' && data[a] != 0) {
                if (a != 'goodscount' && a != 'imagecount') {
                    $('#' + a).parents('a').addClass('num');
                }
                $('#' + a).html(data[a]);
            }
        }
    });
	loadEchartsLine();
	load_echat_month_goods_sales();
	load_echat_lastmonth_goods_sales();
});
function loadEchartsLine() {
	var hasLineChart = $('#main').length > 0;
	if(hasLineChart){
		var lineChart = echarts.init(document.getElementById('main'));
	}
	window.onresize = function () {
		if(hasLineChart) {
			lineChart.resize();
		}
	};		
	$.ajax({
		type: 'GET',
		url: '<?=_url('index/load_order_chart')?>',
		data: {},
		dataType: 'json',
		success: function (json) {
			var option = {
				title: {
					text: ''
				},
				tooltip: {
					trigger: 'axis',
					formatter: function (a) {
						var str = '';
						a.forEach(function (item) {
							str += item.seriesName + ':' + item.value + (item.seriesIndex > 2 ? '%' : '') + '<br/>';
						});
						return str;
					}
				},
				legend: {
					data: []
				},
				grid: {
					left: 50,
					right: 20,
					top: 40,
					bottom: 10,
					containLabel: true
				},
				xAxis: {
					type: 'category',
					axisLabel: {
						rotate: 30
					},
					boundaryGap: false,
					data: []
				},
				yAxis: [
					{
						type: 'value',
						name: '金额',
					}
				],
				series: [
					{
						areaStyle: { normal: {} },
					},
				]
			};
			if(hasLineChart) {
				var lines = json.data.lines;
				
				option.xAxis.data = lines.payAmountLine.xAxisData;
				option.series = [];
				option.series.push(buildLine('付款金额', lines.payAmountLine, 0));
				option.series.push(buildLine2('退款金额', lines.payAmountLine, 0));
					
				lineChart.setOption(option);
				lineChart.resize(); 
			}
			$('#echarts-line-chart-loading').hide();
			$('#main').show();	
		}
	})
}
function buildLine(name, data, yIndex) {
	return {
		name: name,
		type: 'line',
		areaStyle: { normal: {} },
		smooth: true,
		data: data.seriesData[0].data,
		yAxisIndex: yIndex
	};
}
function buildLine2(name, data, yIndex) {
	return {
		name: name,
		type: 'line',
		areaStyle: { normal: {} },
		smooth: true,
		data: data.seriesData[1].data,
		yAxisIndex: yIndex
	};
}
function load_echat_month_goods_sales() {
	var hasLineChart = $('#echat_month_goods_sales').length > 0;
	if (hasLineChart) {
		var lineChart = echarts.init(document.getElementById('echat_month_goods_sales'));
	}
	window.onresize = function () {
		if(hasLineChart) {
			lineChart.resize();
		}
	};
			
	$.ajax({
		type: 'GET',
		url: '<?=_url('index/load_echat_month_goods_sales', array('type' => 1))?>',
		dataType: 'json',
		success: function (data) {
			if (data.code == 0) {
				var le_arr = [];
				var data_obj_arr  = [];		
				for( var i in data.list ) {
					le_arr.push(  data.list[i].name  );
					data_obj_arr.push({value:data.list[i].total, name:data.list[i].name });
				}
				var option = null;
				option = {
					title : {
						text: '销售金额'+data.total,
						subtext: '销售总数：'+data.total_quantity+' ('+data.month+')',
						x:'center'
					},
					tooltip : {
						trigger: 'item',
						formatter: '{a} <br/>{b} : {c} ({d}%)'
					},
					legend: {
						orient: 'vertical',
						left: 'left',
						data: le_arr
					},
					series : [
						{
							name: '销售金额',
							type: 'pie',
							radius : '55%',
							center: ['50%', '60%'],
							data:data_obj_arr,
							itemStyle: {
								emphasis: {
									shadowBlur: 10,
									shadowOffsetX: 0,
									shadowColor: 'rgba(0, 0, 0, 0.5)'
								}
							}
						}
					]
				};
					
				if (option && typeof option === 'object') {
					lineChart.setOption(option, true);
				}
			}
		}
	})		
}

function load_echat_lastmonth_goods_sales() {
	var hasLineChart = $('#echat_lastmonth_goods_sales').length > 0;
	if (hasLineChart) {
		var lineChart = echarts.init(document.getElementById('echat_lastmonth_goods_sales'));
	}
	window.onresize = function () {
		if(hasLineChart) {
			lineChart.resize();
		}
	};
			
	$.ajax({
		type: 'GET',
		url: '<?=_url('index/load_echat_month_goods_sales', array('type' => 2))?>',
		dataType: 'json',
		success: function (data) {
			if(data.code == 0) {	
				var le_arr = [];
				var data_obj_arr  = [];
						
				for( var i in data.list ) {
					le_arr.push(  data.list[i].name  );
					data_obj_arr.push({value:data.list[i].total, name:data.list[i].name });
				}
				var option = null;
				option = {
					title : {
						text: '销售金额'+data.total,
						subtext: '销售总数：'+data.total_quantity+'  ('+data.month+')',
						x:'center'
					},
					tooltip : {
						trigger: 'item',
						formatter: '{a} <br/>{b} : {c} ({d}%)'
					},
					legend: {
						orient: 'vertical',
						left: 'left',
						data: le_arr
					},
					series : [
						{
							name: '销售金额',
							type: 'pie',
							radius : '55%',
							center: ['50%', '60%'],
							data: data_obj_arr,
							itemStyle: {
								emphasis: {
									shadowBlur: 10,
									shadowOffsetX: 0,
									shadowColor: 'rgba(0, 0, 0, 0.5)'
								}
							}
						}
					]
				};
				if (option && typeof option === 'object') {
					lineChart.setOption(option, true);
				}
			}
		}
	})
}
</script>