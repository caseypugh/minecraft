<?

// Replace with your own path
$path = "/Users/caseypugh";
$users = explode("\n", shell_exec("ls {$path}/Dropbox/Minecraft\ Screenshots/ | grep ''"));

for ($i = 0; $i < count($users); $i++)
{
  if (preg_match("/Icon/i", $users[$i]) || trim($users[$i]) == "")
    continue;

  echo $users[$i] . " \n";

  if (!file_exists("{$path}/Dropbox/Public/Minecraft/Screenshots/" . $users[$i]))
    shell_exec("mkdir {$path}/Dropbox/Public/Minecraft/Screenshots/" . $users[$i]);

  $shots = explode("\n", shell_exec("ls {$path}/Dropbox/Minecraft\ Screenshots/{$users[$i]} | grep ''"));

  for ($j = 0; $j < count($shots); $j++) {

    if (trim($shots[$j]) == "")
      continue;

    echo $shots[$j] . "\n";

    if ($j == (count($shots) - 2))
      shell_exec("cp {$path}/Dropbox/Minecraft\ Screenshots/{$users[$i]}/{$shots[$j]} {$path}/Dropbox/Public/Minecraft/Screenshots/{$users[$i]}/latest.png");

    shell_exec("cp {$path}/Dropbox/Minecraft\ Screenshots/{$users[$i]}/{$shots[$j]} {$path}/Dropbox/Public/Minecraft/Screenshots/{$users[$i]}/{$shots[$j]}");
  }

}
