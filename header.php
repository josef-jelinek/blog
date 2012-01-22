<?php
if (!isset($out)) exit;

require 'config/config.php';
require 'config/language.php';

require 'include/db.php';
require 'include/util.php';
require 'include/ui.php';

session_start();
if (!isset($_SESSION['role']))
  $_SESSION['role'] = '';

function home() {
  redirect('index.php/posts');
}

$_GET = urlPath();

$out['baseURL'] = baseURL();
$out['content'] = '';
$out['error'] = '';

// Links
$out['addLink'] .= isAdmin() ? '<a href="/add.php/link" class="add"></a>' : '';
$out['linkListItems'] .= '';
$links = listEntry('links');
if ($links) {
  foreach ($links as $link) {
    $linkEntry = readEntry('links', $link);
    $out['linkListItems'] .= '<li><a href="' . $linkEntry['url'] . '">' . $linkEntry['name'] . '</a>' . manageLink($link) . '</li>';
  }
}

// Tags
$out['addTag'] .= isAdmin() ? '<a href="/add.php/tag" class="add"></a>' : '';
$out['tagLinks'] .= '';
$tags = listEntry('tags');
foreach ($tags as $tag) {
  $tagEntry = readEntry('tags', $tag);
  $out['tagLinks'] .= ' <a href="/view.php/tag/' . $tag . '">' . $tagEntry['name'] . ' (' . count($tagEntry['posts']) . ')</a>';
}

// Archive
$out['archiveListItems'] .= '';
$archives = array();
foreach (listEntry('posts') as $post) {
  $year = substr($post, 0, 4);
  $month = substr($post, 5, 2);
  if (isset($archives[$year][$month])) {
    $archives[$year][$month]++;
  } else {
    $archives[$year][$month] = 1;
  }
}
if ($archives) {
  foreach ($archives as $year => $months) {
    $out['archiveListItems'] .= '<li>' . $year . '&nbsp;';
    foreach ($months as $month => $count) {
      $yearMonth = $year . '-' . $month;
      $out['archiveListItems'] .= ' <a href="/view.php/archive/' . $yearMonth . '">' . date('M', strtotime($yearMonth)) . ' (' . $count . ')</a>';
    }
    $out['archiveListItems'] .= '</li>';
  }
}
?>
