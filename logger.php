<?PHP

//Settings

//Point this to your minecraft log
$myFile = "E:\Program Files (x86)\Minecraft\server.log";

//set up the log
$output = file_get_contents($myFile);
$logs = explode("\n", $output);
$users = array();

//Functions

//Functions are split into two sections, Time Manipulation and User. 


//Time Manipulation     

//This function calculates how much time has passed
function getTimeAgo($datetime, $skip = 0)
{
  //make sure time is not empty
  if(trim($datetime) == "")
    return false;

    //the -6 hours is for Central Time Zone
  $datediff = strtotime("-6 hours") - strtotime($datetime);

  //if Skip is set, will calculate time without timezone.
  if($skip == 1){
    $datediff = $datetime;
  }

  $min =    round($datediff / 60);
  $hours =  round($datediff / (60 * 60));
  $days =   round($datediff / (60 * 60 * 24));
  $months = round($datediff / (60 * 60 * 24 * 31));
  $years =  round($datediff / (60 * 60 * 24 * 365));

  //we don't want to say "ago" so we can use this for online also
  if($datediff < 60) { // seconds
    if($datediff == 0) return "just now";
    return "$datediff second".pluralizer($datediff > 1);// . " ago";
  }
  else if($min < 60) {
    return "$min minute".pluralizer($min>1);//." ago";
  }
  else if($hours < 24) {
    return "$hours hour".pluralizer($hours>1);//." ago";
  }
  else if($days < 31) {
    return "$days day".pluralizer($days>1);//." ago";
  }
  else if($months < 12) {
    return "$months month".pluralizer($months>1);//." ago";
  }
  else {
    return "$years year".pluralizer($years>1);//." ago";
  }

  return false;
}

//this function will add an s to the ends of numbers not ending in 1.
function pluralizer($bln, $suffix='s')
{
  return $bln ? $suffix : '';
}

//This function turns Seconds into an Year, Day, Hours, Minutes, Seconds format.
function Sec2Time($time){
  if(is_numeric($time)){
    $value = array(
      "years" => 0, "days" => 0, "hours" => 0,
      "minutes" => 0, "seconds" => 0,
    );
    $string = "";
    if($time >= 31556926){
      $value["years"] = floor($time/31556926);
      $string .= $value["years"] . " Years, ";
      $time = ($time%31556926);
    }
    if($time >= 86400){
      $value["days"] = floor($time/86400);
      $string .= $value["days"] . " days, ";
      $time = ($time%86400);
    }
    if($time >= 3600){
      $value["hours"] = floor($time/3600);
      $string .= $value["hours"] . " hours, ";
      $time = ($time%3600);
    }
    if($time >= 60){
      $value["minutes"] = floor($time/60);
      $string .= $value["minutes"] . " minutes, ";
      $time = ($time%60);
    }
    $value["seconds"] = floor($time);  
    $string .= $value["seconds"] . " seconds "; 
  
    return $string;
  }else{
    return FALSE;
  }
}


//user stuff

//When the server is shut down, this function logs off all users
function server_quit($time)
{   
  for ($i = 0; $i < count($GLOBALS['users']); $i++) {
    offline($GLOBALS['users'][$i]['name'], false, $time); 
  }
}

//This function will set a users status to online, and save their last log time
function online($name, $time, $log=0, $chat=false)
{
  //This creates a chat log
  if($chat){
   $GLOBALS['chat'][] =  $name . " said: " . $chat . "<br>\n";
  }  

  //Cycle though all users
  for ($i = 0; $i < count($GLOBALS['users']); $i++) {  
    if ($GLOBALS['users'][$i]['name'] == $name) {
      if($log == 1){
       //Increase total logon count, and set last log time.
       $GLOBALS['users'][$i]['logcount']++;
       $GLOBALS['users'][$i]['lastonline'] = $time;              
      }          
      
      //set user to online and set the time they where last seen
      $GLOBALS['users'][$i]['online'] = true;
      $GLOBALS['users'][$i]['time'] = $time;
      return true;
    }
  }

  //if a user does not exist, add him to the users
  add_user($name, true, $time);
}

//sets a user to offline and calculates their session time and adds it to total.
function offline($name, $time = false, $shutDownTime = false)
{
  //Cycle though all users
  for ($i = 0; $i < count($GLOBALS['users']); $i++) {
    if ($GLOBALS['users'][$i]['name'] == $name) {
      //set user to offline
      $GLOBALS['users'][$i]['online'] = false;
      
      if($time){
        //set the time they went offline
       $GLOBALS['users'][$i]['time'] = $time;  
       
       //calculate session time and add it to their total.
       if($GLOBALS['users'][$i]['lastonline'] > 0){
       $GLOBALS['users'][$i]['totaltime'] += strtotime($time) - strtotime($GLOBALS['users'][$i]['lastonline']);
       $GLOBALS['users'][$i]['lastonline'] = 0;
       }
      }
      
       //calculate session time and add it to their total.
      if($shutDownTime){
        if($GLOBALS['users'][$i]['lastonline'] > 0){
        $GLOBALS['users'][$i]['totaltime'] += strtotime($shutDownTime) - strtotime($GLOBALS['users'][$i]['lastonline']);
        $GLOBALS['users'][$i]['lastonline'] = 0;
       }        
      }       
       
      return true;
    }
  }

  //if a user does not exist, add him to the users
  add_user($name, false, $time);
}


//Adds a user to the array with default settings.
function add_user($name, $state, $time)
{
  $GLOBALS['users'][] = array(
    'name' => $name,
    'online' => $state,
    'logcount' => 1,
    'avatar' => "/avatar.php?name={$name}&size=40", 
    'time'  => $time,
    'lastonline' => $time,
    'totaltime' => 0
    
  );
}

//Sort Function
function cmp($a, $b)
{
 
  if ( $a['online'] ) {
	if ( $b['online'] ) { //both online - alphabetically
		return strtotime($a['time']) - strtotime($b['time']);
	} else { // only a is online - it comes first
		return -1;
	}
  } else if ( $b['online'] ) { //only b is online - comes first
	return 1;
  } else {// both offline - the one that was most recently on comes first
	return strtotime($b['time']) - strtotime($a['time']);
  }
  
}

//Secondary sort function for longest total online time.
function cmpTime($a, $b){
  return $b['totaltime'] - $a['totaltime'];
}


//gets the users and sorts them
function getusers()
{
  $tmp = $GLOBALS['users'];
  uasort($tmp, 'cmp');
  
  //if 'total' is set, sort by total time spent on server instead of default
  if(isset($_GET['total'])){
   uasort($tmp, 'cmpTime');
  }
  
  return $tmp;
}

//end FUNCTIONS - code excecation starts here:



// PARSE THE LOG
foreach ($logs as $l)
{
  //Check for users chatting, set them online, log their chat
  if (preg_match("/([0-9-]+ [0-9:]+) \[INFO\] \<([a-zA-Z0-9-_]+)\> (.*)/i", $l, $m))
    online($m[2], $m[1], 0, $m[3]);
  //check for users entering the server, set them online
  else if (preg_match("/([0-9-]+ [0-9:]+) \[INFO\] ([a-zA-Z0-9-_]+) \[.*logged in with entity/i", $l, $m))
    online($m[2], $m[1], 1);
  //Check for users leaving, set them as offline
  else if (preg_match("/([0-9-]+ [0-9:]+) \[INFO\] ([a-zA-Z0-9-_]+) lost connection/i", $l, $m))
    offline($m[2], $m[1]);
  //Check if server shut down, log off all users
  else if (preg_match("/([0-9-]+ [0-9:]+) \[INFO\] Stopping server/i", $l, $m))
    server_quit($m[1]);
  
}


?>