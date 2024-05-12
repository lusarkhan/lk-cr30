<?php
$PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;
    
    $PNG_WEB_DIR = '../personal/temp/';
	
function deletfile($directory,$filename)
{
  $dir = opendir($directory);
  
while(($file = readdir($dir)))
{
  if((is_file("$directory/$file")) && ("$directory/$file" == "$directory/$filename"))
  {
    unlink("$directory/$file");
                  
    if(!file_exists($directory."/".$filename)) return $s = TRUE;
  }
}
  closedir($dir);
}
 deletfile($PNG_TEMP_DIR,$_SESSION['LOGIN'].'_'.$_SESSION['LS'].'.png');
?>
