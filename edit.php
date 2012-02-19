<?php
$out = array();
require 'header.php';

if (isGET('post') && isAdmin() && isValidEntry('posts', $_GET['post'])) {
  $post = $_GET['post'];
  $postEntry = readEntry('posts', $post);
  if (check('title') && check('content')) {
    $postEntry['title'] = clean(cleanMagic($_POST['title']));
    $postEntry['content'] = cleanMagic($_POST['content']);
    $postEntry['locked'] = $_POST['locked'] === 'yes';
    $newTags = $_POST['tags'] ? $_POST['tags'] : array();
    $addedTags = array_diff($newTags, $postEntry['tags']);
    $removedTags = array_diff($postEntry['tags'], $newTags);
    $postEntry['tags'] = $newTags;
    saveEntry('posts', $post, $postEntry);
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
    <p>' . select('locked', array('yes' => $lang['yes'], 'no' => $lang['no']), $postEntry['locked'] ? 'yes' : 'no') . '</p>
    <p>' . multiselect('tags', $tagOptions, $postEntry['tags']) . '</p>
    <p>' . submitAdmin($lang['confirm']) . '</p>
    </form>';
    $out['content'] .= isPOST('content') ? box(cleanMagic($_POST['content'])) : '';
  }
} else if (isGET('draft') && isAdmin() && isValidEntry('drafts', $_GET['draft'])) {
  $draft = $_GET['draft'];
  $draftEntry = readEntry('drafts', $draft);
  if (check('title') && check('content')) {
    $draftEntry['title'] = clean(cleanMagic($_POST['title']));
    $draftEntry['content'] = cleanMagic($_POST['content']);
    saveEntry('drafts', $draft, $draftEntry);
    redirect('view.php/draft/' . $draft);
  } else {
    $out['title'] = $lang['editDraft'] . ': ' . $draftEntry['title'];
    $out['content'] .= '<form action="/edit.php/draft/' . $draft . '" method="post">
    <p>' . text('title', $draftEntry['title']) . '</p>
    <p>' . textarea('content', clean($draftEntry['content'])) . '</p>
    <p>' . submitAdmin($lang['confirm']) . '</p>
    </form>';
    $out['content'] .= isPOST('content') ? box(cleanMagic($_POST['content'])) : '';
  }
} else if (isGET('comment') && (isAdmin() || isAuthor($_GET['comment'])) && isValidEntry('comments', $_GET['comment'])) {
  $comment = $_GET['comment'];
  $commentEntry = readEntry('comments', $comment);
  if (checkBot() && check('content', $config['maxCommentLength'])) {
    $commentEntry['content'] = clean(cleanMagic($_POST['content']));
    saveEntry('comments', $comment, $commentEntry);
    $postEntry = readEntry('posts', $commentEntry['post']);
    redirect('view.php/post/' . $commentEntry['post'] . '/pages/' . pageOf($comment, $postEntry['comment']) . '#' . $comment);
  } else {
    $out['title'] = $lang['editComment'];
    $out['content'] .= '<form action="/edit.php/comment/' . $comment. '" method="post">
    <p>' . textarea('content', $commentEntry['content']) . '</p>
    <p>' . submitSafe($lang['confirm']) . '</p>
    </form>';
    $out['content'] .= isPOST('content') ? box(cleanMagic($_POST['content'])) : '';
  }
} else if (isGET('link') && isAdmin() && isValidEntry('links', $_GET['link'])) {
  $link = $_GET['link'];
  $linkEntry = readEntry('links', $link);
  if (check('name') && check('url')) {
    $linkEntry['name'] = clean(cleanMagic($_POST['name']));
    $linkEntry['url'] = clean(cleanMagic($_POST['url']));
    saveEntry('links', $link, $linkEntry);
    home();
  } else {
    $out['title'] = $lang['editLink'] . ': ' . $linkEntry['name'];
    $out['content'] .= '<form action="/edit.php/link/' . $link . '" method="post">
    <p>' . text('name', $linkEntry['name']) . '</p>
    <p>' . text('url', $linkEntry['url']) . '</p>
    <p>' . submitAdmin($lang['confirm']) . '</p>
    </form>';
  }
} else if (isGET('tag') && isAdmin() && isValidEntry('tags', $_GET['tag'])) {
  $tagEntry = readEntry('tags', $_GET['tag']);
  if (check('name')) {
    $tagEntry['name'] = clean(cleanMagic($_POST['name']));
    saveEntry('tags', $_GET['tag'], $tagEntry);
    home();
  } else {
    $out['title'] = $lang['editTag'] . ': ' .$tagEntry['name'];
    $out['content'] .= '<form action="/edit.php/tag/' . $_GET['tag'] . '" method="post">
    <p>' . text('name', $tagEntry['name']) . '</p>
    <p>' . submitAdmin($lang['confirm']) . '</p>
    </form>';
  }
} else {
  home();
}

require 'templates/page.php';
?>
