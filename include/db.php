<?php
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

function listEntry($type) {
  return fdir("data/$type");
}

function isValidEntry($type, $file) {
  return indir($file . '.php', "data/$type");
}

function newEntry($id = '') {
  return date('Y-m-d-H-i-s-') . ($id != '' ? $id : uniqid());
}
?>
