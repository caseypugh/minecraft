<?PHP

/**
* Minecraft server script
*
* This class can take a Minecraft server log and parse it for data such as 
* online users, chat logs, total time logged in and more!
*
* @category   Minecraft
* @package    Minecraft_Server_Script
* @subpackage Minecraft_Class
* @copyright  Copyright (c) Jaryth Frenette 2012, hfuller 2011, caseypugh, 2011
* @license    Open Source - Anyone can use, modify and redistribute as wanted
* @version    Release: 1.0
* @link       http://jaryth.net
*/

//Global settings:

//Set your time zone to make sure calculations are correct!
//Time zone should match whatever time your Minecraft server runs on.
//List of Timezones can be found at: http://php.net/manual/en/timezones.php
date_default_timezone_set('America/Winnipeg');

class minecraft{

  //User Settings:
  //Change the following options to reflect how you want the class to work
  
  //Enable or disable log caching. true = enabled, false = disabled
  //Note: Disabling does not delete an existing cache if one exists
  //Default: true
  var $cacheEnable = true;
  
  //Enable or disable avatar caching. true = enabled, false = disabled
  //Note: Disabling does not delete an existing cache if one exists
  //Default: true
  var $cacheAvatar = true;  
  
  //Set the name of the cache folder. Ignored if caching is disabled above.
  //Note: Changing the cache folder does not delete the old one if it exists
  //Note: You will need to also change this setting in the avatar.php file 
  //Default 'cache'
  var $cacheFolder = 'cache';
  
  //Set the timeout limit for cache data
  //Default '60' (60 seconds = 1 minute, 300 = 5 minutes, 3600 = 1 hour)
  var $cacheTime = '60';
  
  
  //Set up initial variables
  var $users = array();
  var $chat = array();
  var $log = '';
  var $cache = '';
  

//<-- Class Constructor :: Saves log location, checks for cache and generation  //Constructor
function minecraft($logLocation){
  //Verify the log file exists
  if(is_file($logLocation)){
    //Save the location and continue
    $this->log = $logLocation;
  }else{
    //Rerun false and cancel the rest of the class if it does not.
    return false;
  }   
  
  //Check if the Cache is enabled 
  if($this->cacheEnable){
    //Check cache status (load and generate call are in this function)
    $this->cache();
  }else{
    //Or generate content
    $this->parseLog();
  }
  
}
//--> End of construct()

//<-- parseLog :: Parses though the server log. This is the primary function
function parseLog(){
  //set up the log
  $file = file_get_contents($this->log);
  $logs = explode("\n", $file);
  
  //Parse though the log for all of the information we need
  foreach ($logs as $l){
    //Check for users chatting, set them online, log their chat
    if (preg_match("/([0-9-]+ [0-9:]+) \[INFO\] \<([a-zA-Z0-9-_]+)\> (.*)/i", $l, $m))
      $this->online($m[2], $m[1], 0, $m[3]);
    //check for users entering the server, set them online
    else if (preg_match("/([0-9-]+ [0-9:]+) \[INFO\] ([a-zA-Z0-9-_]+) \[.*logged in with entity/i", $l, $m))
      $this->online($m[2], $m[1], 1);
    //Check for users leaving, set them as offline
    else if (preg_match("/([0-9-]+ [0-9:]+) \[INFO\] ([a-zA-Z0-9-_]+) lost connection/i", $l, $m))
      $this->offline($m[2], $m[1]);
    //Check if server shut down, log off all users
    else if (preg_match("/([0-9-]+ [0-9:]+) \[INFO\] Stopping server/i", $l, $m))
      $this->server_quit($m[1]); 
  }
  
  //Finally we sort the users
  $this->sortUsers();
  
  //Save the cache data if its enabled
  if($this->cacheEnable){
   $this->saveCache();
  }
  
}
//--> End  of parseLog


//  --  User Stuff :: Functions dedicated to user related tasks --              //User Stuff


//<-- add_user :: Adds a user to the array, sets default settings
function add_user($name, $state, $time){
  //If Avatar Caching is enabled, set cache name
  if($this->cacheAvatar){
   $avatar = "/avatar.php?name={$name}&size=40&cache=1";                   
  }else{
   $avatar = "/avatar.php?name={$name}&size=40";
  }
  
  //Enter user data into array
  $this->users[$name] = array(
    'name' => $name,
    'online' => $state,
    'logcount' => 1,
    'avatar' => $avatar,                        
    'time'  => $time,
    'lastonline' => $time,
    'totaltime' => 0    
  );
}
//--> End of add_user()

//<-- online :: Sets a user to 'online' and saves the time. and their chat log
function online($name, $time, $log=0, $chat=false){
  //This creates a chat log, just adds the string into the array
  if($chat){
   $this->chat[] =  $name . " said: " . $chat . "<br>\n";
  }  

  //Check to see if the user exists yet, and changes their status if they do
  if(array_key_exists($name, $this->users)){
    if($log == 1){
     //Increase total logon count, and set last log time.
     $this->users[$name]['logcount']++;
     $this->users[$name]['lastonline'] = $time;              
    }          
    
    //set user to online and set the time they where last seen
    $this->users[$name]['online'] = true;
    $this->users[$name]['time'] = $time;
    return true;  
  }

  //if a user does not exist, add them to the users
  $this->add_user($name, true, $time);
}
//--> End of online()

//<-- offline :: Sets a user to 'offline' and calculates session time
function offline($name, $time = false, $shutDownTime = false){
  //Check to see if the user exists yet, and changes their status if they do
  if(array_key_exists($name, $this->users)){  
    //Set user to 'offline'
    $this->users[$name]['online'] = false;
    
    //If the time flag was set:
    if($time){
      //set the time they went offline
     $this->users[$name]['time'] = $time;  
     
     //calculate session time and add it to their total.
     if($this->users[$name]['lastonline'] > 0){
     $this->users[$name]['totaltime'] += strtotime($time) - strtotime($this->users[$name]['lastonline']);
     $this->users[$name]['lastonline'] = 0;
     }
    }
    
     //calculate session time and add it to their total.
    if($shutDownTime){
      if($this->users[$name]['lastonline'] > 0){
        $this->users[$name]['totaltime'] += strtotime($shutDownTime) - strtotime($this->users[$name]['lastonline']);
        $this->users[$name]['lastonline'] = 0;
      }  
    }
    
    return true;
  }
  
  //if a user does not exist, add them to the users
  $this->add_user($name, false, $time);                                        
}
//--> End of offline()

//<-- server_quit :: Logs off all users when the server shuts down.
function server_quit($time){
  //Loop though all users and change them all to offline   
  foreach($this->users as $user){
   $this->offline($user['name'], false, $time);
  }
}
//--> End of server_quit()

//<-- sortUsers :: Gets the user list and sorts it
function sortUsers(){
  uasort($this->users, array($this,"cmp"));
  
  //if 'total' is set, sort by total time spent on server instead of default
  if(isset($_GET['total'])){
   uasort($this->users, array($this,"cmpTime"));
  }
} 
//--> End of sortUsers()


//  --  Cache Manipulation :: Functions dedicated to cache related tasks --     //Cache Manipulation

//<-- cache :: Checks if cache exists, and how old it is
function cache(){
  //Create the cache folder if need
  if(!is_dir($this->cacheFolder)){
    mkdir($this->cacheFolder);
  }
  
  //This sets the cache location
  $this->cache = $this->cacheFolder . DIRECTORY_SEPARATOR . md5($this->log) . '.cache';
  
  //Verify the cache file exists
  if(is_file($this->cache)){
    //If it does exist, read the file off disk
    $cache = file($this->cache);
    //Set the time difference to see how old the cache is
    $timeDiffrence = mktime() - $cache[0];
    
    //Check to see how old the cache is
    if($timeDiffrence < $this->cacheTime){
      //If its less than cacheTime then load the data into the users array  
      $this->users = unserialize($cache[1]);
    }else{
      //If its too old, generate fresh one
      $this->parseLog();
    }
    
  //If file does not exist, create it
  }else{
   $this->parseLog();
  }   
}
//--> End of cache

//<-- saveCache :: This function serializes the cache data and saves it to disk
function saveCache(){
  //Set cache generation time for tracking later
  $cache = mktime() . "\n" . serialize($this->users); 
  
  //Create the file and write the data
  $cacheFile = fopen($this->cache, 'w');
  fwrite($cacheFile, $cache);
  fclose($cacheFile);   
}
//--> End of saveCache


//  --  Time Manipulation :: Functions dedicated to time related tasks --       //Time Manipulation


//<-- getTimeAgo :: Calculates how much time has passed
function getTimeAgo($datetime, $skip = 0){
  //make sure time is not empty
  if(trim($datetime) == ""){
    return false;
  }
  
  //Sets the time difference. Make sure your time zone is correct!             
  $datediff = strtotime('now') - strtotime($datetime);

  //if Skip is set, will calculate time without timezone.
  if($skip == 1){
    $datediff = $datetime;
  }

  //Break down the different times
  $min =    round($datediff / 60);
  $hours =  round($datediff / (60 * 60));
  $days =   round($datediff / (60 * 60 * 24));
  $months = round($datediff / (60 * 60 * 24 * 31));
  $years =  round($datediff / (60 * 60 * 24 * 365));

  //we don't want to say "ago" so we can use this for online also
  if($datediff < 60){ // seconds
    if($datediff == 0) return "just now";
    return "$datediff second".$this->pluralizer($datediff > 1);// . " ago";
  }
  else if($min < 60){
    return "$min minute".$this->pluralizer($min>1);//." ago";
  }
  else if($hours < 24){
    return "$hours hour".$this->pluralizer($hours>1);//." ago";
  }
  else if($days < 31){
    return "$days day".$this->pluralizer($days>1);//." ago";
  }
  else if($months < 12){
    return "$months month".$this->pluralizer($months>1);//." ago";
  }
  else {
    return "$years year".$this->pluralizer($years>1);//." ago";
  }

  return false;
}
//--> End of gatTimeAgo()

//<-- pluralizer :: Will add 's to the ends of numbers not ending in 1.
function pluralizer($bln, $suffix='s'){
  return $bln ? $suffix : '';
} 
//--> End of  pluralizer()

//<-- Sec2Time :: Turns Seconds to Year, Day, Hours, Minutes, Seconds format.
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
//--> End of Sec2Time()


//  --  Misc Functions :: Functions for doing random other tasks --             //Misc Functions


//<-- cmp :: Primary sorting function, orders by time and name
function cmp($a, $b){ 
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
//--> End of cmp()

//<-- cmpTime :: Secondary sorting function, only sorts by total time online
function cmpTime($a, $b){
  return $b['totaltime'] - $a['totaltime'];
}
//->> End of cmpTime()

}
//--> End minecraft class
?>