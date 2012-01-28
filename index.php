<?PHP
//include file and start session
include("logger.php");
session_start();

//initialize the users
$users = getusers();

//if chat flag is set, show chat
if(isset($_GET['chat'])){

//only show the newest 30 lines of the chat log
$total = count($GLOBALS['chat']);
$start = count($GLOBALS['chat']) - 30;

for ($i = $start; $i < $total; $i++) {
    echo $GLOBALS['chat'][$i];   
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
<link rel="stylesheet" type="text/css" href="/tsstatus/tsstatus.css" />
<script type="text/javascript" src="/tsstatus/tsstatus.js"></script>
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
<?PHP foreach ($users as $u): ?>
  <div class="user <?PHP $u['online'] ? 'online' : 'offline' ?>">
    <img src="<?PHP echo($u['avatar']); ?>" />
    <div class="info">
      <h1><?PHP echo($u['name']) ?></h1>
      <?PHP if (!$u['online']): ?>
        <span>Offline. Last seen <?PHP echo(getTimeAgo($u['time'])) ?> ago.</span>
      <?PHP else: ?>
        <span class="on">Online! Logged on for <?PHP echo(getTimeAgo($u['time'])) ?>.</span>
      <?PHP endif ?>
      <br>
      Number of times logged on: <?PHP echo($u['logcount']) ?><br>
      Total time online: <?PHP echo(Sec2Time($u['totaltime'])) ?> 
    </div>

    <div class="clear"></div>
  </div>
<?PHP endforeach;?>
</div>
 </div>
</div>
<div id="navigation">
<h3>In TeamSpeak:</h3>

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