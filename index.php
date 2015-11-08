<?PHP

/**
* Minecraft Index File
*
*This file can be used to show online users, how long they've been online for,
*the last time they were online, amount of times logged in, and recent chat
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
<!doctype html>
<html>
  <head>
    <title>Minecraft Stats</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
    <link rel="stylesheet" href="https://app.divshot.com/css/divshot-util.css">
    <link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.2.2/css/bootstrap-combined.min.css" rel="stylesheet">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <style type="text/css">
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
</style>
  </head>
  
  <body>
    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="brand" href="#">Minecraft Stats</a>
          <div class="navbar-content">
            <ul class="nav  pull-right"></ul>
          </div>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="page-header">
        <h1>Minecraft Server Statistics</h1>
      </div>
      <div class="row">
        <div class="span12">
          <div class="well">
            <h4>Server Users</h4>
            <div>
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
        <div class="well hidden"> 
        <!--
You can put a Teamspeak viewer, or another monitoring viewer here!
Make sure to delete the hidden part if you are
-->
</div>
      </div>
    </div>
  </div>
    <script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.2.2/js/bootstrap.min.js"></script>
  </body>
</html>
