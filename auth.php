<?php
$out = array();
require 'header.php';

if (isGET('login')) {
  if (checkBot() && check('password') && login($_POST['password'])) {
    session_regenerate_id(true);
    home();
  } else {
    $out['title'] = $lang['login'];
    $out['content'] .= '<form action="/auth.php/login" method="post">
    <p>' . password('password') . '</p>
    <p>' . submitSafe($lang['confirm']) . '</p>
    </form>';
  }
} else if (isGET('logout') && isAdmin()) {
  $_SESSION['role'] = '';
  home();
} else if (isGET('test') && isAdmin()) {
  $out['title'] = $lang['login'];
  $out['content'] .= '<form action="/auth.php/test" method="post">
  <p>' . password('password') . '</p>
  <p>' . submitAdmin($lang['confirm']) . '</p>
  </form>';
  if (check('password'))
    $out['content'] .= box(hide($_POST['password']));
} else {
  home();
}

require 'templates/page.php';
?>
