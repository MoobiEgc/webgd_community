<?php
require_once(dirname(__FILE__) . '/../../../../config.php');
require_once($CFG->dirroot.'/blocks/webgd_community/lib/class/dao/WebgdCommunityDao.php');

require_login(1);

$idCommunity = optional_param('community', 0, PARAM_INTEGER);
$idGlossario = optional_param('glossary', 0, PARAM_INTEGER);
$q = optional_param('q', 0, PARAM_INTEGER);
$webgdCommunityDao = new WebgdCommunityDao();

$glossary = $webgdCommunityDao->searchGlossaryByCommunityById($idCommunity, $idGlossario);

  switch ($q) {

    case 'termo':
    $path = $glossary->videotermo;
      break;
    case 'conceito':
    $path = $glossary->videoconceito;
      break;
    case 'exemplo':
    $path = $glossary->videoexemplo;
      break;
  }


$aux = explode('.',$path);
$extensao = $aux[sizeof($aux)-1];

$size=filesize($path);

$fm=@fopen($path,'rb');
if(!$fm) {
  // You can also redirect here
  header ("HTTP/1.0 404 Not Found");
  die();
}

$begin=0;
$end=$size;

if(isset($_SERVER['HTTP_RANGE'])) {
  if(preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches)) {
    $begin=intval($matches[0]);
    if(!empty($matches[1])) {
      $end=intval($matches[1]);
    }
  }
}

if($begin>0||$end<$size)
  header('HTTP/1.0 206 Partial Content');
else
  header('HTTP/1.0 200 OK');

header("Content-Type: video/".$extensao);
header('Accept-Ranges: bytes');
header('Content-Length:'.($end-$begin));
header("Content-Disposition: inline;");
header("Content-Range: bytes $begin-$end/$size");
header("Content-Transfer-Encoding: binary\n");
header('Connection: close');

$cur=$begin;
fseek($fm,$begin,0);

while(!feof($fm)&&$cur<$end&&(connection_status()==0))
{ print fread($fm,min(1024*16,$end-$cur));
  $cur+=1024*16;
	usleep(1000);
}
die();
