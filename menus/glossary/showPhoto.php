<?php

require_once(dirname(__FILE__) . '/../../../../config.php');
require_once($CFG->dirroot . '/blocks/webgd_community/lib/class/dao/WebgdCommunityDao.php');

require_login(1);

$idCommunity = optional_param('community', 0, PARAM_INTEGER);
$idGlossario = optional_param('glossary', 0, PARAM_INTEGER);

$webgdDao = new WebgdCommunityDao();

$glossary = $webgdDao->searchGlossaryByCommunityById($idCommunity, $idGlossario);
$fileBd = $glossary->image;
if ($fileBd) {
    $file = $fileBd;
    $aux = explode('.', $file);
    $extensao = $aux[sizeof($aux) - 1];
    header('Content-Type: image/' . $extensao);
    //header('Content-Disposition: attachment; filename="'.basename($file).'"');
    header('Content-Length: ' . filesize($file));
    readfile($file);
} else {
    echo get_string('arqNaoEnc','block_webgd_community');
}
