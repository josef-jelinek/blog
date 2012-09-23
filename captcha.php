<?php
session_start();
$width = 160;
$height = 50;
$min_length = 4;
$max_length = 6;
$scale = 2;
$font = 'fonts/ConcertOne-Regular.ttf';
$chars = 'abcdefghijklmnopqrstuvwxyz234578';

$length = rand($min_length, $max_length);
$text = '';
for ($i = 0; $i < $length; $i++) {
  $text .= substr($chars, mt_rand(0, strlen($chars)), 1);
}

$_SESSION['captcha'] = $text;

$im = imagecreatetruecolor($width * $scale, $height * $scale);
$bg = 0xFFFFFF;
$fg = 0x669933;
imagefilledrectangle($im, 0, 0, $width * $scale, $height * $scale, $bg);

$font_scale = 1 + 0.09 * ($max_length - $length);
$x = 20 * $scale;
$y = round($height * 27 / 40 * $scale);
for ($i = 0; $i < $length; $i++) {
    $angle = rand(-9, 9);
    $font_size = rand(22, 30) * $scale * $font_scale;
    $letter = substr($text, $i, 1);
    $coords = imagettftext($im, $font_size, $angle, $x, $y, $fg, $font, $letter);
    $x = $coords[2];
}
$xp = $scale * 11 * rand(1, 3);
$k = rand(0, 100);
for ($x = 0; $x < $width * $scale; $x++) {
  $y = sin($k + $x / $xp) * $scale * 5;
  imagecopy($im, $im, $x - 1, $y, $x, 0, 1, $height * $scale);
}
$k = rand(0, 100);
$yp = $scale * 6 * rand(2, 3);
for ($y = 0; $y < $height * $scale; $y++) {
  $x = sin($k + $y / $yp) * $scale * 8;
  imagecopy($im, $im, $x, $y - 1, 0, $y, $width * $scale, 1);
}

$im2 = imagecreatetruecolor($width, $height);
imagecopyresampled($im2, $im, 0, 0, 0, 0, $width, $height, $width * $scale, $height * $scale);
imagedestroy($im);
header("Content-type: image/png");
imagepng($im2);
imagedestroy($im2);
?>
