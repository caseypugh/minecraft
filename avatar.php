<?php

/**
* Minecraft Avatar script
*
*This script can create and cache Minecraft server Avatars from the Minecraft
*website.
*
* @category   Minecraft
* @package    Minecraft_Server_Script
* @subpackage Avatar_Script
* @copyright  Copyright (c) Jaryth Frenette 2012, hfuller 2011, caseypugh, 2011
* @license    Open Source - Anyone can use, modify and redistribute as wanted
* @version    Release: 1.0
* @link       http://jaryth.net
*/

//User Settings:
//Set the name of the cache folder. Ignored if caching is not set in URL.
//Note: Changing the cache folder does not delete the old one if it exists
//Note: You will need to also change this setting in the minecraft.php file
//Default 'cache'
$cacheFolder = 'cache';

//Set up initial variables
ini_set("display_errors",FALSE);
header("Content-type: image/png");

//Get data from URL
$name = $_GET['name'];
if(strpos($name, '..') !== false){
  exit(); // name in path with '..' in it would allow for directory traversal.
}
$size = $_GET['size'] > 0 ? $_GET['size'] : 100;

//Check if Caching is enabled via the URL(&cache=1)(Although the =1 is not need)
//If (&skip) is enabled, loading the cache will be skipped
//This can be used to rebuild a characters skin
if(isset($_GET['cache'])){
  //Create the cache folder if need
  if(!is_dir($cacheFolder)){
    mkdir($cacheFolder);
  }

  //Set path for file
  $cachePath = $cacheFolder . DIRECTORY_SEPARATOR . $name . '.png';

  //If a cache exists, use it if not generate a new one
  if(is_file($cachePath) && !isset($_GET['skip'])){
    include($cachePath);
    exit();
  }
}

//Grab the skin off of the Minecraft site
$src = imagecreatefrompng("http://skins.minecraft.net/MinecraftSkins/".$name.".png");

//If no path was given or no image can be found, then create from default
if(!$src){
  $src = imagecreatefrompng("http://www.minecraft.net/skin/char.png");
}

//Start creating the image
$dest   = imagecreatetruecolor(8, 8);
imagecopy($dest, $src, 0, 0, 8, 8, 8, 8);   // copy the face

// Check to see if the helm is not all same color
$bg_color = imagecolorat($src, 0, 0);
$no_helm  = true;

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

// copy the helm
if (!$no_helm) {
  imagecopy($dest, $src, 0, -1, 40, 7, 8, 4);
}

//prepare to finish the image
$final = imagecreatetruecolor($size, $size);
imagecopyresized($final, $dest, 0, 0, 0, 0, $size, $size, 8, 8);

//Check if Caching is enabled via URL
if(isset($_GET['cache'])){
  //If it is, save image to disk
  imagepng($final, $cachePath);
  include($cachePath);
}
else {
  //if its not, just show image on screen
  imagepng($final);
}

//Finally some cleanup
imagedestroy($im);
imagedestroy($dest);
imagedestroy($final);

?>
