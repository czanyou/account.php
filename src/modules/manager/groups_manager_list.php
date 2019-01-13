<?php
   $groupDao = BaseService::getService("GroupDao");
    
    $params = array();
    $page   = managerSetPagerParams($_GET, $params);
    $result = $groupDao->findGroups($params);
    $total  = $groupDao->getGroupCount($params);

?>
  <div class="frame-main">
    <div id="wrapper" class="wrapper"><table class="grid-table">
        <tr>
            <th>名称 (共 <?=$total?> 条记录)</th>
            <th class="align_center">编号</th>
            <th class="align_center">成员</th>
            <th class="align_center">设备</th>
            <th class="align_center">群主</th>
            <th class="align_center">操作</th>
        </tr>
        
        <?php if (!$result) { ?>
          <tr><td colspan="6">没有找到记录</td></tr>
        <?php } ?>        

        <?php foreach ($result as $key) { 
            $clazz = ($key->privacy == 2) ? 'business' : 'private';

        ?><tr class='<?=$clazz?>'>
            <td><a href="javascript:onEdit(<?=$key->id?>)"><?=$key->name?></a></td>
            <td class="align_center"><?=$key->id?></td>  
            <td class="align_center"><?=$key->member_count?></td>
            <td class="align_center"><?=$key->device_count?></td>
            <td class="align_center"><?=$key->owner_id?></td>   
            <td class="align_center"><a href="javascript:onRemove(<?=$key->id?>)">删除</a></td>
        </tr>
        <?php } ?>
    </table></div>

    <div class="grid-footer">
        <?php managerShowPager($total, $page, '?path=/manager/groups&'); ?>
    </div>
  </div>