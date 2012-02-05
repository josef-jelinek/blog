<?php
function fdir($dir) {
  $files = array();
  $dh = opendir($dir);
  while (false !== ($file = readdir($dh))) {
    if (strpos($file, '.', 0) > 0) {
      $file = explode('.', $file, 2);
      $files[] = $file[0];
    }
  }
  closedir($dh);
  return $files;
}

function listEntry($type) {
  return fdir("data/$type");
}

function nameEntry($type, $file) {
  return "data/$type/$file.php";
}

function readEntry($type, $file) {
  $data = substr(file_get_contents(nameEntry($type, $file)), 13);
  return eval("return $data;");
}

function saveEntry($type, $file, $data) {
  file_put_contents(nameEntry($type, $file), "<?php exit;?>" . var_export($data, true), LOCK_EX);
}

function deleteEntry($type, $file) {
  unlink(nameEntry($type, $file));
}

function isValidEntry($type, $file) {
  return file_exists(nameEntry($type, $file));
}

function newEntry($id = '') {
  return date('Y-m-d-H-i-s-') . ($id != '' ? $id : uniqid());
}
?>
