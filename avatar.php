<?

header("Content-type: image/png");

$name = $_GET['name'] ? $_GET['name'] : 'notch';
$size = $_GET['size'] > 0 ? $_GET['size'] : 100;

$src = @imagecreatefrompng("http://minecraft.net/skin/{$name}.png");

if (!$src) {
  $src = @imagecreatefrompng("http://www.minecraft.net/images/char.png");
}

$dest   = imagecreatetruecolor(8, 8);
imagecopy($dest, $src, 0, 0, 8, 8, 8, 8);   // copy the face

// Check to see if the helm is not all same color
$bg_color = imagecolorat($src, 0, 0);

$no_helm = true;

// Check if there's any helm
for ($i = 1; $i <= 8; $i++) {
  for ($j = 1; $j <= 4; $j++) {
    // scanning helm area
    if (imagecolorat($src, 40 + $i, 7 + $j) != $bg_color) {
      $no_helm = false;
    }
  }

  if (!$no_helm)
    break;
}


if (!$no_helm) {
  // copy the helm
  imagecopy($dest, $src, 0, -1, 40, 7, 8, 4);
}

// now resize
$final = imagecreatetruecolor($size, $size);
imagecopyresized($final, $dest, 0, 0, 0, 0, $size, $size, 8, 8);

// cleanup time
imagepng($final);
imagedestroy($dest);
imagedestroy($final);