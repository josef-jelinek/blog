<?php
$out['self'] = 'edit';
require 'header.php';

if (isGET('post') && isAdmin() && isValidEntry('posts', $_GET['post'])) {
  $post = $_GET['post'];
  $postEntry = readEntry('posts', $post);
  if (checkBot() && check('title') && check('content')) {
    $postEntry['title'] = clean($_POST['title']);
    $postEntry['content'] = $_POST['content'];
    $postEntry['locked'] = $_POST['locked'] === 'yes';
    $postEntry['published'] = $_POST['published'] === 'yes';
    $newTags = $_POST['tags'] ? $_POST['tags'] : array();
    $addedTags = array_diff($newTags, $postEntry['tags']);
    $removedTags = array_diff($postEntry['tags'], $newTags);
    foreach ($removedTags as $tag) {
      $tagEntry = readEntry('tags', $tag);
      unset($tagEntry['posts'][$post]);
      saveEntry('tags', $tag, $tagEntry);
    }
    foreach ($addedTags as $tag) {
      $tagEntry = readEntry('tags', $tag);
      $tagEntry['posts'][$post] = $post;
      saveEntry('tags', $tag, $tagEntry);
    }
    $postEntry['tags'] = $newTags;
    saveEntry('posts', $post, $postEntry);
    redirect('view.php/post/' . $post);
  } else {
    $tagOptions = array();
    foreach (listEntry('tags') as $tag) {
      $tagEntry = readEntry('tags', $tag);
      $tagOptions[$tag] = $tagEntry['name'];
    }
    $out['title'] = $lang['editPost'] . ': ' . $postEntry['title'];
    $out['content'] .= '<form action="/edit.php/post/' . $post . '" method="post">
    <p>' . text('title', $postEntry['title']) . '</p>
    <p>' . textarea('content', clean($postEntry['content'])) . '</p>
    <p>' . select('published', array('yes' => $lang['yes'], 'no' => $lang['no']), $postEntry['published'] ? 'yes' : 'no') . '</p>
    <p>' . select('locked', array('yes' => $lang['yes'], 'no' => $lang['no']), $postEntry['locked'] ? 'yes' : 'no') . '</p>
    <p>' . multiselect('tags', $tagOptions, $postEntry['tags']) . '</p>
    <p>' . submit() . '</p>
    </form>';
    $out['content'] .= isPOST('content') ? box($_POST['content']) : '';
  }
} else if (isGET('comment') && (isAdmin() || isAuthor($_GET['comment'])) && isValidEntry('comments', $_GET['comment'])) {
  $comment = $_GET['comment'];
  $commentEntry = readEntry('comments', $comment);
  if (checkBot() && check('content', $config['maxCommentLength'])) {
    $commentEntry['content'] = clean($_POST['content']);
    saveEntry('comments', $comment, $commentEntry);
    $postEntry = readEntry('posts', $commentEntry['post']);
    redirect('view.php/post/' . $commentEntry['post'] . '/pages/' . pageOf($comment, $postEntry['comment']) . '#' . $comment);
  } else {
    $out['title'] = $lang['editComment'];
    $out['content'] .= '<form action="/edit.php/comment/' . $comment. '" method="post">
    <p>' . textarea('content', $commentEntry['content']) . '</p>
    <p>' . submit() . '</p>
    </form>';
    $out['content'] .= isPOST('content') ? box($_POST['content']) : '';
  }
} else if (isGET('link') && isAdmin() && isValidEntry('links', $_GET['link'])) {
  $link = $_GET['link'];
  $linkEntry = readEntry('links', $link);
  if (checkBot() && check('name') && check('url')) {
    $linkEntry['name'] = clean($_POST['name']);
    $linkEntry['url'] = clean($_POST['url']);
    saveEntry('links', $link, $linkEntry);
    home();
  } else {
    $out['title'] = $lang['editLink'] . ': ' . $linkEntry['name'];
    $out['content'] .= '<form action="/edit.php/link/' . $link . '" method="post">
    <p>' . text('name', $linkEntry['name']) . '</p>
    <p>' . text('url', $linkEntry['url']) . '</p>
    <p>' . submit() . '</p>
    </form>';
  }
} else if (isGET('tag') && isAdmin() && isValidEntry('tags', $_GET['tag'])) {
  $tagEntry = readEntry('tags', $_GET['tag']);
  if (checkBot() && check('name')) {
    $tagEntry['name'] = clean($_POST['name']);
    saveEntry('tags', $_GET['tag'], $tagEntry);
    home();
  } else {
    $out['title'] = $lang['editTag'] . ': ' .$tagEntry['name'];
    $out['content'] .= '<form action="/edit.php/tag/' . $_GET['tag'] . '" method="post">
    <p>' . text('name', $tagEntry['name']) . '</p>
    <p>' . submit() . '</p>
    </form>';
  }
} else {
  home();
}

require 'templates/page.php';
?>
