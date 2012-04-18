<?PHP

/**
* Minecraft Index File
*
*This file can be used to show online users, how long they've been online for,
*the last time they where online, amount of times logged in, and recetn chat
*items. It is to be used with the minecraft.php class and avatar.php script.
*
*Please feel free to change this file completely to fit your site.
*
* @category   Minecraft
* @package    Minecraft_Server_Script
* @subpackage Minecraft_Index
* @copyright  Copyright (c) Jaryth Frenette 2012, hfuller 2011, caseypugh, 2011
* @license    Open Source - Anyone can use, modify and redistribute as wanted
* @version    Release: 1.0
* @link       http://jaryth.net
*/

//Include the Class file
include("minecraft.php");
//Create a new object (You can create multiple objects using this class
//just make sure to name them each something different, and use a different log
$minecraft = new minecraft("E:\Program Files (x86)\Minecraft\server.log");

//if chat flag is set, show chat. This is optional
if(isset($_GET['chat'])){  
  //only show the newest 30 lines of the chat log
  $total = count($minecraft->chat);
  $start = count($minecraft->chat) - 30;  
  for ($i = $start; $i < $total; $i++) {
    echo $minecraft->chat[$i];   
  }
  exit();
}

//show HTML
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" /> 
<title>Jaryth's Server</title>

<style type="text/css">
html,body{margin:0;padding:0}
body{font: 76% arial,sans-serif;text-align:center; background: #aaaaaa;}
p{margin:0 10px 10px}
a{display:block;color: #981793;padding:10px}
div#header {height:166px;line-height:80px;margin:0;
  padding-left:10px;background-image: url('/images/header.jpg'); background-repeat:no-repeat; color: #79B30B}
div#container{text-align:left}
div#content p{line-height:1.4}
div#navigation{background: #bbb;}
div#extra{}
div#footer{}
div#footer p{margin:0;padding:5px 10px}

div#container{width:600px;margin:0 auto}
div#content{float:right;width:400px; }
div#navigation{float:left;width:200px}
div#extra{clear:both;width:100%}

.server { font: bold 24px Helvetica, Arial; padding: 10px 5px; color: #7ed471; background: #333; border-bottom: 1px solid #fff; }
.users .user { padding: 5px; clear: both; border-bottom: 1px solid #ddd; }
.users .user img { float: left; }
.users .user .info { margin-top: 1px; float: left; margin-left: 5px; }
.users .user .ss { float: right; width: 140px; height: 40px; overflow: hidden; }
.users .user .ss img { height: 60px; }
.users .user .info h1 { font: 20px Helvetica, Arial; color: #333; }
.users .user .info span { color: #eee; font: normal 15px Helvetica, Arial; }
.users .user .info span.on { color: #69FF22; font-weight:bold;}


.users .offline .info h1 { color: #666; }
.users .offline { background: #eee;  }
.clear { clear: both; }
</style>
</head>
<body>
<div id="container">
<div id="header">&nbsp;</div>
<div id="wrapper">
<div id="content">
<div class="users">
<b><a href="/map/">-Server Map</a></b>
<b><a href="/port/">-Character Management</a></b>
<h3>In Server</h3>
<?PHP foreach ($minecraft->users as $u): ?>
  <div class="user <?PHP $u['online'] ? 'online' : 'offline' ?>">
    <a href="<?PHP echo($u['avatar']); ?>&skip"><img src="<?PHP echo($u['avatar']); ?>" /></a>
    <div class="info">
      <h1><?PHP echo($u['name']) ?></h1>
      <?PHP if (!$u['online']): ?>
        <span>Offline. Last seen <?PHP echo($minecraft->getTimeAgo($u['time'])) ?> ago.</span>
      <?PHP else: ?>
        <span class="on">Online! Logged on for <?PHP echo($minecraft->getTimeAgo($u['time'])) ?>.</span>
      <?PHP endif ?>
      <br>
      Number of times logged on: <?PHP echo($u['logcount']) ?><br>
      Total time online: <?PHP echo($minecraft->Sec2Time($u['totaltime'])) ?> 
    </div>

    <div class="clear"></div>
  </div>
<?PHP endforeach;?>
</div>
 </div>
</div>
<div id="navigation">
<h3>In TeamSpeak:</h3>

<div id="ts3viewer_956271" style="width:; background-color:;"> </div>
<!--
You can put a Teamspeak viewer, or another monitoring viewer here!
-->
</div>
<div id="extra">
 </div>
<div id="footer"></div>
</div>
</body>
</html>