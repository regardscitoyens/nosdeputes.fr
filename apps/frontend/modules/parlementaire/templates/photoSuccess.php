<?php
$file = tempnam(sys_get_temp_dir(), 'Parl');
$photo = $parlementaire->photo;
if (!strlen($photo)) {
  copy($path_to_default_parl_image, $file);
} else {
  $fh = fopen($file, 'w');
  fwrite($fh ,$photo);
  fclose($fh);
}
list($width, $height, $image_type) = getimagesize($file);
if (!$width || !$height) {
  copy($path_to_default_parl_image, $file);
  list($width, $height, $image_type) = getimagesize($file);
}

if (!$request_height) {
  $request_height = $height;
}
$newheight = ceil($request_height/10)*10;
if ($newheight > 250)
  $newheight = 250;
$ratio = 125/160.;
$width2 = $width; $height2 = $height;
if ($ratio > $width/$height)
  $height2 = $width/$ratio;
else $width2 = $height*$ratio;
$iorig = imagecreatefromjpeg($file);
$ih = imagecreatetruecolor($work_height*$ratio, $work_height);
if (!$color && ((!$parlementaire->isEnMandat() && !myTools::isFinlegislature()) || preg_match('/décè/i', $parlementaire->getAnciensMandats())))
  self::imagetograyscale($iorig);
imagecopyresampled($ih, $iorig, 0, 0, max(0, ($width - $width2)/2), max(0, ($height - $height2)/2), $work_height*$ratio, $work_height, $width2, $height2);
$width = $work_height*$ratio;
$height = $work_height;
imagedestroy($iorig);
unlink($file);

if ((isset($parlementaire->autoflip) && $parlementaire->autoflip) XOR $request_flip) {
  //self::horizontalFlip($ih);
  $size_x = imagesx($ih);
  $size_y = imagesy($ih);
  $temp = imagecreatetruecolor($size_x, $size_y);
  $x = imagecopyresampled($temp, $ih, 0, 0, ($size_x-1), 0, $size_x, $size_y, 0-$size_x, $size_y);
  if ($x) {
    $ih = $temp;
  }
  else {
    die("Unable to flip image");
  }
}

$groupe = $parlementaire->groupe_acronyme;
if ($groupe) {
  imagefilledellipse($ih, $width-$rayon, $height-$rayon, $rayon+$bordure, $rayon+$bordure, imagecolorallocate($ih, 255, 255, 255));

  $colormap = myTools::getGroupesColorMap();
  if (isset($colormap[$groupe]) && preg_match('/^(\d+),(\d+),(\d+)$/', $colormap[$groupe], $match))
    imagefilledellipse($ih, $width-$rayon, $height-$rayon, $rayon, $rayon, imagecolorallocate($ih, $match[1], $match[2], $match[3]));

/*  Old code to handle groupes bicolore
imagefilledarc($ih, $width-$rayon, $height-$rayon, $rayon, $rayon, 45, 225, imagecolorallocate($ih, 0, 170, 0), IMG_ARC_EDGED);
imagefilledarc($ih, $width-$rayon, $height-$rayon, $rayon, $rayon, 225, 45, imagecolorallocate($ih, 240, 0, 0), IMG_ARC_EDGED);
*/
}

if ($newheight) {
  $newwidth = $newheight*$width/$height;
  $image = imagecreatetruecolor($newwidth, $newheight);
  imagecopyresampled($image, $ih, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
  imagedestroy($ih);
  $ih = $image;
}
imagepng($ih);
imagedestroy($ih);
