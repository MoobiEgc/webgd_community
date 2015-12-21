<?php

require_once(dirname(__FILE__) . '/../../../../config.php');
require_once($CFG->dirroot . '/blocks/webgd_community/lib/class/dao/WebgdCommunityDao.php');

require_login(1);

$idFile = optional_param('file', 0, PARAM_INTEGER);

$webgdDao = new WebgdCommunityDao();

$fileBd = $webgdDao->searchPhotoById($idFile);

if ($fileBd) {
    $file = $fileBd->path;
    $aux = explode('.', $file);
    $extensao = $aux[sizeof($aux) - 1];
    header('Content-Type: image/' . $extensao);
    //header('Content-Disposition: attachment; filename="'.basename($file).'"');
    header('Content-Length: ' . filesize($file));
    readfile($file);
} else {
    echo 'Arquivo nao encontrado';
}
