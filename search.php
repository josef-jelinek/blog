<?php
require 'header.php';

if (check('text')) {
  $text = clean($_POST['text']);
  $foundPosts = array();
  foreach (listEntry('posts') as $post) {
    $postEntry = readEntry('posts', $post);
    if (stripos($postEntry['title'], $text) !== false || stripos($postEntry['content'], $text) !== false)
      $foundPosts[$post] = $postEntry['title'];
  }
  $out['title'] = $lang['search'];
  $out['content'] .= '<ul>';
  if ($foundPosts) {
    foreach ($foundPosts as $post => $title)
      $out['content'] .= '<li><a href="/view.php/post/' . $post . '">' . $title . '</a>' . managePost($post) . ' &mdash; ' . toDate($post) . '</li>';
  }
  $out['content'] .= '</ul>';
} else {
  home();
}

require 'templates/page.php';
?>
