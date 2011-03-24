<?
date_default_timezone_set('America/New_York');

$ch = curl_init("http://dl.dropbox.com/u/19353/Minecraft/server.log");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($ch);
curl_close($ch);

$logs = explode("\n", $output);
$users = array();

function getTimeAgo($datetime)
{
  if(trim($datetime) == "")
    return false;

  $datediff = strtotime(date("Y-m-d H:i:s")) - strtotime($datetime);

  $min =    round($datediff / 60);
  $hours =  round($datediff / (60 * 60));
  $days =   round($datediff / (60 * 60 * 24));
  $months = round($datediff / (60 * 60 * 24 * 31));
  $years =  round($datediff / (60 * 60 * 24 * 365));

//echo "$min $hours $days $months $years ";

  if($datediff < 60) { // seconds
    if($datediff == 0) return "just now";
    return "$datediff second".pluralizer($datediff > 1) . " ago";
  }
  else if($min < 60) {
    return "$min minute".pluralizer($min>1)." ago";
  }
  else if($hours < 24) {
    return "$hours hour".pluralizer($hours>1)." ago";
  }
  else if($days < 31) {
    return "$days day".pluralizer($days>1)." ago";
  }
  else if($months < 12) {
    return "$months month".pluralizer($months>1)." ago";
  }
  else {
    return "$years year".pluralizer($years>1)." ago";
  }

  return false;
}

function pluralizer($bln, $suffix='s')
{
  return $bln ? $suffix : '';
}

function server_quit()
{
  for ($i = 0; $i < count($GLOBALS['users']); $i++) {
    offline($GLOBALS['users'][$i]['name']);
  }
}

function online($name, $time)
{
  //echo $name . " came online\n";

  for ($i = 0; $i < count($GLOBALS['users']); $i++) {
    if ($GLOBALS['users'][$i]['name'] == $name) {
      $GLOBALS['users'][$i]['online'] = true;
      $GLOBALS['users'][$i]['time'] = $time;
      return true;
    }
  }

  add_user($name, true, $time);
}

function offline($name, $time = false)
{
  //echo $name . " went offline\n";

  for ($i = 0; $i < count($GLOBALS['users']); $i++) {
    if ($GLOBALS['users'][$i]['name'] == $name) {
      $GLOBALS['users'][$i]['online'] = false;
      if ($time) $GLOBALS['users'][$i]['time'] = $time;
      return true;
    }
  }

  add_user($name, false, $time);
}

function add_user($name, $state, $time)
{
  $GLOBALS['users'][] = array(
    'name' => $name,
    'online' => $state,
    'avatar' => "/minecraft_avatar.php?name={$name}", //'http://www.mcserverreview.com/avatars/minecraft_creeper_wallpaper_by_lynchmob10_09_1_-avatar.jpg'
    'time'  => $time
  );
}

function cmp($a, $b)
{
  if (date("YmdHis", strtotime($a['time'])) > date("YmdHis", strtotime($b['time'])))
    return -1;

  return 1;
}

function getusers()
{
  $tmp = $GLOBALS['users'];
  uasort($tmp, 'cmp');
  return $tmp;
}

// PARSE THE LOG

foreach ($logs as $l)
{
  if (preg_match("/([0-9-]+ [0-9:]+) \[INFO\] \<([a-zA-Z0-9-_]+)\> (.*)/i", $l, $m))
    online($m[2], $m[1]);
  else if (preg_match("/([0-9-]+ [0-9:]+) \[INFO\] ([a-zA-Z0-9-_]+) \[.*logged in with entity/i", $l, $m))
    online($m[2], $m[1]);
  else if (preg_match("/([0-9-]+ [0-9:]+) \[INFO\] ([a-zA-Z0-9-_]+) lost connection/i", $l, $m))
    offline($m[2], $m[1]);
  else if (preg_match("/Stopping server/i", $l, $m))
    server_quit();
}

$users = getusers();

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
      .users .user .info h1 { font: 46px Helvetica, Arial; color: #333; }
      .users .user .info span { color: #999; font: normal 14px Helvetica, Arial; }
      .users .user .info span.on { color: #7ed471; }


      .users .offline .info h1 { color: #666; }
      .users .offline { background: #eee;  }
      .clear { clear: both; }

    </style>
  </head>
  <body>
    <div class="server">
      cpu.mypets.ws
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
          <div class="clear"></div>
        </div>
      <? endforeach ?>

    </div>
  </body>
</html>