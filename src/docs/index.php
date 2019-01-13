<!DOCTYPE html>
<?php

require_once('common.php');

global $page;

$file = safe_get($_GET, 'f', 'api_index.md');
$page = safe_get($_GET, 'p', 'api');
$articleHtml = md_load($page, $file);


$mode = safe_get($_GET, 'm');
if ($mode == 'embed') {
    echo $articleHtml;
    return;
}

?>
<html>
<head>
    <meta http-equiv="mobile-agent" content="format=html5;"/>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no"/>
    <title>Vision Server</title>
    <link rel="stylesheet" href="assets/style.css?q=<?=$Q?>" />
    <link rel="stylesheet" href="assets/highlight.css?q=<?=$Q?>">
    <link href="favicon.ico?q=<?=$Q?>" rel="shortcut icon">
    <script src="assets/jquery.js?q=<?=$Q?>"></script>
	<script src="assets/raphael.js?q=<?=$Q?>"></script>
    <script src="assets/underscore.js?q=<?=$Q?>"></script>
    <script src="assets/sequence-diagram.js?q=<?=$Q?>"></script>
    <script src="assets/highlight.js?q=<?=$Q?>"></script>
    <script>
    var page = "<?=$page?>";

    $(document).ready(function() {
        $("#header-nav a").each(function() {
            var $this = $(this)
            if ($this.attr('name') == page) {
                $this.addClass('current')
            }
        })

        try {
            var seq = $(".language-seq");
            if (seq && seq.sequenceDiagram) {
                seq.sequenceDiagram({theme: 'simple'});
            }
        } catch (e) {
            console.log(e)
        }

        hljs.initHighlighting();

        $(".leftmenu a").each(function() {
            var href = $(this).attr('href');
            href = location.pathname + '?p=' + page + '&f=' + href + '.md';

            $(this).click(function() {
                var url = href + "&m=embed"
                window.history.pushState({}, 0, href);
                $("#article").hide().load(url, null, function() {

                    $(this).find('pre code').each(function(i, block) {
                        hljs.highlightBlock(block);
                    });

                    $(this).fadeIn();
                })
                return false;
            })
        })

    })
    </script>
</head>
<body>
<header id="header" class="header">
  <div id="header-inner" class="header-inner">
    <a class="logo" href="?h=home&q=<?=$Q?>">
      <img src="assets/logo.png?q=<?=$Q?>.1" alt="logo"
      /><span>Vision Server - 开放平台</span></a>
    <nav id="header-nav">
      <a name="api" href="?p=api&f=api_index.md&q=<?=$Q?>">API</a>
    </nav>
  </div>
</header>

<div id="wrapper"><div class="main-wrapper">
  <?php require_once($page . '/menu.html') ?>

  <div class="post"><div id="article" class="article">
    <?= $articleHtml ?>
  </div></div>

</div></div>

<footer id="footer" class="footer">
  <div id="backtop-wrapper"><a href="#">返回顶部</a></div>
</footer>
</body>
</html>
