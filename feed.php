<?php
$out = array();
require 'header.php';

function getFeedEntry($title, $url, $date, $content) {
  return '
  <entry>
    <title>' . $title . '</title>
    <link href="' . $url . '"/>
    <id>' . $url . '</id>
    <updated>' . $date . '</updated>
    <content type="html">' . str_replace('<', '&lt;', str_replace('&', '&amp;', str_replace('<br />', '<br>', $content))) . '</content>
  </entry>';
}

if (isGET('comments')) {
  $out['title'] = $lang['comments'];
  $out['type'] = 'comments';
  $items = listEntry('comments');
  rsort($items);
  $items = array_slice($items, 0, 100);
  if ($items) {
    foreach ($items as $item) {
      $itemData = readEntry('comments', $item);
      $parentData = readEntry('posts', $itemData['post']);
      $title = clean($itemData['commenter'] . $lang['commented'] . $parentData['title']);
      $url = $out['baseURL'] . 'view.php/post/' . $itemData['post'] . '/pages/' . pageOf($item, $parentData['comments']) . '#' . $item;
      $out['content'] .= getFeedEntry($title, $url, toDate($item, 'c'), content($itemData['content']));
    }
  }
} else {
  $out['title'] = $lang['posts'];
  $out['type'] = 'posts';
  $items = listEntry('posts');
  rsort($items);
  $items = array_slice($items, 0, 100);
  if ($items) {
    foreach ($items as $item) {
      $itemData = readEntry('posts', $item);
      $title = clean($itemData['title']);
      $url = $out['baseURL'] . 'view.php/post/' . $item;
      $out['content'] .= getFeedEntry($title, $url, toDate($item, 'c'), $itemData['content']);
    }
  }
}

require 'templates/feed.php';
?>
