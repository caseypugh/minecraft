<?
date_default_timezone_set('America/New_York');

include("logger.php");

$users = getusers();
$dropbox_url = "http://dl.dropbox.com/u/19353";

?>
<html>
  <head>
    <title>Casey's Minecraft Server (cpu.mypets.ws)</title>
    <link rel="stylesheet" type="text/css" href="/assets/css/reset.css" media="screen" />
    <style>

    .server { font: bold 24px Helvetica, Arial; padding: 20px 10px; color: #7ed471; background: #333; border-bottom: 1px solid #fff; }
    .users .user { padding: 10px; clear: both; border-bottom: 1px solid #ddd; }
    .users .user img { float: left; }
    .users .user .info { margin-top: 15px; float: left; margin-left: 10px; }
    .users .user .ss { float: right; width: 160px; height: 100px; overflow: hidden; }
    .users .user .ss img { height: 100px; }
    .users .user .info h1 { font: 46px Helvetica, Arial; color: #333; }
    .users .user .info span { color: #999; font: normal 14px Helvetica, Arial; }
    .users .user .info span.on { color: #7ed471; }


    .users .offline .info h1 { color: #666; }
    .users .offline { background: #eee;  }
    .clear { clear: both; }

    </style>
    <meta http-equiv="refresh" content="60;url=http://caseypugh.com/minecraft" />
  </head>
  <body>
    <div class="server">
      cpu.mypets.ws <a href="/minecraft/map" target="_blank">map</a>
    </div>
    <div class="users">

      <? foreach ($users as $u): ?>
        <div class="user <?= $u['online'] ? 'online' : 'offline' ?>">
          <img src="<?= $u['avatar'] ?>" />
          <div class="info">
            <h1><?= $u['name'] ?></h1>
            <? if (!$u['online']): ?>
              <span>Offline. Last seen <?= getTimeAgo($u['time']) ?></span>
            <? else: ?>
              <span class="on">Online!</span>
            <? endif ?>
          </div>
          <div class="ss">
            <a href="<?= $dropbox_url ?>/Minecraft/Screenshots/<?= $u['name'] ?>/latest.png" target="_blank"><img src="<?= $dropbox_url ?>/Minecraft/Screenshots/<?= $u['name'] ?>/latest.png" onerror="this.style.display='none'" /></a>
          </div>
          <div class="clear"></div>
        </div>
      <? endforeach ?>

    </div>
  </body>
</html>