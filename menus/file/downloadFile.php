<?php

require_once(dirname(__FILE__) . '/../../../../config.php');
require_once($CFG->dirroot . '/blocks/webgd_community/lib/class/dao/WebgdCommunityDao.php');

require_login(1);

$idFile = optional_param('file', 0, PARAM_INTEGER);

$webgdDao = new WebgdCommunityDao();

$fileBd = $webgdDao->searchFileById($idFile);

if ($fileBd) {
    $file = $fileBd->path;
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($file) . '"');
    header('Content-Length: ' . filesize($file));
    readfile($file);
} else {
    echo 'arquivo nao encontrado';
}
