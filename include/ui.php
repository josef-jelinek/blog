<?php
function isGET($name) {
  return isset($_GET[$name]) && is_string($_GET[$name]);
}

function isPOST($name) {
  return isset($_POST[$name]) && is_string($_POST[$name]);
}

function message($msg) {
  global $out;
  $out['error'] .= '<div class="msg">' . $msg . '</div>';
}

function password($name) {
  global $lang;
  return $lang[$name] . ' <input type="password" name="' . $name . '">';
}

function text($name, $default = '') {
  global $lang;
  return $lang[$name] . ' <input type="text" name="' . $name . '" value="' . (isPOST($name) ? clean($_POST[$name]) : $default) . '">';
}

function textarea($name, $default = '') {
  return '<textarea name="' . $name . '" rows="10">' . (isPOST($name) ? clean($_POST[$name]) : $default) . '</textarea>';
}

function submitSafe($label) {
  $num1 = rand(0, 10);
  $num2 = rand(0, 10);
  $_SESSION['captcha'] = (string)($num1 * $num2);
  return $num1 . ' x ' . $num2 . ' = ? <input type="text" name="captcha" style="width:50px"> <input type="submit" value="' . $label . '">';
}

function submitAdmin($label) {
  return '<input type="submit" value="' . $label . '">';
}

function select($name, $options, $default = '') {
  global $lang;
  $selected = isPOST($name) && isset($options[$_POST[$name]]) ? $_POST[$name] : $default;
  $out = $lang[$name] . ' <select name="' . $name . '">';
  foreach ($options as $value => $option)
    $out .= '<option value="' . $value . '"' . ($value == $selected ? ' selected' : '') . '>' . $option . '</option>';
  return $out . '</select>';
}

function multiselect($name, $options, $selected) {
  global $lang;
  $out = $lang[$name] . ' <select name="' . $name . '[]" multiple>';
  foreach ($options as $value => $option)
    $out .= '<option value="' . $value . '"' . ($selected && in_array($value, $selected) ? ' selected' : '') . '>' . $option . '</option>';
  return $out . '</select>';
}

function box($text) {
  return '<div class="box">' . content(clean($text)) . '</div>';
}

function check($name, $max = 0) {
  global $lang;
  if (!isPOST($name))
    return false;
  $len = strlen(trim($_POST[$name]));
  if ($len >= 1 && ($max == 0 || $len <= $max))
    return true;
  message($lang[$name] . $lang[$len == 0 ? 'errorEmpty' : 'errorTooLong']);
  return false;
}

function checkBot() {
  global $lang;
  if (!isPOST('captcha'))
    return false;
  if (isset($_SESSION['captcha']) && $_POST['captcha'] === $_SESSION['captcha'])
    return true;
  message($lang['errorBot'] . ' "' . $_POST['captcha'] . '" <> ' . $_SESSION['captcha']);
  return false;
}

function unslash($text) {
  return get_magic_quotes_gpc() ? stripslashes($text) : $text;
}

function clean($text) {
  return htmlspecialchars(trim(unslash($text)), ENT_QUOTES);
}

function content($text) {
  return nl2br($text);
}

function commenter($name) {
  $parts = explode('#', $name, 2);
  return $parts[0] . (isset($parts[1]) ? '#' . substr(md5($parts[1]), -5) : '');
}

function managePost($post) {
  return isAdmin() ? '<a href="/edit.php/post/' . $post . '" class="edit"></a><a href="/delete.php/post/' . $post . '" class="delete"></a>' : '';
}

function manageComment($comment) {
  return isAdmin() || isAuthor($comment) ? '<a href="/edit.php/comment/' . $comment . '" class="edit"></a><a href="/delete.php/comment/' . $comment . '" class="delete"></a>' : '';
}

function manageTag($tag) {
  return isAdmin() ? '<a href="/edit.php/tag/' . $tag . '" class="edit"></a><a href="/delete.php/tag/' . $tag . '" class="delete"></a>' : '';
}

function manageLink($link) {
  return isAdmin() ? '<a href="/edit.php/link/' . $link . '" class="edit"></a><a href="/delete.php/link/' . $link . '" class="delete"></a>' : '';
}

function paging($page, $pages, $loc) {
  global $lang;
  if ($pages <= 1)
    return '';
  $parts = explode('#', $loc, 2);
  $hash = isset($parts[1]) ? '#' . $parts[1] : '';
  $base = $parts[0] . '/pages/';
  $out = '<div id="page">';
  for ($i = 1; $i <= $pages; $i++)
    $out .= $page === $i ? '<b>' . $i . '</b>' : '<a href="' . $base . $i . $hash . '">' . $i . '</a>';
  return $out . ($page < $pages ? '<a href="' . $base . ($page + 1) . $hash . '">' . $lang[nextPage] . '</a>' : '') . '</div>';
}

function page($pages) {
  return isGET('pages') && $_GET['pages'] >= 1 && $_GET['pages'] <= $pages ? (int)$_GET['pages'] : 1;
}

function pages($items) {
  $itemNum = count($items);
  return $itemNum === 0 ? 1 : (int)ceil($itemNum / 8);
}

function pageItems($items, $page) {
  return array_slice($items, 8 * ($page - 1), 8);
}

function pageOf($item, $items) {
  return (int)(array_search($item, array_values($items), true) / 8) + 1;
}
?>
