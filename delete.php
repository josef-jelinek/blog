<?php
$out = array();
require 'header.php';

if (isGET('post') && isAdmin()) {
  $post = GET('post');
  $postEntry = readEntry('posts', $post);
  deleteEntry('posts', $post);
  foreach ($postEntry['tags'] as $tag) {
    $tagEntry = readEntry('tags', $tag);
    unset($tagEntry['posts'][$post]);
    saveEntry('tags', $tag, $tagEntry);
  }
  foreach ($postEntry['comment'] as $comment)
    deleteEntry('comments', $comment);
  home();
} else if (isGET('draft') && isAdmin()) {
  deleteEntry('drafts', GET('draft'));
  home();
} else if (isGET('comment') && (isAdmin() || isAuthor(GET('comment')))) {
  $comment = GET('comment');
  $commentEntry = readEntry('comments', $comment);
  deleteEntry('comments', $comment);
  $postEntry = readEntry('posts', $commentEntry['post']);
  unset($postEntry['comments'][$comment]);
  saveEntry('posts', $commentEntry['post'], $postEntry);
  redirect('view.php?post=' . $commentEntry['post'] . '#comments');
} else if (isGET('link') && isAdmin()) {
  deleteEntry('links', GET('link'));
  home();
} else if (isGET('tag') && isAdmin()) {
  $tag = GET('tag');
  $tagEntry = readEntry('tags', $tag);
  deleteEntry('tags', $tag);
  foreach ($tagEntry['posts'] as $post) {
    $postEntry = readEntry('posts', $post);
    $postEntry['tags'] = array_diff($postEntry['tags'], array($tag));
    saveEntry('posts', $post, $postEntry);
  }
  home();
} else {
  home();
}

require 'templates/page.php';
?>
