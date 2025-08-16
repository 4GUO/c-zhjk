<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class="page">
    <div class="fixed-empty"></div>
    <form method='get' action='<?=users_url('index/untihuoquan_list')?>'>
        <table class='search-form'>	
            <tbody>
                <tr>
                    <td>&nbsp;</td>
                    <th> 
                        <select name='search_type'>
                            <option value='nickname' <?php if(input('get.search_type', '') == 'nickname'){?> selected='selected'<?php }?>>昵称</option>
                            <option value='truename' <?php if(input('get.search_type', '') == 'truename'){?> selected='selected'<?php }?>>姓名</option>
                            <option value='mobile' <?php if(input('get.search_type', '') == 'mobile'){?> selected='selected'<?php }?>>手机号</option>
                        </select>
                    </th>
                    <td class='w160'><input class='text w150' name='keyword' value='<?=input('get.keyword', '')?>' type='text' placeholder='请输入搜索关键词'></td>
                    <th style='padding:0px 15px;'> 
                        <input class='text w150' name='query_start_date' value='<?=input('get.query_start_date', '')?>' type='text' placeholder='开始日期' onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})">
                    </th>
                    <th style='padding:0px 15px;'> 
                        <input class='text w150' name='query_end_date' value='<?=input('get.query_end_date', '')?>' type='text' placeholder='结束日期' onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})">
                    </th>
                    <td class='tc w70'>
                        <label class='submit-border'>
                            <input class='submit' value='搜索' type='submit'>
                        </label>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
    
    <table class='css-default-table' id='untihuoquan_list'>
        <thead>
            <tr>
                <th class='w60'>ID</th>
                <th class='w80'>头像</th>
                <th class='w120'>会员昵称</th>
                <th class='w120'>真实姓名</th>
                <th class='w120'>手机号</th>
                <th class='w100'>会员级别</th>
                <th class='w120'>提货券金额</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($output['list'])) { ?>
            <?php foreach ($output['list'] as $key => $val) { ?>
            <tr>
                <td><?=$val['id']?></td>
                <td>
                    <div class='pic-thumb'>
                        <?php if(!empty($output['member_list'][$val['uid']]['headimg'])) {?>
                            <img src='<?php echo $output['member_list'][$val['uid']]['headimg'];?>' style='width:40px;height:40px;border-radius:50%;' />
                        <?php } else { ?>
                            <img src='<?=STATIC_URL?>/shop/img/default_user.png' style='width:40px;height:40px;border-radius:50%;' />
                        <?php } ?>
                    </div>
                </td>
                <td><?php echo $output['member_list'][$val['uid']]['nickname'] ?? '未知';?></td>
                <td><?php echo $output['member_list'][$val['uid']]['truename'] ?? '未设置';?></td>
                <td><?php echo $output['member_list'][$val['uid']]['mobile'] ?? '未设置';?></td>
                <td><?php echo empty($output['level_list'][$output['member_list'][$val['uid']]['level_id']]) ? '未知' : $output['level_list'][$output['member_list'][$val['uid']]['level_id']]['level_name'];?></td>
                <td><span class='green'>&yen;<?=number_format($val['amount'], 2)?></span></td>
            </tr>
            <?php } ?>		
            <?php } else { ?>
            <tr>
                <td colspan='9' class='norecord'><div class='warning-option'><i class='icon-warning-sign'></i><span>暂无记录</span> </div></td>
            </tr>
            <?php } ?>
        </tbody>
        <?php if (!empty($output['list'])) { ?>
        <tfoot>
            <tr>
                <td colspan='9'><div class='pagination'><?=$output['page']?></div></td>
            </tr>
        </tfoot>
        <?php } ?>
    </table>
</div>

<script type="text/javascript" src="<?=STATIC_URL?>/js/My97DatePicker/WdatePicker.js"></script>
