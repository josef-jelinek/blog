<?php
require 'header.php';

if (isGET('draft') && isAdmin() && isValidEntry('drafts', $_GET['draft'])) {
  $draft = $_GET['draft'];
  if (check('title') && check('content') && check('id')) {
    $postEntry['title'] = clean($_POST['title']);
    $postEntry['content'] = $_POST['content'];
    $postEntry['locked'] = $_POST['locked'] === 'yes';
    $postEntry['tags'] = $_POST['tags'] ? $_POST['tags'] : array();
    $post = newEntry($_POST['id']);
    saveEntry('posts', $post, $postEntry);
    deleteEntry('drafts', $draft);
    redirect('view.php/post/' . $post);
  } else {
    $draftEntry = readEntry('drafts', $draft);
    $out['title'] = $lang['publishPost'] . ': ' . $draftEntry['title'];
    $out['content'] .= '<form action="/publish.php/draft/' . $draft . '" method="post">
    <p>' . text('title', $draftEntry['title']) . '</p>
    <p>' . text('id', substr($draft, 20)) . '</p>
    <p>' . textarea('content', clean($draftEntry['content'])) . '</p>
    <p>' . select('locked', array('yes' => $lang['yes'], 'no' => $lang['no']), $postEntry['locked'] ? 'yes' : 'no') . '</p>
    <p>' . multiselect('tags', $tagOptions, $postEntry['tags']) . '</p>
    <p>' . submitAdmin($lang['confirm']) . '</p>
    </form>';
    $out['content'] .= isPOST('content') ? box($_POST['content']) : '';
  }
} else {
  home();
}

require 'templates/page.php';
?>
