<?

header("Content-type: image/png");

$size = $_GET['size'] > 0 ? $_GET['size'] : 100;

$src = @imagecreatefrompng("http://minecraft.net/skin/{$_GET['name']}.png");

if (!$src) {
  $src = @imagecreatefrompng("http://www.minecraft.net/img/char.png");
}

$dest   = imagecreatetruecolor(8, 8);
imagecopy($dest, $src, 0, 0, 8, 8, 8, 8);
imagecopy($dest, $src, 0, -1, 40, 7, 8, 4);

$final = imagecreatetruecolor($size, $size);
imagecopyresized($final, $dest, 0, 0, 0, 0, $size, $size, 8, 8);

imagepng($final);
imagedestroy($im);
imagedestroy($dest);
imagedestroy($final);