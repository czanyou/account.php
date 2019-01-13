  <h2>个人</h2>
  <ul>
  <?php if (VisionIsAdmin()) { ?>
	  <li id="mitem_home">
	  	<a href="<?=S_PATH?>/?path=/manager/profile">个人信息</a></li>
  </ul>

  <hr/>
  <h2>管理</h2>

  <ul>
	  <li id="mitem_user">
	  	<a href="<?=S_PATH?>/?path=/manager/user">用户管理</a></li>
	  <li id="mitem_group">
	  	<a href="<?=S_PATH?>/?path=/manager/groups">群组管理</a></li>
	  <li id="mitem_user_session">
	  	<a href="<?=S_PATH?>/?path=/manager/user_session">会话管理</a></li>
	  <li id="mitem_settings">
	  	<a href="<?=S_PATH?>/?path=/manager/settings">系统参数</a></li>
	  
  <?php } else { ?>
	  <li id="mitem_group">
	  	<a href="<?=S_PATH?>/?path=/manager/group_groups">群组管理</a></li>
	
  <?php } ?>
  </ul>

  <div id="side_menu"></div>
