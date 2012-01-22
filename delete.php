<?php
$out['self'] = 'delete';
require 'header.php';

if (isGET('post') && isAdmin()) {
  $post = $_GET['post'];
  $postEntry = readEntry('posts', $post);
  if (checkBot()) {
    deleteEntry('posts', $post);
    foreach ($postEntry['tags'] as $tag) {
      $tagEntry = readEntry('tags', $tag);
      unset($tagEntry['posts'][$post]);
      saveEntry('tags', $tag, $tagEntry);
    }
    foreach ($postEntry['comment'] as $comment)
      deleteEntry('comments', $comment);
    home();
  } else {
    $out['title'] = $lang['deletePost'] . ': ' . $postEntry['title'];
    $out['content'] .= '<form action="/delete.php/post/' . $post . '" method="post">
    <p>' . submit() . '</p>
    </form>';
  }
} else if (isGET('comment') && (isAdmin() || isAuthor($_GET['comment']))) {
  $comment = $_GET['comment'];
  $commentEntry = readEntry('comments', $comment);
  if (checkBot()) {
    deleteEntry('comments', $comment);
    $postEntry = readEntry('posts', $commentEntry['post']);
    unset($postEntry['comments'][$comment]);
    saveEntry('posts', $commentEntry['post'], $postEntry);
    redirect('view.php/post/' . $commentEntry['post']);
  } else {
    $out['title'] = $lang['deleteComment'];
    $out['content'] .= '<form action="/delete.php/comment/' . $comment . '" method="post">
    <p>' . submit() . '</p>
    </form>';
  }
} else if (isGET('link') && isAdmin()) {
  $link = $_GET['link'];
  if (checkBot()) {
    deleteEntry('links', $_GET['link']);
    home();
  } else {
    $linkEntry = readEntry('links', $link);
    $out['title'] = $lang['deleteLink'] . ': ' . $linkEntry['name'];
    $out['content'] .= '<form action="/delete.php/link/' . $link . '" method="post">
    <p>' . submit() . '</p>
    </form>';
  }
} else if (isGET('tag') && isAdmin()) {
  $tag = $_GET['tag'];
  $tagEntry = readEntry('tags', $tag);
  if (checkBot()) {
    deleteEntry('tags', $tag);
    foreach ($tagEntry['posts'] as $post) {
      $postEntry = readEntry('posts', $post);
      $postEntry['tags'] = array_filter($postEntry['tags'], function ($e) use ($tag) { return ($e != $tag); });
      saveEntry('posts', $post, $postEntry);
    }
    home();
  } else {
    $out['title'] = $lang['deleteTag'] . ': ' . $tagEntry['name'];
    $out['content'] .= '<form action="/delete.php/tag/' . $tag . '" method="post">
    <p>' . submit() . '</p>
    </form>';
  }
} else {
  home();
}

require 'templates/page.php';
?>
