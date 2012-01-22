<?php
if (!isset($out)) exit;
header('Content-Type: text/html; charset=UTF-8');
?><!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <title><?php echo $out['title'] . ' - ' . $config['title'];?></title>
  <meta name="description" content="<?php echo $out['title'];?>">
  <link href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,400&amp;subset=latin-ext,latin" rel="stylesheet" type="text/css">
  <link rel="stylesheet" type="text/css" href="/css/main.css">
  <link rel="alternate" type="application/atom+xml" href="/feed.php/posts" title="<?php echo $config['title'];?>">
</head>
<body>
  <div id="header"><?php echo $config['title'];?></div>
  <div id="menu">
    <div class="nav">
      <a href="/index.php/posts"><?php echo $lang['posts'];?></a>
      <a href="/index.php/comments"><?php echo $lang['comments'];?></a>
      <?php if (isAdmin()) echo '<a href="/add.php/post">' . $lang['newPost'] . '</a>';?>
    </div>
    <div class="ctl">
      <a href="/feed.php/posts" class="postfeed"><?php echo $lang['posts'];?></a>
      <a href="/feed.php/comments" class="commentfeed"><?php echo $lang['comments'];?></a>
      <?php echo isAdmin()
        ? '<a href="/auth.php/logout" class="logout">' . $lang['logout'] . '</a>'
        : '<a href="/auth.php/login" class="login">' . $lang['login'] . '</a>';?>
      <form action="/search.php" method="post" class="search">
        <input type="text" name="text" value="" class="text"><input type="submit" value="" class="submit">
      </form>
    </div>
  </div>
  <div id="main">
    <div id="content">
      <?php echo $out['error'];?>
      <?php echo isSet($out['titleHtml']) ? $out['titleHtml'] : '<h1>' . $out['title'] . '</h1>';?>
      <?php echo $out['content'];?>
    </div>
  </div>
  <div id="sidebar">
    <?php if (isAdmin() || $out['linkListItems'] != '') {?>
    <h3><?php echo $lang['links'] . $out['addLink'];?></h3>
    <ul><?php echo $out['linkListItems'];?></ul>
    <?php }?>
    <?php if (isAdmin() || $out['tagLinks'] != '') {?>
    <h3><?php echo $lang['tags'] . $out['addTag'];?></h3>
    <div class="tags"><?php echo $out['tagLinks'];?></div>
    <?php }?>
    <h3><?php echo $lang['archive'];?></h3>
    <ul><?php echo $out['archiveListItems'];?></ul>
  </div>
  <div id="footer"></div>
</body>
</html>
