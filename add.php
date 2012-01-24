<?php
$out['self'] = 'add';
require 'header.php';

if (isGET('post') && isAdmin()) {
  if (checkBot() && check('title') && check('content')) {
    $postEntry['title'] = clean($_POST['title']);
    $postEntry['content'] = $_POST['content'];
    $postEntry['comments'] = array();
    $postEntry['tags'] = array();
    $postEntry['locked'] = false;
    $postEntry['published'] = false;
    $post = newEntry();
    saveEntry('posts', $post, $postEntry);
    redirect('view.php/post/' . $post);
  } else {
    $out['title'] = $lang['newPost'];
    $out['content'] .= '<form action="/add.php/post" method="post" class="form">
    <p>' . text('title') . '</p>
    <p>' . textarea('content') . '</p>
    <p>' . submit() . '</p>
    </form>';
    $out['content'] .= isPOST('content') ? box($_POST['content']) : '';
  }
} else if (isGET('comment') && isValidEntry('posts', $_GET['comment'])) {
  $postEntry = readEntry('posts', $_GET['comment']);
  if ($postEntry['locked']) {
    home();
  } else if (checkBot() && check('name', $config['maxNameLength']) && check('content', $config['maxCommentLength'])) {
    $commentEntry['content'] = clean($_POST['content']);
    $commentEntry['post'] = $_GET['comment'];
    $comment = newEntry();
    $commentEntry['commenter'] = $_POST['name'] === '' ? substr($comment, -5) : commenter(clean($_POST['name']));
    saveEntry('comments', $comment, $commentEntry);
    $postEntry['comments'][$comment] = $comment;
    saveEntry('posts', $_GET['comment'], $postEntry);
    $_SESSION[$comment] = $comment;
    redirect('view.php/post/' . $_GET['comment'] . '/pages/' . pageOf($comment, $postEntry['comment']) . '#' . $comment);
  } else {
    $out['title'] = $lang['addComment'] . ': ' . $postEntry['title'];
    $out['content'] .= '<form action="/add.php/comment/' . $_GET['comment'] . '" method="post" class="form">
    <p>' . text('name') . '</p>
    <p>' . textarea('content') . '</p>
    <p>' . submit() . '</p>
    </form>';
    $out['content'] .= isPOST('content') ? box($_POST['content']) : '';
  }
} else if (isGET('link') && isAdmin()) {
  if (checkBot() && check('name') && check('url')) {
    $linkEntry['name'] = clean($_POST['name']);
    $linkEntry['url'] = clean($_POST['url']);
    saveEntry('links', newEntry(), $linkEntry);
    home();
  } else {
    $out['title'] = $lang['addLink'];
    $out['content'] .= '<form action="/add.php/link" method="post" class="form">
    <p>' . text('name') . '</p>
    <p>' . text('url') . '</p>
    <p>' . submit() . '</p>
    </form>';
  }
} else if (isGET('tag') && isAdmin()) {
  if (checkBot() && check('name')) {
    $tagEntry['name'] = clean($_POST['name']);
    $tagEntry['posts'] = array();
    saveEntry('tags', newEntry(), $tagEntry);
    home();
  } else {
    $out['title'] = $lang['addTag'];
    $out['content'] .= '<form action="/add.php/tag" method="post" class="form">
    <p>' . text('name') . '</p>
    <p>' . submit() . '</p>
    </form>';
  }
} else {
  home();
}

require 'templates/page.php';
?>
