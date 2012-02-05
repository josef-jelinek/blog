<?php
function urlPath() {
  $out = array();
  if (isset($_SERVER['PATH_INFO'])) {
    $info = explode('/', $_SERVER['PATH_INFO']);
    $infoNum = count($info);
    for ($i = 1; $i < $infoNum; $i += 2) {
      if ($info[$i] !== '')
        $out[$info[$i]] = isset($info[$i + 1]) ? $info[$i + 1] : '';
    }
  }
  return $out;
}

function baseURL() {
  $dir = dirname($_SERVER['SCRIPT_NAME']);
  return 'http://' . $_SERVER['SERVER_NAME'] . $dir . ($dir === '/' ? '' : '/');
}

function redirect($loc) {
  header('Location: ' . baseURL() . $loc);
  exit;
}

function shortNum($int) {
  return $int < 1000 ? $int : round($int / 1000, 1) . 'K';
}

function toDate($id, $pattern = 'Y/m/d H:i') {
  global $lang;
  $date = substr($id, 0, 19);
  $date = substr_replace($date, 'T', 10, 1);
  $date = substr_replace($date, ':', 13, 1);
  $date = substr_replace($date, ':', 16, 1);
  $timestamp = strtotime($date);
  $diff = time() - $timestamp;
  if ($pattern !== 'c' && $diff < 604800) { // 7 days
    if ($diff === 0)
      return $lang['now'];
    $periods = array(86400 => $lang['day'], 3600 => $lang['hour'], 60 => $lang['minute'], 1 => $lang['second']);
    foreach ($periods as $key => $value) {
      if ($diff >= $key) {
        $num = (int)($diff / $key);
        return $num . ' ' . $value . ($num > 1 ? $lang['plural'] : '') . ' ' . $lang['ago'];
      }
    }
  }
  return date($pattern, $timestamp);
}

function hide($text) {
  return md5($text . md5($text));
}

function isAdmin() {
  return $_SESSION['role'] === 'admin';
}

function isAuthor($entry) {
  return isset($_SESSION[$entry]);
}

function login($password) {
  global $config;
  if (hide($password) !== $config['password'])
    return false;
  $_SESSION['role'] = 'admin';
  return true;
}
?>
