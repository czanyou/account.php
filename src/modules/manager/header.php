  <header><div class="header-inner">
    <div class="left-item">
    <a href="<?=S_PATH?>/" class="logoc">&nbsp;</a>
    <div class="header-menu header-logo">
      <a href="<?=S_PATH?>/">后台管理</a></div>
    <div class="header-menu" id="header-menu"></div></div>
    <?php if (VisionIsLogin()) { ?>
        <div class="right-item"><a href="javascript:onLogout();"><label>退出</label></a></div>
    <?php } else { ?>
        <div class="right-item"><a href="index.php"><label>登录</label></a></div>
    <?php } ?>
  </div></header>
  <div class="aside" id="aside-bar">
    <?php require_once("manager/left.php"); ?>
  </div>