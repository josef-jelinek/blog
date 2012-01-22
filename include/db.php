<?php
function nameEntry($type, $file) {
  return 'data/' . $type . '/' . $file . '.php';
}

function readEntry($type, $file) {
  $data = substr(file_get_contents(nameEntry($type, $file)), 14);
  return eval("return $data;");
}

function saveEntry($type, $file, $data) {
  file_put_contents(nameEntry($type, $file), "<?php exit;?>\n" . var_export($data, true), LOCK_EX);
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

function newEntry() {
  return date('Y-m-dHis').substr(uniqid(), -5);
}
?>
