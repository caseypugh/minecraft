<?

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