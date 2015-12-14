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

echo $OUTPUT->heading('Deletar Foto');

$idFile = optional_param('file', 0, PARAM_INTEGER);
$idCommunity = optional_param('community', 0, PARAM_INTEGER);

$webgdCommunityDao = new WebgdCommunityDao();

$msg = 'Foto não encontrada';


if($webgdCommunityDao->searchPhotoCommunityById($idCommunity, $idFile)){

	$file = $webgdCommunityDao->searchPhotoById($idFile);

	if($webgdCommunityDao->deletePhotoByCommunityByIdByuser($file->id, $USER->id, $file->post)){
		$msg = 'Foto excluida com sucesso';

		unlink($file->path);
	}

}

redirect("{$CFG->wwwroot}/blocks/webgd_community/view.php?community=$idCommunity", $msg, 10);

echo $OUTPUT->footer();
