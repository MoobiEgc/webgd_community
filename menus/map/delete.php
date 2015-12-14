<?php
require_once(dirname(__FILE__) . '/../../../../config.php');
require_once($CFG->dirroot.'/blocks/webgd_community/commons/TableResouces.php');
require_once($CFG->dirroot.'/blocks/webgd_community/lib/class/dao/WebgdCommunityDao.php');

global $USER;

require_login(1);

$PAGE->set_url('/course/index.php');

$PAGE->set_context(context_system::instance());

$PAGE->set_pagelayout('standard');

echo $OUTPUT->header('themeselector');

echo $OUTPUT->heading('Deletar Mapa mental');

$idQuestion = optional_param('map', 0, PARAM_INTEGER);
$idCommunity = optional_param('community', 0, PARAM_INTEGER);

$webgdCommunityDao = new WebgdCommunityDao();

$msg = 'Mapa mental não Encontrado';

if($webgdCommunityDao->searchMentalMapByCommunityByIdByUser($idCommunity, $idQuestion, $USER->id)){
	$map = $webgdCommunityDao->searchMentalMapByCommunityById($idQuestion);
	$msg = 'Erro ao excluir mapa mental';
	if($webgdCommunityDao->deleteMentalMapByCommunityByIdByuser($map->id, $USER->id, $map->post)){
		$msg = 'Mapa mental deletado com sucesso';
	}
}

redirect("{$CFG->wwwroot}/blocks/webgd_community/view.php?community=$idCommunity&suboption=1", $msg, 10);

echo $OUTPUT->footer();
