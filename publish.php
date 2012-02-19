<?php
$out = array();
require 'header.php';

if (isGET('draft') && isAdmin() && isValidEntry('drafts', $_GET['draft'])) {
  $draft = $_GET['draft'];
  if (check('title') && check('content') && check('id')) {
    $post = newEntry(cleanMagic($_POST['id']));
    $postEntry['title'] = clean(cleanMagic($_POST['title']));
    $postEntry['content'] = cleanMagic($_POST['content']);
    $postEntry['locked'] = $_POST['locked'] === 'yes';
    $addedTags = $_POST['tags'] ? $_POST['tags'] : array();
    $postEntry['tags'] = $addedTags;
    saveEntry('posts', $post, $postEntry);
    foreach ($addedTags as $tag) {
      $tagEntry = readEntry('tags', $tag);
      $tagEntry['posts'][$post] = $post;
      saveEntry('tags', $tag, $tagEntry);
    }
    deleteEntry('drafts', $draft);
    redirect('view.php/post/' . $post);
  } else {
    $draftEntry = readEntry('drafts', $draft);
    $tagOptions = array();
    foreach (listEntry('tags') as $tag) {
      $tagEntry = readEntry('tags', $tag);
      $tagOptions[$tag] = $tagEntry['name'];
    }
    $out['title'] = $lang['publishPost'] . ': ' . $draftEntry['title'];
    $out['content'] .= '<form action="/publish.php/draft/' . $draft . '" method="post">
    <p>' . text('title', $draftEntry['title']) . '</p>
    <p>' . text('id', substr($draft, 20)) . '</p>
    <p>' . textarea('content', clean($draftEntry['content'])) . '</p>
    <p>' . select('locked', array('yes' => $lang['yes'], 'no' => $lang['no']), $postEntry['locked'] ? 'yes' : 'no') . '</p>
    <p>' . multiselect('tags', $tagOptions, $postEntry['tags']) . '</p>
    <p>' . submitAdmin($lang['confirm']) . '</p>
    </form>';
    $out['content'] .= isPOST('content') ? box(cleanMagic($_POST['content'])) : '';
  }
} else {
  home();
}

require 'templates/page.php';
?>
