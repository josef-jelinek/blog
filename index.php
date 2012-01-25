<?php
require 'header.php';

if (isGET('posts')) {
  $out['title'] = $lang['posts'];
  $out['titleHtml'] = '';
  $out['content'] .= '';
  $posts = listEntry('posts');
  sort($posts);
  $pages = pages($posts);
  $page = page($pages);
  if ($posts) {
    $first = true;
    foreach (pageItems($posts, $page) as $post) {
      $postEntry = readEntry('posts', $post);
      $out['content'] .= $first ? '' : '<div class="div">&middot; &middot; &middot; &middot; &middot;</div>';
      $first = false;
      $out['content'] .= '<div class="post">
      <h2><a href="/view.php/post/' . $post . '">' . $postEntry['title'] . managePost($post) . '</a></h2>
      <div class="date">' . toDate($post) . '</div>';
      $out['content'] .= '<div class="info">';
      foreach ($postEntry['tags'] as $tag) {
        $tagEntry = readEntry('tags', $tag);
        $tagName = $tagEntry['name'];
        $out['content'] .= '<a href="/view.php/tag/' . $tag . '">' . $tagName . '</a>';
      }
      $out['content'] .= '</div>
      <div class="content">' . unslash($postEntry['content']) . '</div>';
      $commentCount = $postEntry['comments'] ? count($postEntry['comments']) : 0;
      $out['content'] .= $commentCount > 0 ? '<div class="ccount"><a href="/view.php/post/' . $post . '#comments">' . $commentCount . ($commentCount > 1 ? $lang['ncomments'] : $lang['ncomment']) . '</a></div>' : '';
      $out['content'] .= '</div>';
    }
  }
  $out['content'] .= paging($page, $pages, '/index.php/posts/all');
} else if (isGET('drafts') && isAdmin()) {
  $out['title'] = $lang['drafts'];
  $out['titleHtml'] = '';
  $out['content'] .= '';
  $drafts = listEntry('drafts');
  sort($drafts);
  $pages = pages($drafts);
  $page = page($pages);
  if ($drafts) {
    $first = true;
    foreach (pageItems($drafts, $page) as $draft) {
      $draftEntry = readEntry('drafts', $draft);
      $out['content'] .= $first ? '' : '<div class="div">&middot; &middot; &middot; &middot; &middot;</div>';
      $first = false;
      $out['content'] .= '<div class="post">
      <h2><a href="/view.php/draft/' . $draft . '">' . $draftEntry['title'] . manageDraft($draft) . '</a></h2>
      <div class="date">' . toDate($draft) . '</div>';
      $out['content'] .= '<div class="content">' . unslash($draftEntry['content']) . '</div>
      </div>';
    }
  }
  $out['content'] .= paging($page, $pages, '/index.php/drafts/all');
} else if (isGET('comments')) {
  $out['title'] = $lang['comments'];
  $out['content'] .= '';
  $comments = listEntry('comments');
  rsort($comments);
  $pages = pages($comments);
  $page = page($pages);
  if ($comments) {
    $out['content'] .= '<div id="comments">';
    $first = true;
    foreach (pageItems($comments, $page) as $comment) {
      $out['content'] .= $first ? '' : '<div class="div">&middot; &middot; &middot; &middot; &middot;</div>';
      $first = false;
      $commentEntry = readEntry('comments', $comment);
      $postEntry = readEntry('posts', $commentEntry['post']);
      $title = $commentEntry['commenter'] . $lang['commented'] . $postEntry['title'];
      $pageOf = pageOf($comment, $postEntry['comments']);
      $link = '/view.php/post/' . $commentEntry['post'] . '/pages/' . $pageOf . '#' . $comment;
      $out['content'] .= '<div class="comment">
      <div class="title"><a href="' . $link . '">' . $title . manageComment($comment) . '</a></div>
      <div class="date">' . toDate($comment) . '</div>
      <div class="content">' . content($commentEntry['content']) . '</div>
      </div>';
    }
    $out['content'] .= '</div>';
  }
  $out['content'] .= paging($page, $pages, '/index.php/comments/all');
} else if (isGET('404')) {
  $out['title'] = 'HTTP 404';
  $out['content'] .= '<p>' . $lang['notFound'] . '</p>';
} else {
  $out['title'] = $lang['posts'];
  $out['titleHtml'] = '';
  $out['content'] .= '';
  $posts = listEntry('posts');
  rsort($posts);
  $pages = pages($posts);
  $page = page($pages);
  if ($posts) {
    $first = true;
    foreach (pageItems($posts, $page) as $post) {
      $postEntry = readEntry('posts', $post);
      $out['content'] .= $first ? '' : '<div class="div">&middot; &middot; &middot; &middot; &middot;</div>';
      $first = false;
      $out['content'] .= '<div class="post">
      <h2><a href="/view.php/post/' . $post . '">' . $postEntry['title'] . managePost($post) . '</a></h2>
      <div class="date">' . toDate($post) . '</div>';
      $out['content'] .= '<div class="info">';
      foreach ($postEntry['tags'] as $tag) {
        $tagEntry = readEntry('tags', $tag);
        $tagName = $tagEntry['name'];
        $out['content'] .= '<a href="/view.php/tag/' . $tag . '">' . $tagName . '</a>';
      }
      $out['content'] .= '</div>
      <div class="content">' . unslash($postEntry['content']) . '</div>';
      $commentCount = $postEntry['comments'] ? count($postEntry['comments']) : 0;
      $out['content'] .= $commentCount > 0 ? '<div class="ccount"><a href="/view.php/post/' . $post . '#comments">' . $commentCount . ($commentCount > 1 ? $lang['ncomments'] : $lang['ncomment']) . '</a></div>' : '';
      $out['content'] .= '</div>';
    }
  }
  $out['content'] .= paging($page, $pages, '/index.php');
}

require 'templates/page.php';
?>
