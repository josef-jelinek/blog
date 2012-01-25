<?php
require 'header.php';

if (isGET('post') && isValidEntry('posts', $_GET['post'])) {
  $postEntry = readEntry('posts', $_GET['post']);
  $out['title'] = $postEntry['title'];
  $out['titleHtml'] = '';
  $out['content'] .= '<div class="post">
  <h1 class="title">' . $out['title'] . managePost($_GET['post']) . '</h1>
  <div class="date">' . toDate($_GET['post']) . '</div>';
  $out['content'] .= '<div class="content">' . unslash($postEntry['content']) . '</div>';
  $out['content'] .= '<div class="info">';
  foreach ($postEntry['tags'] as $tag) {
    $tagEntry = readEntry('tags', $tag);
    $tagName = $tagEntry['name'];
    $out['content'] .= '<a href="/view.php/tag/' . $tag . '">' . $tagName . '</a>';
  }
  $out['content'] .= '</div>
  </div>';
  $pages = pages($postEntry['comments']);
  $page = page($pages);
  if ($postEntry['comments']) {
    $commentCount = count($postEntry['comments']);
    $out['content'] .= '<div class="ccount">' . $commentCount . ($commentCount != 1 ? $lang['ncomments'] : $lang['ncomment']) . '</div>';
    $out['content'] .= '<div id="comments">';
    $first = true;
    foreach (pageItems($postEntry['comments'], $page) as $comment) {
      $out['content'] .= $first ? '' : '<div class="div">&middot; &middot; &middot; &middot; &middot;</div>';
      $first = false;
      $commentEntry = readEntry('comments', $comment);
      $out['content'] .= '<div id="' . $comment . '" class="comment">
      <div class="title">' . $commentEntry['commenter'] . manageComment($comment) . '</div>
      <div class="date">' . toDate($comment) . '</div>
      <div class="content">' . content($commentEntry['content']) . '</div>
      </div>';
    }
    $out['content'] .= '</div>';
  }
  $out['content'] .= paging($page, $pages, '/view.php/post/' . $_GET['post'] . '#comments');
  if (!$postEntry['locked']) {
    $out['content'] .= '<form action="/add.php/comment/' . $_GET['post'] . '" method="post">
    <p>' . text('name') . '</p>
    <p>' . textarea('content') . '</p>
    <p>' . submitSafe('send') . '</p>
    </form>';
  }
} else if (isGET('draft') && isValidEntry('drafts', $_GET['draft'])) {
  $draftEntry = readEntry('drafts', $_GET['draft']);
  $out['title'] = $draftEntry['title'];
  $out['titleHtml'] = '';
  $out['content'] .= '<div class="post">
  <h1 class="title">' . $out['title'] . manageDraft($_GET['draft']) . '</h1>
  <div class="date">' . toDate($_GET['draft']) . '</div>';
  $out['content'] .= '<div class="content">' . unslash($draftEntry['content']) . '</div>
  </div>';
} else if (isGET('tag') && isValidEntry('tags', $_GET['tag'])) {
  $tagEntry = readEntry('tags', $_GET['tag']);
  $out['title'] = $tagEntry['name'];
  $out['titleHtml'] .= '<h1>' . $out['title'] . manageTag($_GET['tag']) . '</h1>';
  $out['content'] .= '';
  if ($tagEntry['posts']) {
    foreach ($tagEntry['posts'] as $post) {
      $postEntry = readEntry('posts', $post);
      $title = $postEntry['title'];
      $out['content'] .= '<p><a href="/view.php/post/' . $post . '">' . $title . '</a>' . managePost($post) . ' &mdash; ' . toDate($post) . '</p>';
    }
  }
} else if (isGET('archive') && strlen($_GET['archive']) === 7) {
  $archivedPosts = array();
  foreach (listEntry('posts') as $post)
    if ($_GET['archive'] === substr($post, 0, 7))
      $archivedPosts[] = $post;
  if (!$archivedPosts) {
    redirect('index.php/404');
  } else {
    $out['title'] = date('M Y', strtotime($_GET['archive']));
    $out['content'] .= '';
    foreach ($archivedPosts as $post) {
      $postEntry = readEntry('posts', $post);
      $title = $postEntry['title'];
      $out['content'] .= '<p><a href="/view.php/post/' . $post . '">' . $title . '</a>' . managePost($post) . ' &mdash; ' . toDate($post) . '</p>';
    }
  }
} else {
  redirect('index.php/404');
}

require 'templates/page.php';
?>
