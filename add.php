<?php
$out = array();
require 'header.php';

if (isGET('draft') && isAdmin()) {
  if (check('title') && check('content')) {
    $postEntry['title'] = clean(cleanMagic($_POST['title']));
    $postEntry['content'] = cleanMagic($_POST['content']);
    $post = newEntry(cleanMagic($_POST['id']));
    saveEntry('drafts', $post, $postEntry);
    redirect('view.php?draft=' . $post);
  } else {
    $out['title'] = $lang['newPost'];
    $out['content'] .= '<form action="./add.php?draft" method="post">
    <p>' . text('title') . '</p>
    <p>' . text('id') . '</p>
    <p>' . textarea('content') . '</p>
    <p>' . submitAdmin($lang['confirm']) . '</p>
    </form>';
    $out['content'] .= isPOST('content') ? box(cleanMagic($_POST['content'])) : '';
  }
} else if (isGET('comment') && isValidEntry('posts', GET('comment'))) {
  $postEntry = readEntry('posts', GET('comment'));
  if ($postEntry['locked']) {
    home();
  } else if (checkBot() && check('name', $config['maxNameLength']) && check('content', $config['maxCommentLength'])) {
    $commentEntry['content'] = clean(cleanMagic($_POST['content']));
    $commentEntry['post'] = GET('comment');
    $comment = newEntry();
    $commentEntry['commenter'] = clean(cleanMagic($_POST['name']));
    saveEntry('comments', $comment, $commentEntry);
    $postEntry['comments'][$comment] = $comment;
    saveEntry('posts', GET('comment'), $postEntry);
    $_SESSION[$comment] = $comment;
    redirect('view.php?post=' . GET('comment') . '/pages/' . pageOf($comment, $postEntry['comment']) . '#' . $comment);
  } else {
    $out['title'] = $lang['addComment'] . ': ' . $postEntry['title'];
    $out['content'] .= '<form action="./add.php?comment=' . GET('comment') . '" method="post">
    <p>' . text('name') . '</p>
    <p>' . textarea('content') . '</p>
    <p>' . submitSafe($lang['confirm']) . '</p>
    </form>';
    $out['content'] .= isPOST('content') ? box(cleanMagic($_POST['content'])) : '';
  }
} else if (isGET('link') && isAdmin()) {
  if (check('name') && check('url')) {
    $linkEntry['name'] = clean(cleanMagic($_POST['name']));
    $linkEntry['url'] = clean(cleanMagic($_POST['url']));
    saveEntry('links', newEntry(), $linkEntry);
    home();
  } else {
    $out['title'] = $lang['addLink'];
    $out['content'] .= '<form action="./add.php?link" method="post">
    <p>' . text('name') . '</p>
    <p>' . text('url') . '</p>
    <p>' . submitAdmin($lang['confirm']) . '</p>
    </form>';
  }
} else if (isGET('tag') && isAdmin()) {
  if (check('name')) {
    $tagEntry['name'] = clean(cleanMagic($_POST['name']));
    $tagEntry['posts'] = array();
    saveEntry('tags', newEntry(), $tagEntry);
    home();
  } else {
    $out['title'] = $lang['addTag'];
    $out['content'] .= '<form action="./add.php?tag" method="post">
    <p>' . text('name') . '</p>
    <p>' . submitAdmin($lang['confirm']) . '</p>
    </form>';
  }
} else {
  home();
}

require 'templates/page.php';
?>
