<?php 

$dir = "./";
$scandir = scandir($dir);

function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

foreach ($scandir as $key => $value)
{
  if (!in_array($value,array(".",".."))) //filter . and  .. directory on linux-systems
  {
     if (is_dir($dir . DIRECTORY_SEPARATOR . $value))
     {
         foreach (glob($dir . DIRECTORY_SEPARATOR . $value . "/*.php") as $filename) {
           $files[] = $filename;
        //    $files[] = $value . DIRECTORY_SEPARATOR . $filename;

        //    $files[] = $value . DIRECTORY_SEPARATOR;
        }
     }else{
        $files[] = $dir.DIRECTORY_SEPARATOR . $value;
        // echo "\n";
     }
  }
} 

// var_dump($files);

$querySqlArr=[];
foreach($files as $f){
    // echo $f;
    // die;
    // echo "\n";
    // echo get_string_between(file_get_contents(str_replace('.//','./',$f)),'$sql = "','";');
    $variableName = str_replace(".php","",str_replace(".//","",$f));
    $query = "$".$variableName."=\"".get_string_between(file_get_contents(str_replace('.//','./',$f)),'$sql = "','";')."\";";
    if(strlen($query)>30){
        $querySqlArr[]=$query;
    }
}

$newString = implode(PHP_EOL, $querySqlArr);
file_put_contents('./geojson.sql', $newString);