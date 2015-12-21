<?php
require_once(dirname(__FILE__) . '/../../../../config.php');
global $USER, $CFG, $DB;
require_once($CFG->dirroot . '/blocks/webgd_community/lib/class/dao/WebgdCommunityDao.php');
require_once($CFG->dirroot . '/blocks/webgd_community/commons/TableResouces.php');
require_login(1);
$idGlossario = optional_param('idGlossario', 0, PARAM_INT);
$voto = optional_param('votacao', 0, PARAM_INT);

$webgdCommunityDao = new WebgdCommunityDao();

$votoAnterior = 0;

if ($glossaryvotacao = $webgdCommunityDao->searchGlossaryUserVotation($idGlossario, $USER->id)) {
    $votoAnterior = $glossaryvotacao->voto;
    $DB->delete_records(TableResouces::$TABLE_PAGE_COMMUNITY_GLOSSARRY_VOTACAO, array('id' => $glossaryvotacao->id));
}

$glossary_user_votation = new stdClass();
$glossary_user_votation->userid = $USER->id;
$glossary_user_votation->glossarryid = $idGlossario;
$glossary_user_votation->voto = $voto;

$DB->insert_record(TableResouces::$TABLE_PAGE_COMMUNITY_GLOSSARRY_VOTACAO, $glossary_user_votation);

$glossary = $webgdCommunityDao->searchGlossaryById($idGlossario);

if ($votoAnterior != 0) {
    $glossary->votos = $glossary->votos - $votoAnterior + $voto;
    $votos = $glossary->votos;
    $total = $glossary->totalvotos;
} else {
    $glossary->totalVotos = $glossary->totalvotos + 1;
    $total = $glossary->totalVotos;
    $glossary->votos = $glossary->votos + $voto;
    $votos = $glossary->votos;
}

$DB->update_record(TableResouces::$TABLE_PAGE_COMMUNITY_GLOSSARY, $glossary);

$nivel = ($votos / $total) / 10;

echo $nivel;
?>
