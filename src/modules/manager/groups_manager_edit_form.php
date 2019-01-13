<?php

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 
    function getSelect($name, $categorys, $current) {
        $select = '<select name="'.$name.'">';
        foreach ($categorys as $key => $value) {
            $select .= '<option value="';
            $select .= $key;
            $select .= '"';

            if ($key == $current) {
                $select .= ' selected="selected"';
            }

            $select .= '>';
            $select .= $key;
            $select .= '/';
            $select .= $value;
            $select .= '</option>';
        }
        $select .= '</select>';
        return $select;
    }

    $groupDao = BaseService::getService("GroupDao");

    $id = safe_get($_GET, "id");
    $entry = $groupDao->getGroup($id);
    $categorys = (object)$PUBLIC_CATEGORYS;
    
    $disableds = array('0'=>'正常', '1'=>'下线');

    $category_select = getSelect('category', $categorys, $entry->category);
    $status_select = getSelect('disabled', $disableds, $entry->disabled);

?><form name="editForm" id="editForm">
    <div class="form-header"><h2>修改群组:</h2></div>
    <dl class="form-content">
      <dt>ID:</dt>
      <dd><?=$entry->id?></dd>

      <dt>创建日期:</dt>
      <dd><?=$entry->created?></dd>
    
      <dt>管理群组:</dt>
      <dd><a href="?path=/manager/group_detail&group_id=<?=$entry->id?>" target="_group" class="rbutton">管理群组</a></dd>

      <dt>名称 (1 到 16 个字符):</dt>
      <dd><input type="text" class="text" name="name" required="required" placeholder="名称" value="<?=$entry->name?>"/></dd>
      
      <dt>描述 (1 到 128 个字符):</dt>
      <dd><textarea class="text" name="description" placeholder="描述" rows="5"><?=$entry->description?></textarea>
      
      <dt>类型 (0: 私有群组, 2: 企业号, 其他类型不接受):</dt>
      <dd><input type="text" class="text" name="privacy" required="required" placeholder="类型" value="<?=$entry->privacy?>"/></dd>
     
      <dt>群主 (填用户的数字编号):</dt>
      <dd><input type="text" class="text" name="owner_id" required="required" placeholder="用户ID" value="<?=$entry->owner_id?>"/></dd>

      <dd><input type="button" class="button" value="确定" onClick="onFormSubmit('editForm')"/></dd>
  
    </dl>
    <input type="hidden" name="action" value="edit"/>
    <input type="hidden" name="id" value="<?=$id?>"/>
    <input type="hidden" name="page" value="<?=$page?>"/>
</form>