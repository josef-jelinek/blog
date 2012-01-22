<?php
$out['self'] = 'edit';
require 'header.php';

if (isGET('post') && isAdmin() && isValidEntry('posts', $_GET['post'])) {
  $postEntry = readEntry('posts', $_GET['post']);
  if (checkBot() && check('title') && check('content') &&
    isPOST('locked') && ($_POST['locked'] === 'yes' || $_POST['locked'] === 'no')) {
    $postEntry['title'] = clean($_POST['title']);
    $postEntry['content'] = $_POST['content'];
    $postEntry['locked'] = $_POST['locked'] === 'yes';
    $newTags = $_POST['tags'] ? $_POST['tags'] : array();
    $addedTags = array_diff($newTags, $postEntry['tags']);
    $removedTags = array_diff($postEntry['tags'], $newTags);
    foreach ($removedTags as $tag) {
      $tagEntry = readEntry('tags', $tag);
      unset($tagEntry['posts'][$_GET['post']]);
      saveEntry('tags', $tag, $tagEntry);
    }
    foreach ($addedTags as $tag) {
      $tagEntry = readEntry('tags', $tag);
      $tagEntry['posts'][$_GET['post']] = $_GET['post'];
      saveEntry('tags', $tag, $tagEntry);
    }
    $postEntry['tags'] = $newTags;
    saveEntry('posts', $_GET['post'], $postEntry);
    redirect('view.php/post/' . $_GET['post']);
  } else {
    $tagOptions = array();
    foreach (listEntry('tags') as $tag) {
      $tagEntry = readEntry('tags', $tag);
      $tagOptions[$tag] = $tagEntry['name'];
    }
    $out['title'] = $lang['editPost'] . ': ' . $postEntry['title'];
    $out['content'] .= '<form action="/edit.php/post/' . $_GET['post'] . '" method="post">
    <p>' . text('title', $postEntry['title']) . '</p>
    <p>' . textarea('content', clean($postEntry['content'])) . '</p>
    <p>' . select('locked', array('yes' => $lang['yes'], 'no' => $lang['no']), $postEntry['locked'] ? 'yes' : 'no') . '</p>
    <p>' . multiselect('tags', $tagOptions, $postEntry['tags']) . '</p>
    <p>' . submit() . '</p>
    </form>';
    $out['content'] .= isPOST('content') ? box($_POST['content']) : '';
  }
} else if (isGET('comment') && (isAdmin() || isAuthor($_GET['comment'])) && isValidEntry('comments', $_GET['comment'])) {
  $commentEntry = readEntry('comments', $_GET['comment']);
  if (checkBot() && check('content', $config['maxCommentLength'])) {
    $commentEntry['content'] = clean($_POST['content']);
    saveEntry('comments', $_GET['comment'], $commentEntry);
    $postEntry = readEntry('posts', $commentEntry['post']);
    redirect('view.php/post/' . $commentEntry['post'] . '/pages/' . pageOf($_GET['comment'], $postEntry['comment']) . '#' . $_GET['comment']);
  } else {
    $out['title'] = $lang['editComment'];
    $out['content'] .= '<form action="/edit.php/comment/' . $_GET['comment'] . '" method="post">
    <p>' . textarea('content', $commentEntry['content']) . '</p>
    <p>' . submit() . '</p>
    </form>';
    $out['content'] .= isPOST('content') ? box($_POST['content']) : '';
  }
} else if (isGET('link') && isAdmin() && isValidEntry('links', $_GET['link'])) {
  $linkEntry = readEntry('links', $_GET['link']);
  if (checkBot() && check('name') && check('url')) {
    $linkEntry['name'] = clean($_POST['name']);
    $linkEntry['url'] = clean($_POST['url']);
    saveEntry('links', $_GET['link'], $linkEntry);
    home();
  } else {
    $out['title'] = $lang['editLink'] . ': ' . $linkEntry['name'];
    $out['content'] .= '<form action="/edit.php/link/' . $_GET['link'] . '" method="post">
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
